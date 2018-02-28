<?php
/**
 * Ms\Core\Entity\Db\Query\QueryDelete
 * Сущность DELETE запроса к базе данных
 *
 * @package MSergeev\Core
 * @subpackage Entity\Db\Query
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Exception;
use Ms\Core\Lib\DataManager;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib;

class QueryDelete extends QueryBase
{
	/**
	 * @var mixed
	 */
	private $deletePrimary = null;

	/**
	 * @var bool
	 */
	private $deleteConfirm = false;

	/**
	 * @var array
	 */
	private $arTableLinks = array();

	/**
	 * @var null
	 */
	private $massDeleteResult = null;

	/**
	 * Конструктор
	 *
	 * @param mixed $deletePrimary
	 * @param bool $deleteConfirm
	 * @param string $tableClass
	 * @since 0.2.0
	 */
	public function __construct ($deletePrimary=null,$deleteConfirm=null,$tableClass=null)
	{
		try
		{
			if (!is_null($deletePrimary))
			{
				$this->deletePrimary = $deletePrimary;
			}
			else
			{
				throw new Exception\ArgumentNullException('$deletePrimary');
			}
			if (!is_null($deleteConfirm) && $deleteConfirm)
			{
				$this->deteteConfirm = true;
			}
			/** @var DataManager $tableClass */
			if (!is_null($tableClass))
			{
				if (class_exists($tableClass))
				{
					$this->setTableName($tableClass::getTableName());
					$this->setTableMap($tableClass::getMapArray());
					$this->arTableLinks = $tableClass::getTableLinks();
				}
				else
				{
					throw new Exception\ArgumentTypeException('$tableClass','DataManager');
				}
			}
			else
			{
				throw new Exception\ArgumentNullException('$tableClass');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}
		catch (Exception\ArgumentTypeException $e2)
		{
			die($e2->showException());
		}

		$this->buildQuery();
	}

	/**
	 * Собирает SQL запрос из параметров
	 * @since 0.2.0
	 */
	protected function buildQuery ()
	{
		$helper = new SqlHelper($this->getTableName());
		$arMap = $this->getTableMap();
		$primaryId = $this->deletePrimary;

		/**
		 * @var string $field
		 * @var Fields\ScalarField $objData
		 */
		foreach ($arMap as $field=>$objData)
		{
			if ($objData->isPrimary())
			{
				$primaryField = $objData->getColumnName();
				$primaryObj = $objData;
				break;
			}
		}
		try
		{
			if (!isset($primaryField))
			{
				throw new Exception\ArgumentNullException('$primaryField');
			}
			if (!isset($primaryObj))
			{
				throw new Exception\ArgumentNullException('$primaryObj');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}
		$sql = "DELETE FROM ".$helper->wrapTableQuotes();
		$sql .= " WHERE ".$helper->wrapFieldQuotes($primaryField)." = ";
		$sql .= $primaryObj->getSqlValue($primaryId);
		$sql .= " LIMIT 1";
		//msEchoVar($sql);
		//msDebug($arTableLinks);
		if (empty($this->arTableLinks))
		{
			$this->setSql($sql);
		}
		elseif ($this->deleteConfirm)
		{
			$this->sqlMassDelete();
		}
		else
		{
			$bCanDelete = $this->checkCanDelete();

			if ($bCanDelete)
			{
				$this->setSql($sql);
			}
		}
	}

	/**
	 * Возвращает результат SQL запроса массового удаления данных
	 *
	 * @return null
	 * @since 0.2.0
	 */
	public function getMassDeleteResult ()
	{
		return $this->massDeleteResult;
	}

	//TODO: Протестировать
	/**
	 * Функция массового удаления записи и всех связанных с ней записей
	 * @since 0.2.0
	 */
	private function sqlMassDelete ()
	{
		$arMap = $this->getTableMap();
		$tableName = $this->getTableName();
		$helper = new SqlHelper($tableName);
		$massSql = '';

		/*
		 * Бежим по массиву вида
		 * вариант 1:
		 * array(
		 *      'поле_в_таблице' => array(
		 *          'другая_таблица' => 'ссылающееся_поле'
		 *      )
		 * )
		 * или
		 * вариант 2:
		 * array(
		 *      'поле_в_таблице' => array(
		 *          'другая_таблица' => array('ссылающеся_поле_1','ссылающееся_поле_2',...)
		 *      )
		 * )
		 */
		foreach ($this->arTableLinks as $field=>$arLinked)
		{
			foreach ($arLinked as $linkTable=>$linkField)
			{
				//Если исходный массив вариант 2
				if (is_array($linkField))
				{
					//для каждого 'ссылающееся_поле_n'
					foreach ($linkField as $linkF)
					{
						/** @var DataManager $tableClassName */
						$tableClassName = Lib\Tools::getClassNameByTableName($linkTable);
						$arRes = $tableClassName::getList(
							array(
								'select' => array('ID'),
								'filter' => array(
									$linkF => $this->deletePrimary
								)
							)
						);
						//Если в ссылающейся таблице есть записи с использованием 'поле_в_таблице', удаляем их
						if ($arRes)
						{
							foreach ($arRes as $delID)
							{
								$deleteQuery = new QueryDelete($delID['ID'],true,$tableClassName);
								if (strlen($deleteQuery->getSql())>5)
								{
									$massSql .= $deleteQuery->getSql().";\n";
								}
							}
						}
					}
				}
				else
				{
					/** @var DataManager $tableClassName */
					$tableClassName = Lib\Tools::getClassNameByTableName($linkTable);
					$arRes = $tableClassName::getList(
						array(
							'select' => array('ID'),
							'filter' => array(
								$linkField => $this->deletePrimary
							)
						)
					);
					//Если в ссылающейся таблице есть записи с использованием 'поле_в_таблице', удаляем их
					if ($arRes)
					{
						foreach ($arRes as $delID)
						{
							$deleteQuery = new QueryDelete($delID['ID'],true,$tableClassName);
							if (strlen($deleteQuery->getSql())>5)
							{
								$massSql .= $deleteQuery->getSql().";\n";
							}
						}
					}
				}
			}
		}
		//msEchoVar($massSql);

		/**
		 * @var string $field
		 * @var Fields\ScalarField $objData
		 */
		foreach ($arMap as $field=>$objData)
		{
			if ($objData->isPrimary())
			{
				$primaryField = $objData->getColumnName();
				$primaryObj = $objData;
				break;
			}
		}
		try
		{
			if (!isset($primaryField))
			{
				throw new Exception\ArgumentNullException('$primaryField');
			}
			if (!isset($primaryObj))
			{
				throw new Exception\ArgumentNullException('$primaryObj');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}
		//msDebug($primaryField);
		//msDebug($primaryObj);

		$sql = "DELETE FROM ".$helper->wrapTableQuotes();
		$sql .= " WHERE ".$helper->wrapFieldQuotes($primaryField)." = ";
		$sql .= $primaryObj->getSqlValue($this->deletePrimary);
		$sql .= " LIMIT 1;";

		//TODO: Случаются ошибки из-за лишних ; в запросе
		$massSql .= $sql;
		//msEchoVar($massSql);
		$delQuery = new QueryBase($massSql);
		$res = $delQuery->exec();

		$this->massDeleteResult = $res;
	}

	//TODO: Протестировать
	/**
	 * Функция проверки возможности удаления записи. Проверяет нет ли записей, связанных с удаляемой
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	private function checkCanDelete()
	{
		$bCanDelete = true;

		foreach ($this->arTableLinks as $field=>$arLinked)
		{
			foreach ($arLinked as $linkTable=>$linkField)
			{
				if (is_array($linkField))
				{
					foreach ($linkField as $linkF)
					{
						/** @var DataManager $tableClass */
						$tableClass = Lib\Tools::getClassNameByTableName($linkTable);
						$arRes = $tableClass::getList(
							array(
								'select' => array('ID'),
								'filter' => array(
									$linkF => $this->deletePrimary
								)
							)
						);
						if ($arRes)
						{
							$bCanDelete = false;
						}
					}
				}
				else
				{
					/** @var DataManager $tableClass */
					$tableClass = Lib\Tools::getClassNameByTableName($linkTable);
					$arRes = $tableClass::getList(
						array(
							'select' => array('ID'),
							'filter' => array(
								$linkField => $this->deletePrimary
							)
						)
					);
					if ($arRes)
					{
						$bCanDelete = false;
					}
				}
			}
		}

		return $bCanDelete;
	}
}
<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Exception;
use Ms\Core\Lib\DataManager;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib;

/**
 * Класс Ms\Core\Entity\Db\Query\QueryDelete
 * Сущность DELETE запроса к базе данных
 */
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
	 * @deprecated 1.0.0
	 */
	private $massDeleteResult = null;

	/**
	 * Конструктор
	 *
	 * @param mixed  $deletePrimary
	 *  param bool $deleteConfirm Устарел с 1.0.0
	 * @param string $tableClass
	 *
	 * @throws Exception\ArgumentNullException
	 * @throws Exception\ArgumentTypeException
	 */
	public function __construct ($deletePrimary=null,$tableClass=null)
	{
		if (!is_null($deletePrimary))
		{
			$this->deletePrimary = $deletePrimary;
		}
		else
		{
			throw new Exception\ArgumentNullException('$deletePrimary');
		}
		/** @var DataManager $tableClass */
		if (!is_null($tableClass))
		{
			if (class_exists($tableClass))
			{
				$this->setTableName($tableClass::getTableName());
				$this->setTableMap($tableClass::getMapArray());
//					$this->arTableLinks = $tableClass::getTableLinks();
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

		$this->buildQuery();
	}

	/**
	 * Собирает SQL запрос из параметров
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

		$this->setSql($sql);

		//<editor-fold defaultstate="collapse" desc="Код устарел с 1.0.0. Теперь все проверки ложаться на программистов и на foreign key">
		/*
		if (empty($this->arTableLinks))
		{
			$this->setSql($sql);
		}
		elseif ($this->deleteConfirm)
		{
//			$this->sqlMassDelete();
			$this->setSql($sql);
		}
		else
		{
			$bCanDelete = $this->checkCanDelete();

			if ($bCanDelete)
			{
				$this->setSql($sql);
			}

			$this->setSql($sql);
		}
		*/
		//</editor-fold>
	}

	/**
	 * Возвращает результат SQL запроса массового удаления данных
	 * @deprecated 1.0.0
	 * @return null
	 */
	public function getMassDeleteResult ()
	{
//		return $this->massDeleteResult;
		return null;
	}

	/**
	 * Функция массового удаления записи и всех связанных с ней записей
	 * @deprecated  1.0.0
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
								$deleteQuery = new QueryDelete($delID['ID'],$tableClassName);
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
							$deleteQuery = new QueryDelete($delID['ID'],$tableClassName);
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

	/**
	 * Функция проверки возможности удаления записи. Проверяет нет ли записей, связанных с удаляемой
	 *
	 * @return bool
	 * @deprecated 1.0.0
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
<?php
/**
 * Ms\Core\Entity\Db\Query\QueryUpdate
 * Сущность UPDATE запроса к базе данных
 *
 * @package Ms\Core
 * @subpackage Entity\Db\Query
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Lib\DataManager;
use Ms\Core\Exception;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Fields;

class QueryUpdate extends QueryBase
{
	/**
	 * @var mixed
	 */
	private $updatePrimary=null;

	/**
	 * @var array
	 */
	private $updateArray=null;

	/**
	 * Конструктор
	 *
	 * @param null $updatePrimary
	 * @param null $updateArray
	 * @param null $tableClass
	 *
	 * @throws Exception\ArgumentTypeException
	 * @since 0.2.0
	 */
	public function __construct ($updatePrimary=null,$updateArray=null,$tableClass=null)
	{
		$this->setType('update');
		try
		{
			if (!is_null($updatePrimary))
			{
				$this->updatePrimary = $updatePrimary;
			}
			if (is_null($updateArray))
			{
				throw new Exception\ArgumentNullException('$updateArray');
			}
			else
			{
				$this->updateArray = $updateArray;
			}
			if (is_null($tableClass))
			{
				throw new Exception\ArgumentNullException('$tableClass');
			}
			elseif (!class_exists($tableClass))
			{
				throw new Exception\ObjectNotFoundException('$tableClass');
			}
			else
			{
				/** @var DataManager $tableClass */
				$this->setTableName($tableClass::getTableName());
				$this->setTableMap($tableClass::getMapArray());
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}
		catch (Exception\ObjectNotFoundException $e2)
		{
			die($e2->showException());
		}

		$this->setSql($this->buildQuery());
	}

	/**
	 * Собирает SQL запрос из параметров
	 *
	 * @return string
	 * @since 0.2.0
	 */
	protected function buildQuery()
	{
		$helper = new SqlHelper($this->getTableName());
		$arMap = $this->getTableMap();
		$arUpdate = $this->updateArray;
		$primaryId = $this->updatePrimary;
		$primaryField = $primaryObj = null;
		$arDefaultUpdate = array();

		$sql = "UPDATE \n\t".$helper->wrapTableQuotes()."\nSET\n";
		/**
		 * @var string $field
		 * @var Fields\ScalarField $objData
		 */
		//Ищем PRIMARY ключ
		foreach ($arMap as $field=>$objData)
		{
			if ($objData->isPrimary())
			{
				//Сохраняем параметры PRIMARY ключа
				$primaryField = $objData->getColumnName();
				$primaryObj = $objData;
				try
				{
					//Если PRIMARY ключ не был задан явно, но присутствует в массиве - используем его
					if (is_null($primaryId) && intval($arUpdate[$primaryField]) > 0)
					{
						$primaryId = intval($arUpdate[$primaryField]);
					}
					//Иначе выводим исключение
					elseif (is_null($primaryId))
					{
						throw new Exception\ArgumentNullException('$updatePrimary');
					}
				}
				catch (Exception\ArgumentNullException $e)
				{
					die($e->showException());
				}
			}
			elseif (!is_null($objData->getDefaultValue('update')))
			{
				$arDefaultUpdate[$objData->getColumnName()] = $objData;
			}
		}
		$bFirst = true;
		foreach ($arUpdate as $field=>$value)
		{
			try
			{
				/** @var Fields\ScalarField[] $arMap */
				if (isset($arMap[$field]))
				{
					$columnName = $arMap[$field]->getColumnName();
					if (isset($arDefaultUpdate[$columnName]))
					{
						unset($arDefaultUpdate[$columnName]);
					}
					if ($bFirst)
					{
						$bFirst = false;
					}
					else
					{
						$sql .= ",\n";
					}
					$sql .= "\t".$helper->wrapQuotes($columnName)." = ";

					/** @var Fields\ScalarField $fieldClassName */
					$fieldClassName = $arMap[$field]->getClassName();
					//msDebug($value);
					//$value = $arMap[$field]->saveDataModification($value);
					$value = $fieldClassName::saveDataModification($value,$arMap[$field]);
					//msDebug($value);
					if (is_null($value))
					{
						$sql .= 'NULL';
					}
					else
					{
						$sql .= $arMap[$field]->getSqlValue($value);
					}
				}
				else
				{
					throw new Exception\ArgumentOutOfRangeException('arUpdate['.$field.']');
				}
			}
			catch (Exception\ArgumentOutOfRangeException $e_out)
			{
				die($e_out->showException());
			}
		}
		if (!empty($arDefaultUpdate))
		{
			foreach ($arDefaultUpdate as $columnName=>$objData)
			{
				$sql .= ",\n\t".$helper->wrapQuotes($columnName)." = ";
				if ($objData->isDefaultSql('update'))
				{
					$sql .= $objData->getDefaultValue('update');
				}
				else
				{
					$value = $objData->getDefaultValue('update');
					/** @var Fields\ScalarField $class */
					$class = $objData->getClassName();
					$value = $class::saveDataModification($value,$objData);
					$sql .= $objData->getSqlValue($value);
				}
			}
		}
		$sql .= "\nWHERE\n\t".$helper->wrapFieldQuotes($primaryField)." =";
		$sql .= $primaryObj->getSqlValue($primaryId);
		$sql .= "\nLIMIT 1 ;";

		return $sql;
	}
}
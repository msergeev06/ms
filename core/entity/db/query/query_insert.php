<?php
/**
 * Ms\Core\Entity\Db\Query\QueryInsert
 * Сущность INSERT запроса к базе данных
 *
 * @package MSergeev\Core
 * @subpackage Entity\Db\Query
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Exception;
use Ms\Core\Lib;
use Ms\Core\Entity\Db\Fields;

class QueryInsert extends QueryBase
{
	/**
	 * @var array
	 */
	private $arAdd = null;

	/**
	 * @var array
	 */
	private $arNames = null;

	/**
	 * @var array
	 */
	private $arValues = null;

	/**
	 * Заполняет все необходимые параметры для INSERT запроса
	 *
	 * @api
	 *
	 * @param array     $insertArray    Массив добавляемый полей => значений
	 * @param string    $tableClass     Класс таблицы
	 *
	 * @throws Exception\ArgumentNullException
	 * @throws Exception\ArgumentTypeException
	 * @since 0.2.0
	 */
	public function __construct (array $insertArray=null, $tableClass=null)
	{
		$this->setType('insert');

		try
		{
			if (!is_null($insertArray))
			{
				if (isset($insertArray[0]))
				{
					$this->setArray($insertArray);
				}
				else
				{
					$this->setArray(array($insertArray));
				}
			}
			else
			{
				throw new Exception\ArgumentNullException('$insertArray');
			}
			/** @var Lib\DataManager $tableClass */
			if (!is_null($tableClass))
			{
				if (class_exists($tableClass))
				{
					$this->setTableName($tableClass::getTableName());
					$this->setTableMap($tableClass::getMapArray());
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

		$this->setSql($this->buildQuery());
	}

	/**
	 * Собирает SQL запрос из параметров
	 *
	 * @return string|void
	 * @since 0.2.0
	 */
	protected function buildQuery()
	{
		$arAddValues = $this->getArray();
		$tableName = $this->getTableName();
		$helper = new SqlHelper($tableName);
		$arMapArray = $this->getTableMap();
		$sql = "";
		$bFFirst = true;

		//Бежим по добавляемым записям
		$sql .= "INSERT INTO ".$helper->wrapTableQuotes()." ";
		foreach ($arAddValues as $arValue)
		{
			$this->arNames = array();
			$this->arValues = array();
			/**
			 * @var string $field
			 * @var Fields\ScalarField $obMap
			 */
			foreach ($arMapArray as $field => $obMap)
			{
				/** @var string $columnName Название поля в базе данных */
				$columnName = $obMap->getColumnName();
				/** @var string $fieldName Название поля в API */
				$fieldName = $obMap->getName();

				//Если среди добавляемых значений есть значение для данного поля
				if (isset($arValue[$fieldName]))
				{
					$this->getSaveValue ($arValue,$obMap,$fieldName,$columnName);
				}
				//Если значения не установлено для данного поля
				else
				{
					$this->getEmptyValue ($arValue,$obMap,$fieldName,$columnName);
				}
			}
			$sql .= $this->getInsertSql($bFFirst);
		}
		$sql .= ';';

		return $sql;
	}

	/**
	 * Получает сохраненные значения
	 *
	 * @param                    $arValue
	 * @param Fields\ScalarField $obMap
	 * @param                    $fieldName
	 * @param                    $columnName
	 * @since 0.2.0
	 */
	private function getSaveValue (&$arValue,Fields\ScalarField &$obMap,$fieldName,$columnName)
	{
		$helper = new SqlHelper();
		//Получаем класс данного поля
		/** @var Fields\ScalarField $fieldClassName */
		$fieldClassName = $obMap->getClassName();
		//Получаем обработанное значение
		$arValue[$fieldName] = $fieldClassName::saveDataModification($arValue[$fieldName],$obMap);
		//Сохраняем значение и имя поля
		$this->arNames[] = $helper->wrapQuotes($columnName);
		$this->arValues[] = $obMap->getSqlValue($arValue[$fieldName]);
	}

	/**
	 * Получает пустые значения
	 *
	 * @param                    $arValue
	 * @param Fields\ScalarField $obMap
	 * @param                    $fieldName
	 * @param                    $columnName
	 * @since 0.2.0
	 */
	private function getEmptyValue (&$arValue,Fields\ScalarField &$obMap,$fieldName,$columnName)
	{
		$helper = new SqlHelper();
		//Если данное поле не является обязательным или стоит флаг автокомплита
		if (!$obMap->isRequired() || $obMap->isAutocomplete())
		{
			//Устанавливаем его в значение NULL
			$this->arNames[] = $helper->wrapQuotes($columnName);
			$this->arValues[] = 'NULL';
		}
		else
		{
			try
			{
				//Если значение может быть получено из функции
				if (!is_null($obMap->getRun()))
				{
					$this->getRunValue ($arValue,$obMap,$columnName);
				}
				//Если есть значение по-умолчанию
				elseif (!is_null($obMap->getDefaultValue('insert')))
				{
					$this->getDefaultValue ($obMap, $columnName);
				}
				else
				{
					throw new Exception\ArgumentNullException('field['.$fieldName.']');
				}
			}
			catch (Exception\ArgumentNullException $e)
			{
				die($e->showException());
			}
		}
	}

	/**
	 * @param                    $arValue
	 * @param Fields\ScalarField $obMap
	 * @param                    $columnName
	 * @since 0.2.0
	 */
	private function getRunValue (&$arValue,Fields\ScalarField &$obMap,$columnName)
	{
		$helper = new SqlHelper();
		try
		{
			$arRun = $obMap->getRun();
			//Если не задана функция, выбрасываем исключение
			if (!isset($arRun['function']))
			{
				throw new Exception\ArgumentNullException('$arRun["function"]');
			}
			//Если задана колонка, из которой берется значение
			if (isset($arRun['column']))
			{
				$bSetColumns = true;
				//Если задан массив колонок, проверяем заданы ли они все
				if (is_array($arRun['column']))
				{
					foreach ($arRun['column'] as $col)
					{
						if (!isset($arValue[$col]))
						{
							$bSetColumns = false;
							break;
						}
					}
				}
				//Если колонка только одна, проверяем задана ли она
				elseif (!isset($arValue[$arRun['column']]))
				{
					$bSetColumns = false;
				}
				//Если значение не всех колонок было установлено, выбрасываем исключение
				if (!$bSetColumns)
				{
					throw new Exception\ArgumentNullException('$arValue[$arRun["column"]]');
				}
				//Иначе
				else
				{
					//Если указанная функция существует и может быть выполнена
					if (is_callable($arRun['function']))
					{
						//Выполняем функцию, передав ей все заданные колонки
						$arParams = array("VALUES"=>$arValue,'COLUMNS'=>$arRun['column']);
						$res = call_user_func($arRun['function'],$arParams);
						//Результат работы функции записываем в значение колонки
						$this->arNames[] = $helper->wrapQuotes($columnName);
						$this->arValues[] = $obMap->getSqlValue($res);
					}
					//Иначе, выбрасываем исключение
					else
					{
						throw new \BadFunctionCallException(strval($arRun["function"]));
					}
				}
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}
		catch (\BadFunctionCallException $e2)
		{
			die($e2->getMessage().$e2->getTrace());
		}
	}

	/**
	 * Получает значения по-умолчанию
	 *
	 * @param Fields\ScalarField $obMap
	 * @param                    $columnName
	 * @since 0.2.0
	 */
	private function getDefaultValue (Fields\ScalarField &$obMap, $columnName)
	{
		$helper = new SqlHelper();
		$this->arNames[] = $helper->wrapQuotes($columnName);
		$value = $obMap->getDefaultValue('insert');
		if ($obMap->isDefaultSql('insert'))
		{
			$this->arValues[] = $value;
		}
		else
		{
			/** @var Fields\ScalarField $fieldClassName */
			$fieldClassName = $obMap->getClassName();
			$value = $fieldClassName::saveDataModification($value,$obMap);
			$this->arValues[] = $obMap->getSqlValue($value);
		}
	}

	/**
	 * Возвращает INSERT зпрос
	 *
	 * @param $bFirst
	 *
	 * @return string
	 * @since 0.2.0
	 */
	private function getInsertSql (&$bFirst)
	{
		$sql = '';
		if ($bFirst)
		{
			$bFirst = false;
			$sql .= "(".implode(',',$this->arNames).")\nVALUES\n\t(".implode(',',$this->arValues).")";
		}
		else
		{
			$sql .= ",\n\t(".implode(',',$this->arValues).")";
		}

		return $sql;
	}

	/**
	 * Устанавливает массив значений полей таблицы
	 *
	 * @param array $array
	 * @since 0.2.0
	 */
	protected function setArray(array $array)
	{
		$this->arAdd = $array;
	}

	/**
	 * Возвращает массив значений полей таблицы
	 *
	 * @return array
	 * @since 0.2.0
	 */
	private function getArray ()
	{
		return $this->arAdd;
	}
}
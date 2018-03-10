<?php
/**
 * Ms\Core\Entity\Db\Query\QuerySelect
 * Сущность SELECT запроса к базе данных
 *
 * @package Ms\Core
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
use Ms\Core\Lib\Tools;

class QuerySelect extends QueryBase
{
	/**
	 * @var mixed
	 */
	private $primaryKey = null;

	/**
	 * @var string|array
	 */
	private $paramsSelect = null;

	/**
	 * @var array
	 */
	private $paramsFilter = null;

	/**
	 * @var array
	 */
	private $paramsGroup = null;

	/**
	 * @var array
	 */
	private $paramsOrder = null;

	/**
	 * @var int
	 */
	private $paramsLimit = null;

	/**
	 * @var int
	 */
	private $paramsOffset = null;

	/**
	 * @var null
	 */
	private $paramsRuntime = null;

	/**
	 * @var array
	 */
	private $arSqlFrom = array();

	/**
	 * @var array
	 */
	private $arFieldTable = array();

	/**
	 * @var array
	 */
	private $join_registry = array();

	/**
	 * @var array
	 */
	private $join_map = array();

	/**
	 * @var array
	 */
	private $arSelect = array();

	/**
	 * @var array
	 */
	private $arAlias = array();

	/**
	 * Конструктор
	 *
	 * @param null|string $tableClass
	 * @param array       $arParams
	 *
	 * @throws Exception\ArgumentTypeException
	 * @since 0.2.0
	 */
	public function __construct ($tableClass,$arParams=array())
	{
		/** @var DataManager $tableClass */
		$this->setType('select');
		$this->setTableMap($tableClass::getMapArray());
		$this->setTableName($tableClass::getTableName());
		$this->primaryKey = $tableClass::getPrimaryField();

		if (!empty($arParams))
		{
			foreach ($arParams as $field=>$values)
			{
				switch (strtolower($field))
				{
					case 'select':
						$this->paramsSelect = $values;
						break;
					case 'filter':
						$this->paramsFilter = $values;
						break;
					case 'group':
						$this->paramsGroup = $values;
						break;
					case 'order':
						$this->paramsOrder = $values;
						break;
					case 'limit':
						$this->paramsLimit = $values;
						break;
					case 'offset':
						$this->paramsOffset = $values;
						break;
					case 'runtime':
						$this->paramsRuntime = $values;
						break;
				}
			}
		}
		if (is_null($this->paramsOrder))
		{
			$this->paramsOrder = array($this->primaryKey=>'ASC');
		}

		$this->buildQuery();
	}

	/**
	 * Возвращает список полей SQL запроса
	 *
	 * @return array
	 * @since 0.2.0
	 */
	public function getSelect()
	{
		return $this->arSelect;
	}

	/**
	 * Собирает SQL запрос из параметров
	 * @since 0.2.0
	 */
	protected function buildQuery()
	{
		$sql = "";

		$sql.= $this->createSqlSelect();

		$sql.= $this->createSqlFrom();

		$sql.= $this->createSqlJoin();

		$sql.= $this->createSqlWhere();

		$sql.= $this->createSqlGroup();

		$sql.= $this->createSqlOrder();

		$sql.= $this->createSqlLimit();

/*		if (!is_null($this->getRuntime()))
		{
			//TODO: Доделать !is_null($this->getRuntime())
		}*/

		$this->setSql($sql);
	}

	/**
	 * Собирает SELECT часть SQL запроса
	 *
	 * @return string
	 * @since 0.2.0
	 */
	private function createSqlSelect()
	{
		$tableName = $this->getTableName();
		$helper = new SqlHelper($tableName);
		$arMap = $this->getTableMap();
		$sqlSelect = "SELECT\n\t";
		/*
		 * Проверяем отсутствие параметра select, либо значение равно *,
		 * то есть выбрать все поля таблицы
		 */
		if (is_null($this->paramsSelect) || $this->paramsSelect == '*')
		{
			$sqlSelect .= "*\n";
			return $sqlSelect;
		}

		//Создаем массив получаемых полей
		/*
		 * Формат массива arSelect
		 * $fieldName => array(
		 *  "NAME" => $fieldName,
		 *  "ALIAS" => $altFieldName,
		 *  "TABLE" => $tableClass,
		 *  "FIELD" => $arMap[$fieldName]
		 * )
		 */
		$arSelect = &$this->arSelect;
		/*
		 * Проверяем случай, когда кужно выбрать лишь одно поле, тогде select = этому полю
		 */
		if (!is_array($this->paramsSelect))
		{
			$this->addSelectField($this->paramsSelect);
		}
		else
		{
			/*
			 * Проверяем массив параметров select. Массив может сочетать различные виды:
			 * 0. выбираем все поля (оставлено для совместимости)
			 * 0 => *
			 *
			 * 1. выбираем поле FIELD и в результате оно будет значится как FIELD
			 * 0 => FIELD
			 *
			 * 2.выбикаем поле FIELD и в результате оно будет значится как NEW_FIELD
			 * FIELD => NEW_FIELD
			 *
			 * 3. выбираем поле OTHER_FIELD из другой таблицы, на которую ссылается поле FIELD
			 * значение будет находится в поле FIELD_OTHER_FIELD
			 * 0 => FIELD.OTHER_FIELD
			 *
			 * 4. выбираем поле OTHER_FIELD из другой таблицы, на которую ссылается поле FIELD
			 * значение будет находится в поле FIELD_OTHER
			 * FIELD.OTHER_FIELD => FIELD_OTHER
			 */
			foreach ($this->paramsSelect as $key=>$value)
			{
				//Если массив вида 0, 1 или 3
				if (is_numeric($key))
				{
					//Если массив вида 0, 1
					if (strpos($value,'.')===false)
					{
						//Если массив вида 0
						if ($value=='*')
						{
							//Указываем, что выбираются все поля
							/**
							 * @var string $fieldName
							 * @var Fields\ScalarField $objField
							 */
							foreach ($arMap as $fieldName=>$objField)
							{
								if (!isset($arSelect[$fieldName]))
								{
									$this->addSelectField($fieldName);
								}
							}
						}
						//Если массив вида 1
						else
						{
							$this->addSelectField($value);
						}
					}
					//Если массив вида 3
					else
					{
						$this->addSelectLinkedField($value);
					}
				}
				//Если массив вида 2 или 4
				else
				{
					try
					{
						//Если вдруг $value == '*', выбрасываем исключение (не должно такого быть)
						if ($value=='*')
						{
							throw new Exception\ArgumentOutOfRangeException('select('.$key.'=>'.$value.')');
						}
					}
					catch (Exception\ArgumentOutOfRangeException $e)
					{
						die($e->showException());
					}

					//Если массив вида 2
					if (strpos($key,'.')===false)
					{
						//Если поле еще не добавлено, добавляем
						if (!isset($arSelect[$key]))
						{
							$this->addSelectField($key,$value);
						}
						//Если поле было добавлено, меняем его вид
						else
						{
							unset($arSelect[$key]);
							$this->addSelectField($key,$value);
						}
					}
					//Если массив вида 4
					else
					{
						//Если поле еще не добавлено, добавляем
						if (!isset($arSelect[str_replace('.','_',$key)]))
						{
							$this->addSelectLinkedField($key,$value);
						}
						//Если поле было добавлено, меняем его вид
						else
						{
							unset($arSelect[str_replace('.','_',$key)]);
							$this->addSelectLinkedField($key,$value);
						}
					}
				}
			}
		}
		//msDebugNoAdmin($this->arSelect);

		if (!empty($this->arSelect))
		{
			$bFirst = true;
			foreach ($this->arSelect as $alias=>$arParams)
			{
				if (!is_array($arParams))
				{
					continue;
				}
				if ($bFirst)
				{
					$bFirst = false;
				}
				else
				{
					$sqlSelect .= ",\n\t";
				}
				$sqlSelect .= $helper->wrapQuotes($arParams['TABLE']::getTableName()).'.'
					.$helper->wrapQuotes($arParams['NAME']);
				if (!is_null($arParams['ALIAS']))
				{
					$sqlSelect .= " AS ".$helper->wrapQuotes($arParams['ALIAS']);
				}
			}
		}
		//Если массив полей пуст, выбираем всё
		else
		{
			$sqlSelect .= "*\n";
		}
		//msDebug($this->arAlias);
		//msDebug($this->join_registry);
		//msDebug($this->join_map);

		return $sqlSelect."\n";
	}

	/**
	 * @param      $fieldName
	 * @param null $altFieldName
	 * @param null $tableName
	 * @since 0.2.0
	 */
	private function addSelectField ($fieldName, $altFieldName=null, $tableName=null)
	{
		/*
		 * Формат массива arSelect
		 * $fieldName => array(
		 *  "NAME" => $fieldName,
		 *  "ALIAS" => $altFieldName,
		 *  "TABLE" => $tableClass,
		 *  "FIELD" => $arMap[$fieldName]
		 * )
		 */
		//Получаем имя таблицы из параметра или из объекта
		if (is_null($tableName))
		{
			$tableName = $this->getTableName();
		}
		if ($fieldName==$altFieldName)
		{
			$altFieldName = null;
		}
		//Получает имя класса таблицы
		/** @var DataManager $tableClass */
		$tableClass = Tools::getClassNameByTableName($tableName);
		//Получаем карту таблицы
		$arMap = $tableClass::getMapArray();

		//Если у поля есть альтернативное название, сохраняем его и ссылку на оригинальное поле
		if (!is_null($altFieldName))
		{
			$this->arAlias[$tableName.'.'.$fieldName] = $altFieldName;
		}
		try
		{
			if (!isset($arMap[$fieldName]))
			{
				throw new Exception\ArgumentNullException('$arMap['.$fieldName.']');
			}
			else
			{
				if (!is_null($altFieldName))
				{
					$alias = $altFieldName;
				}
				else
				{
					$alias = $fieldName;
				}
				//Сохраняем параметры выбираемого поля
				$this->arSelect[$alias] = array(
					"NAME"  => $fieldName,
					"ALIAS" => $altFieldName,
					"TABLE" => $tableClass,
					"FIELD" => $arMap[$fieldName]
				);
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}
	}

	/**
	 * Добавляет связанные поля в SELECT
	 *
	 * @param      $fieldName
	 * @param null $altFieldName
	 * @param null $tableName
	 * @since 0.2.0
	 */
	private function addSelectLinkedField ($fieldName, $altFieldName=null, $tableName=null)
	{
		/*
		 * $fieldName вида FIELD.OTHER_FIELD.THIRD_FIELD
		 */
		//Определяем имя основной таблицы из параметров или из свойства объекта
		if (is_null($tableName))
		{
			$tableName = $this->getTableName();
		}
		/** @var DataManager $tableClass */
		$tableClass = Tools::getClassNameByTableName($tableName);
		//Получаем карту таблицы
		$arMap = $tableClass::getMapArray();

		//Разбираем поле
		$arFields = explode('.',$fieldName);
		//msDebug($arFields);
		for ($i=0; $i<count($arFields)-1; $i++)
		{
			if (isset($arFields[$i+1]))
			{
				//Определяем основное поле и поле из другой таблицы
				$mainField = &$arFields[$i];
				$linkedField = &$arFields[$i+1];
				//Определяем обрабатываемую таблицу
				if (isset($linkTable))
				{
					$selectTable = $linkTable;
					if (isset($this->arAlias[$linkTable.'.'.$mainField]))
					{
						$mainFieldSelect = $this->arAlias[$linkTable.'.'.$mainField];
					}
					else
					{
						$mainFieldSelect = $mainField;
					}
				}
				else
				{
					$selectTable = $tableName;
					$mainFieldSelect = $mainField;
				}
				//Если основного поля нет в массиве отбираемых полей, добавляем его
				if (!isset($this->arSelect[$mainFieldSelect]))
				{
					$this->addSelectField($mainField,$mainFieldSelect,$selectTable);
				}
				//Определяем карту таблицы
				if (isset($linkedMap))
				{
					$selectMap = $linkedMap;
				}
				else
				{
					$selectMap = $arMap;
				}
				//Получаем слинкованную таблицу для главного поля
				/** @var Fields\ScalarField[] $selectMap */
				$link = $selectMap[$mainField]->getLink();
				try
				{
					if (is_null($link))
					{
						throw new Exception\ArgumentNullException('$arMap['.$mainField.'] link');
					}
				}
				catch (Exception\ArgumentNullException $e)
				{
					die($e->showException());
				}
				//Разбираем слинкованную таблицу на имя таблицы и имя PRIMARY этой таблицы
				list($linkTable, $linkField) = explode('.',$link);
				/** @var DataManager $linkTableClass */
				$linkTableClass = Tools::getClassNameByTableName($linkTable);
				//Получаем карту слинкованной таблицы
				$linkedMap = $linkTableClass::getMapArray();
				try
				{
					//Если поле из другой таблицы равно *, значит необходимо выбрать все поля
					if ($linkedField == '*')
					{
						//Если слинкованная талица не была еще подключена, подключаем ее через JOIN
						if (!isset($this->join_registry[$linkTable]))
						{
							$this->addJoin(
								$linkTable,
								"LEFT",
								array(
									"BASE_TABLE" => $selectTable,
									"BASE_FIELD" => $mainField,
									"COMPARE_TABLE" => $linkTable,
									"COMPARE_FIELD" => $linkField
								)
							);
						}
						//Бежим по карте слинкованной таблицы и подключаем все поля
						foreach ($linkedMap as $f=>$o)
						{
							$this->addSelectField($f,$mainFieldSelect.'_'.$f,$linkTable);
						}
					}
					//Если указано имя поля и оно есть в карте таблицы, добавляем в выбор его
					elseif (isset($linkedMap[$linkedField]))
					{
						//Если слинкованная талица не была еще подключена, подключаем ее через JOIN
						if (!isset($this->join_registry[$linkTable]))
						{
							$this->addJoin(
								$linkTable,
								"LEFT",
								array(
									"BASE_TABLE" => $selectTable,
									"BASE_FIELD" => $mainField,
									"COMPARE_TABLE" => $linkTable,
									"COMPARE_FIELD" => $linkField
								)
							);
						}

						$this->addSelectField($linkedField,$mainFieldSelect.'_'.$linkedField,$linkTable);
					}
					//Иначе выбрасываем исключение
					else
					{
						throw new Exception\ArgumentNullException('$linkedMap['.$linkedField.']');
					}
				}
				catch (Exception\ArgumentNullException $e)
				{
					die($e->showException());
				}
			}
		}
		//Если было задано альтернативное название поля, то меняем последнее обработанное поле на него
		if (!is_null($altFieldName) && isset($mainFieldSelect) && isset($linkedField) && isset($linkTable))
		{
			if (isset($this->arSelect[$mainFieldSelect.'_'.$linkedField]))
			{
				unset($this->arSelect[$mainFieldSelect.'_'.$linkedField]);
			}
			$this->addSelectField($linkedField,$altFieldName,$linkTable);
		}
	}

	/**
	 * Добавляет JOIN заданного типа к SQL запросу, если не был добавлен ранее
	 *
	 * @param string    $table  Таблица JOIN
	 * @param string    $type   Тип JOIN (INNER, LEFT, RIGHT и т.д.)
	 * @param array     $on     Значения параметра ON
	 * @since 0.2.0
	 */
	private function addJoin ($table,$type='',$on=array()/*,$using=''*/)
	{
		if (!isset($this->join_registry[$table]))
		{
			$arJoin = array(
				'TYPE' => $type,
				'TABLE' => $table
			);
			if (!empty($on))
			{
				if (isset($on['BASE_TABLE']) || isset($on['BASE_FIELD']) ||
					isset($on['COMPARE_TABLE']) || isset($on['COMPARE_FIELD']))
				{
					$arJoin['ON'] = array($on);
				}
				else
				{
					$arJoin['ON'] = $on;
				}
			}
			/*
			if ($using != '')
			{
				$arJoin['USING'] = $using;
			}*/
			$this->join_map[] = $arJoin;
			$this->join_registry[$table] = true;
		}
	}

	/**
	 * Собирает FROM часть SQL запроса
	 *
	 * @return string
	 * @since 0.2.0
	 */
	private function createSqlFrom()
	{
		$helper = new SqlHelper($this->getTableName());

		return "FROM\n\t".$helper->wrapTableQuotes()."\n";
	}

	/**
	 * Собирает JOIN часть SQL запроса
	 *
	 * @return string
	 * @since 0.2.0
	 */
	private function createSqlJoin()
	{
		$helper = new SqlHelper();
		$arJoin = &$this->join_map;
		$sql = '';
		if (!empty($arJoin))
		{
			foreach ($arJoin as $join)
			{
				$sql .= "\n\t".$join['TYPE']." JOIN\n\t\t"
					.$helper->wrapQuotes($join['TABLE'])."\n\t";
				if (isset($join['ON']))
				{
					$sql .= "ON\n";
					if (isset($join['ON']['LOGIC']))
					{
						$logic = $join['ON']['LOGIC'];
						unset($join['ON']['LOGIC']);
					}
					else
					{
						$logic = "AND";
					}
					$count = count($join['ON']);
					foreach ($join['ON'] as $i=>$join_on)
					{
						$sql .= "\t\t".$helper->wrapQuotes($join_on['BASE_TABLE'])."."
							.$helper->wrapQuotes($join_on['BASE_FIELD'])." = "
							.$helper->wrapQuotes($join_on['COMPARE_TABLE'])."."
							.$helper->wrapQuotes($join_on['COMPARE_FIELD']);
						if ($i<($count-2))
						{
							$sql .= " ".$logic;
						}
						$sql .= "\n";
					}
				}
			}
		}

		return $sql;
	}

	/**
	 * Собирает WHERE часть SQL запроса
	 *
	 * @return string
	 * @throws
	 * @since 0.2.0
	 */
	private function createSqlWhere()
	{
		/*
		 * Массив arFilter может быть следующего вида:
		 */
		$sql = '';
		if (!empty($this->paramsFilter))
		{
			$sql.="WHERE\n\t";
			$sql .= $this->prepareArFilter($this->paramsFilter,true);
		}


		return $sql."\n";
	}

	/**
	 * Обрабатывает параметры в arFilter
	 *
	 * @param      $arFilter
	 * @param bool $bFirstFilter
	 *
	 * @return string
	 * @throws Exception\ArgumentNullException
	 * @since 0.2.0
	 */
	private function prepareArFilter($arFilter,$bFirstFilter = false)
	{
		//Определяем по какой логике обрабатываются поля, по 'И' или по 'ИЛИ'
		if (isset($arFilter['LOGIC']))
		{
			if ($arFilter['LOGIC']=='OR')
			{
				$filterLogic = 'OR';
			}
			else
			{
				$filterLogic = 'AND';
			}
			unset($arFilter['LOGIC']);
		}
		else
		{
			$filterLogic = 'AND';
		}
		$arMap = $this->getTableMap();
		$helper = new SqlHelper($this->getTableName());
		$sql = '';
		//Определяем множественность условий фильтра
		$bMultiFilter = (count($arFilter)>1);
		//Если это не первый фильтр и он множественный, используем скобки
		if (!$bFirstFilter && $bMultiFilter)
		{
			$sql .= "(\n\t";
		}
		$bFirst = true;
		//Идем по массиву
		foreach ($arFilter as $field=>$value)
		{
			if ($bFirst)
			{
				$bFirst = false;
			}
			else
			{
				$sql .= " ".$filterLogic."\n\t";
			}
			//Если элементом является массив filter, обрабатываем его отдельно
			if (is_numeric($field))
			{
				$sql .= $this->prepareArFilter($value);
			}
			//Если необходимо обработать значение фильтра
			else
			{
				if (is_null($value))
				{
					$arMask = $this->setMask($field,null);
				}
				else
				{
					$arMask = $this->setMask($field,is_array($value));
				}
				try
				{
					if (!isset($arMap[$field]))
					{
						//msDebugNoAdmin($arMask);
						throw new Exception\ArgumentOutOfRangeException($field);
					}
				}
				catch (Exception\ArgumentOutOfRangeException $e)
				{
					die($e->showException());
				}
				//Флаг того, что значением является другое поле таблицы
				$bValueField = false;
				//Если значение не является массивом и значением является другое поле таблицы
				if (!is_array($value) && strpos($value,'FIELD_')!==false)
				{
					$bValueField = true;
					$value = str_replace('FIELD_','',$value);
				}
				//Собираем sql запрос
				if ($arMask['op']=='NOT')
				{
					$sql .= 'NOT ';
				}
				$sql .= $helper->wrapFieldQuotes($field)." ".$arMask['op'];
				//Если значением является другое поле этой таблицы, указываем поле
				if ($bValueField)
				{
					$sql .= " ".$helper->wrapFieldQuotes($value);
				}
				//Если значение не равно NULL
				elseif (!is_null($value))
				{
					//Если значение является массивом
					if (is_array($value))
					{
						//Если используется функция BETWEEN или NOT BETWEEN
						if (strpos($arMask['op'],'BETWEEN')!==false)
						{
							try
							{
								//Если в массиве есть значения с индексами 0 и 1
								if (isset($value[0]) && $value[1])
								{
									$sql .= ' '.$arMap[$field]->getSqlValue($value[0]).' AND '
										.$arMap[$field]->getSqlValue($value[1]);
								}
								//Если нет значений с индексами 0 и 1, выбрасываем исключение
								else
								{
									throw new Exception\ArgumentTypeException($field,'array[0,1]');
								}
							}
							catch (Exception\ArgumentTypeException $e)
							{
								die($e->showException());
							}
						}
						//В противном случае, обрабатываем значения в массиве
						else
						{
							$arVal = array();
							foreach ($value as $val)
							{
								$arVal[] = $arMap[$field]->getSqlValue($val);
							}
							$sql .= ' ('.implode(',',$arVal).')';
						}
					}
					//Обрабатываем значение, которое явялется SQL кодом
					elseif ($arMask['sql'])
					{
						$sql .= ' '.$value;
					}
					//Обрабатываем все остальные значения
					else
					{
						$sql .= ' '.$arMap[$field]->getSqlValue($value);
					}
				}
			}
		}
		if (!$bFirstFilter && $bMultiFilter)
		{
			$sql .= "\n\t)";
		}

		return $sql;
	}

	/**
	 * Устанавливает маску
	 *
	 * @param      $field
	 * @param bool $isArrValue
	 *
	 * @return array
	 * @throws Exception\ArgumentNullException
	 * @since 0.2.0
	 */
	private function setMask (&$field, $isArrValue=false)
	{
		$arMask = $this->maskField($field, $isArrValue);
		$field = $arMask['field'];

		return $arMask;
	}

	/**
	 * Вычисляет маску поля
	 *
	 * @param null $field
	 * @param bool $isArrValue
	 *
	 * @return array
	 * @throws Exception\ArgumentNullException
	 * @since 0.2.0
	 */
	private function maskField ($field=null, $isArrValue=false)
	{
		static $triple_char = array(
			"!><" => "NB",  //not between
			"s!=" => "SNI",  //sql not Identical
			"s!%" => "SNS",  //sql not substring
		);
		static $double_char = array(
			"!=" => "NI",   //not Identical
			"!%" => "NS",   //not substring
			"><" => "B",    //between
			">=" => "GE",   //greater or equal
			"<=" => "LE",   //less or equal
			"s=" => "SE",   //sql equal
			"s%" => "SS",   //sql LIKE
			"s>" => "SG",   //sql greater
			"s<" => "SL",   //sql less
		);
		static $single_char = array(
			"=" => "I",     //Identical
			"%" => "S",     //substring
			"?" => "?",     //logical
			">" => "G",     //greater
			"<" => "L",     //less
			"!" => "N",     //not field LIKE val
		);

		try
		{
			if (is_null($field))
			{
				throw new Exception\ArgumentNullException('field');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}

		try
		{
			$op = substr($field,0,3);
			if ($op && isset($triple_char[$op]))
			{
				$arr = array(
					"field"=>substr($field,3),
					"mask"=>$op,
					"operation"=>$triple_char[$op],
					'op' => $op,
					'sql' => false
				);

				//"!><" => "NB",  //not between
				//"s!=" => "SNI",  //sql not Identical
				//"s!%" => "SNS",  //sql not substring
				if ($op == '!><')
				{
					if (!$isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'array');
					}
					$arr['op'] = 'NOT BETWEEN';
				}
				elseif ($op == 's!=')
				{
					if ($isArrValue)
					{
						$arr['op'] = 'NOT IN';
					}
					elseif (!is_null($isArrValue))
					{
						$arr['op'] = '!=';
					}
					else
					{
						throw new Exception\ArgumentNullException($field);
					}
					$arr['sql'] = true;
				}
				elseif ($op == 's!%')
				{
					if ($isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'string');
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
					$arr['op'] = 'NOT LIKE';
					$arr['sql'] = true;
				}
				return $arr;
			}
			$op = substr($field,0,2);
			if ($op && isset($double_char[$op]))
			{
				$arr = array(
					"field"=>substr($field,2),
					"mask"=>$op,
					"operation"=>$double_char[$op],
					'op' => $op,
					'sql' => false
				);

				//"!=" => "NI",   //not Identical
				//"!%" => "NS",   //not substring
				//"><" => "B",    //between
				//">=" => "GE",   //greater or equal
				//"<=" => "LE",   //less or equal
				//"s=" => "SE",   //sql equal
				//"s%" => "SS",   //sql LIKE
				//"s>" => "SG",   //sql greater
				//"s<" => "SL",   //sql less
				if ($op == '!=')
				{
					if ($isArrValue)
					{
						$arr['op'] = 'NOT IN';
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
				}
				elseif ($op == '!%')
				{
					if ($isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'string');
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
					$arr['op'] = 'NOT LIKE';
				}
				elseif ($op == '><')
				{
					if (!$isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'array');
					}
					$arr['op'] = 'BETWEEN';
				}
				elseif ($op == '>=')
				{
					if ($isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'string|int|float');
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
				}
				elseif ($op == '<=')
				{
					if ($isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'string|int|float');
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
				}
				elseif ($op == 's=')
				{
					if ($isArrValue)
					{
						$arr['op'] = 'IN';
					}
					elseif (!is_null($isArrValue))
					{
						$arr['op'] = '=';
					}
					else
					{
						throw new Exception\ArgumentNullException($field);
					}
					$arr['sql'] = true;
				}
				elseif ($op == 's%')
				{
					if ($isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'string');
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
					$arr['op'] = 'LIKE';
					$arr['sql'] = true;
				}
				elseif ($op == 's>')
				{
					if ($isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'string');
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
					$arr['op'] = '>';
					$arr['sql'] = true;
				}
				elseif ($op == 's<')
				{
					if ($isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'string');
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
					$arr['op'] = '<';
					$arr['sql'] = true;
				}
				return $arr;
			}
			$op = substr($field,0,1);
			if ($op && isset($single_char[$op]))
			{
				$arr = array(
					"field"=>substr($field,1),
					"mask"=>$op,
					"operation"=>$single_char[$op],
					'op' => $op,
					'sql' => false
				);

				//"=" => "I",     //Identical
				//"%" => "S",     //substring
				//"?" => "?",     //logical
				//">" => "G",     //greater
				//"<" => "L",     //less
				//"!" => "N",     //not field LIKE val
				if ($op == '=')
				{
					if ($isArrValue)
					{
						$arr['op'] = 'IN';
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
				}
				elseif ($op == '%')
				{
					if ($isArrValue)
					{
						throw new Exception\ArgumentTypeException('$value','string');
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
					$arr['op'] = 'LIKE';
				}
				elseif ($op == '>')
				{
					if ($isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'string|int|float');
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
				}
				elseif ($op == '<')
				{
					if ($isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'string|int|float');
					}
					elseif (is_null($isArrValue))
					{
						throw new Exception\ArgumentNullException($field);
					}
				}
				elseif ($op == '!')
				{
					if (is_null($isArrValue))
					{
						$arr['op'] = 'IS NOT NULL';
					}
					elseif ($isArrValue)
					{
						throw new Exception\ArgumentTypeException($field,'NULL|string|int|float');
					}
					else
					{
						$arr['op'] = 'NOT';
					}
				}

				return $arr;
			}
		}
		catch (Exception\ArgumentTypeException $e)
		{
			die($e->showException());
		}


		$arr = array(
			"field"=>$field,
			"mask"=>null,
			"operation"=>null,
			'op' => '=',
			'sql' => false
		);
		if (is_null($isArrValue))
		{
			$arr['op'] = 'IS NULL';
		}
		elseif ($isArrValue)
		{
			$arr['op'] = 'IN';
		}

		return $arr;
	}

	/**
	 * Собирает GROUP часть SQL запроса
	 *
	 * @return string
	 * @since 0.2.0
	 */
	private function createSqlGroup()
	{
		$sqlGroup = "";
		$helper = new SqlHelper();
		$arGroup = $this->paramsGroup;
		if (!is_null($arGroup) && !empty($arGroup))
		{
			//TODO: Доделать (работу с группировками)
			$sqlGroup .= "GROUP BY\n\t";
			$bFirst = true;
			foreach ($arGroup as $groupField=>$sort)
			{
				if($bFirst)
				{
					$bFirst = false;
					$sqlGroup .= $helper->wrapQuotes($sort);//.' '.$sort;
				}
				else
				{
					$sqlGroup .= ",\n\t".$helper->wrapQuotes($sort);//.' '.$sort;
				}
			}
			$sqlGroup.="\n";
		}

		return $sqlGroup;
	}

	//TODO: Функцию не проверял на обновленном ядре
	/**
	 * Собирает ORDER часть SQL запроса
	 *
	 * @return string
	 * @since 0.2.0
	 */
	private function createSqlOrder()
	{
		$sqlOrder = "";
		$helper = new SqlHelper();
		$arOrder = &$this->paramsOrder;
		$tableName = $this->getTableName();
		$arMap = $this->getTableMap();
		if (!empty($arOrder))
		{
			$sqlOrder .= "ORDER BY\n\t";
			$bFirst = true;
			//msDebug($arOrder);
			foreach ($arOrder as $sort=>$by)
			{
				$childTable = $tableName;
				$childField = 'ID';
				if (strpos($sort,'.')!==false)
				{
					try
					{
						list($baseField,$childField) = explode('.',$sort);
						if (isset($arMap[$baseField]))
						{
							list($childTable,) = explode('.',$arMap[$baseField]->getLink());
						}
						else
						{
							throw new Exception\ArgumentNullException('$arMap['.$baseField.']');
						}
					}
					catch(Exception\ArgumentNullException $e)
					{
						die($e->showException());
					}
				}
				if ($bFirst)
				{
					$bFirst = false;
				}
				else
				{
					$sqlOrder .= ",\n\t";
				}
				if (strpos($sort,'.')===false)
				{
					if (isset($arMap[$sort]))
					{
						$sqlOrder .= $helper->wrapQuotes($tableName).'.'.$helper->wrapQuotes($sort).' '.$by;
					}
					elseif (isset($this->arSelect[$sort]))
					{
						$sqlOrder .= $helper->wrapQuotes($sort).' '.$by;
					}
				}
				else
				{
					$sqlOrder .= $helper->wrapQuotes($childTable).'.'.$helper->wrapQuotes($childField).' '.$by;
				}
			}
			$sqlOrder.="\n";
		}

		return $sqlOrder;
	}

	/**
	 * Собирает LIMIT часть SQL запроса
	 *
	 * @return string
	 * @since 0.2.0
	 */
	private function createSqlLimit()
	{
		$sqlLimit = "";
		if (!is_null($this->paramsLimit))
		{
			$sqlLimit .= "LIMIT ";
			if (!is_null($this->paramsOffset) && intval($this->paramsOffset)>0)
			{
				$sqlLimit .= $this->paramsOffset;
			}
			else
			{
				$sqlLimit .= 0;
			}
			$sqlLimit .= ', '.$this->paramsLimit;
			$sqlLimit.= "\n";
		}

		return $sqlLimit;
	}
}
<?php
/**
 * MSergeev\Core\Entity\Db\DBResult
 * Осуществляет обработку результата запроса к базе данных
 *
 * @package MSergeev\Core
 * @subpackage Entity\Db
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.1.0
 */

namespace MSergeev\Core\Entity\Db;

use MSergeev\Core\Entity\Application;
use MSergeev\Core\Lib;
use MSergeev\Core\Entity\Db\Query;

class DBResult
{
	/**
	 * Результат mysql запроса
	 * @var resource
	 */
	protected $result;
	/**
	 * Номер ошибки mysql запроса
	 * @var number|null
	 */
	protected $result_error_number=null;

	/**
	 * Текст ошибки mysql запроса
	 * @var string|null
	 */
	protected $result_error_text=null;

	/**
	 * Массив с описанием полей таблицы
	 * @var array
	 */
	protected $table_map;

	/**
	 * Массив последнего разобранного результата mysql запроса
	 * @var array
	 */
	protected $last_res;

	/**
	 * Массив последнего разобранного и обработанного mysql запроса
	 * @var array
	 */
	protected $last_result;

	/**
	 * Тип query запроса ("select", "insert", "delete", "create")
	 * @var string
	 */
	protected $query_type;

	/**
	 * Количество затронутых строк, при "select" запросе
	 * @var null|number
	 */
	protected $mysql_affected_rows=null;

	/**
	 * ID добавленной записи при "insert" запросе
	 * @var null|int
	 */
	protected $mysql_insert_id=null;

	/**
	 * Количество полей в результате mysql запроса
	 * @var int|null
	 */
	protected $mysql_num_fields=null;

	/**
	 * Количество строк в результате mysql запроса
	 * @var int|null
	 */
	protected $mysql_num_rows=null;

	/**
	 * Массив сущностей полей таблицы
	 * @var array
	 */
	protected $arFieldsEntity;

	/**
	 * Текст SQL запроса
	 * @var string
	 */
	protected $sql;

	/**
	 * Массив выбираемых из базы полей
	 * @var array
	 */
	protected $arSelect = array();

	/**
	 * Создает объект при получении результата mysql запроса
	 *
	 * @api
	 *
	 * @param resource $res Результат mysql запроса
	 * @param Query\QueryBase $obQuery Объект Query, содержащий mysql запрос
	 * @since 0.2.0
	 */
	function __construct($res=null, Query\QueryBase $obQuery=null)
	{
		$this->result = $res;
		if (!is_null($obQuery))
		{
			$this->table_map = $obQuery->getTableMap();
			$this->query_type = $obQuery->getType();
			//$this->arFieldsEntity = $obQuery->getFieldsEntity();
			$this->sql = $obQuery->getSql();
			if ($this->query_type == 'select')
			{
				/** @var Query\QuerySelect $obQuery */
				$this->arSelect = $obQuery->getSelect();
			}
		}
		if ($res)
		{
			if ($this->query_type == "select"){
				$this->mysql_num_fields = mysql_num_fields($res);
				$this->mysql_num_rows = mysql_num_rows($res);
			}
		}
	}

	/**
	 * Возвращает время исполнения SQL запроса
	 *
	 * @return float
	 * @since 0.1.0
	 */
	public function getQueryTime ()
	{
		$DB = Application::getInstance()->getConnection();
		return floatval($DB->getLastQueryTime());
	}

	/**
	 * Возвращает время исполнения всех SQL запросов
	 *
	 * @return float
	 * @since 0.1.0
	 */
	public function getAllQueryTime ()
	{
		$DB = Application::getInstance()->getConnection();
		return floatval($DB->getAllQueryTime());
	}

	/**
	 * Возвращает количество SQL запросов
	 *
	 * @return int
	 * @since 0.1.0
	 */
	public function getQueryCount ()
	{
		$DB = Application::getInstance()->getConnection();
		return intval($DB->getCountQuery());
	}

	/**
	 * Возвращает текст SQL запроса
	 *
	 * @api
	 *
	 * @return string Текст SQL запроса
	 * @since 0.1.0
	 */
	public function getSql ()
	{
		return $this->sql;
	}

	/**
	 * Разбирает результат mysql запроса и возвращает массив обработанных значений
	 *
	 * @api
	 *
	 * @return array Массив обработанных значений
	 * @since 0.1.0
	 */
	public function fetch ()
	{
		if ($this->query_type == "select" || $this->query_type == 'sql')
		{
			$ar_res = mysql_fetch_array($this->result);
			$this->last_res = $ar_res;
			$arResult = $arLast = array();
			if (is_array($ar_res))
			{
				foreach ($ar_res as $k => $v)
				{
					if (!is_numeric($k))
					{
						$arResult[$k] = $arLast['~'.$k] = $v;
						$arResult[$k] = $this->getFetchValue($k,$v);
					}
				}
				$arResult = array_merge($arResult,$arLast);
			}
			else
			{
				$arResult = $ar_res;
			}
		}
		else
		{
			$arResult = $this->result;
		}
		$this->last_result = $arResult;

		return $arResult;
	}

	/**
	 * Возвращает обработанное значение из базы
	 *
	 * @param string $k Имя полученного поля
	 * @param mixed  $v Значение полученного поля
	 *
	 * @return array|mixed
	 * @since 0.1.0
	 */
	private function getFetchValue ($k,$v)
	{
		$field = null;
		if (isset($this->arSelect[$k]))
		{
			$field = &$this->arSelect[$k]['FIELD'];
		}
		elseif (isset($this->table_map[$k]))
		{
			$field = &$this->table_map[$k];
		}

		if (!is_null($field))
		{
			/**
			 * @var Fields\ScalarField $fieldClassName
			 * @var Fields\ScalarField $field
			 */
			$fieldClassName = $field->getClassName();
			//$v = $this->arFieldsEntity[$k]->fetchDataModification($v);
			$v = $fieldClassName::fetchDataModification($v,$field);
		}

		return $v;
	}

	/**
	 * Возвращает количество строк в результате
	 *
	 * @api
	 *
	 * @return int Количество строк в результате
	 * @since 0.1.0
	 */
	public function getNumRows ()
	{
		return $this->mysql_num_rows;
	}

	/**
	 * Устанавливает количество полей в результате
	 *
	 * @api
	 *
	 * @param number $data Количество полей в результате
	 * @since 0.1.0
	 */
	public function setNumFields ($data)
	{
		$this->mysql_num_fields = $data;
	}

	/**
	 * Возвращает количество полей в результате
	 *
	 * @api
	 *
	 * @return int Количество полей в результате
	 * @since 0.1.0
	 */
	public function getNumFields ()
	{
		return $this->mysql_num_fields;
	}

	/**
	 * Возвращает массив последнего разобранного результата mysql запроса
	 *
	 * @api
	 *
	 * @return array Массив последнего разобранного результата mysql запроса
	 * @since 0.1.0
	 */
	public function getLastRes ()
	{
		return $this->last_res;
	}

	/**
	 * Возвращает массив последнего разобранного и обработанного mysql запроса
	 *
	 * @api
	 *
	 * @return array Массив последнего разобранного и обработанного mysql запроса
	 * @since 0.1.0
	 */
	public function getLastResult ()
	{
		return $this->last_result;
	}

	/**
	 * Возвращает результат mysql запроса
	 *
	 * @api
	 *
	 * @return resource Результат mysql запроса
	 * @since 0.1.0
	 */
	public function getResult ()
	{
		return $this->result;
	}

	/**
	 * Устанавливает номер ошибки mysql запроса
	 *
	 * @api
	 *
	 * @param number $number Номер ошибки mysql запроса
	 * @since 0.1.0
	 */
	public function setResultErrorNumber($number)
	{
		$this->result_error_number = $number;
	}

	/**
	 * Возвращает номер ошибки mysql запроса
	 *
	 * @api
	 *
	 * @return number номер ошибки mysql запроса
	 * @since 0.1.0
	 */
	public function getResultErrorNumber()
	{
		return $this->result_error_number;
	}

	/**
	 * Устанавливает текст ошибки mysql запроса
	 *
	 * @api
	 *
	 * @param string $text текст ошибки mysql запроса
	 * @since 0.1.0
	 */
	public function setResultErrorText ($text)
	{
		$this->result_error_text = $text;
	}

	/**
	 * Возвращает текст ошибки mysql запроса
	 *
	 * @api
	 *
	 * @return string текст ошибки mysql запроса
	 * @since 0.1.0
	 */
	public function getResultErrorText()
	{
		return $this->result_error_text;
	}

	/**
	 * Устанавливает количество затронутых строк, при "select" запросе
	 *
	 * @api
	 *
	 * @param number $data Количество затронутых строк, при "select" запросе
	 * @since 0.1.0
	 */
	public function setAffectedRows ($data)
	{
		$this->mysql_affected_rows = $data;
	}

	/**
	 * Возвращает количество затронутых строк, при "select" запросе
	 *
	 * @api
	 *
	 * @return number Количество затронутых строк, при "select" запросе
	 * @since 0.1.0
	 */
	public function getAffectedRows ()
	{
		return $this->mysql_affected_rows;
	}

	/**
	 * Устанавливает ID добавленной записи при "insert" запросе
	 *
	 * @api
	 *
	 * @param int $data ID добавленной записи при "insert" запросе
	 * @since 0.1.0
	 */
	public function setInsertId ($data)
	{
		$this->mysql_insert_id = $data;
	}

	/**
	 * Возвращает ID добавленной записи при "insert" запросе
	 *
	 * @api
	 *
	 * @return int ID добавленной записи при "insert" запросе
	 * @since 0.1.0
	 */
	public function getInsertId ()
	{
		return $this->mysql_insert_id;
	}
}
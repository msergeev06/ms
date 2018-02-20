<?php
/**
 * MSergeev\Core\Lib\DataManager
 * Используется для описания и обработки таблиц базы данных.
 * Наследуется в классах описания таблиц ядра и модулей
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @since 0.1.0
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Exception;
use MSergeev\Core\Entity\Db\Fields;
use MSergeev\Core\Entity\Db;

abstract class DataManager
{
	/**
	 * Возвращает имя текущего класса
	 *
	 * @api
	 *
	 * @return string Имя класса
	 */
	final public static function getClassName ()
	{
		return get_called_class();
	}

	/**
	 * Генерирует и возвращает название таблицы в базе
	 *
	 * @api
	 *
	 * @example 'ms_core_options'
	 *
	 * @return string название таблицы в базе
	 */
	final public static function getTableName()
	{
		$arClass = explode('\\',static::getClassName());
		if ($arClass[0]=='MSergeev')
		{
			$name = 'ms_';
		}
		else
		{
			$name = strtolower($arClass[0]).'_';
		}
		for($i=1; $i<count($arClass)-1;$i++)
		{
			if ($arClass[$i]!='Tables' && $arClass[$i]!='Modules')
			{
				$name .= strtolower($arClass[$i]).'_';
			}
		}
		$arCamel = preg_split('/([[:upper:]][[:lower:]]+)/', $arClass[count($arClass)-1], null, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
		if ($arCamel)
		{
			foreach ($arCamel as $camel)
			{
				if ($camel != 'Table')
				{
					$name .= strtolower($camel).'_';
				}
			}
		}
		$name = substr($name,0,strlen($name)-1);

		return $name;
	}

	/**
	 * Возвращает описание таблицы
	 *
	 * @api
	 *
	 * @example 'Опции'
	 *
	 * @return string Текст описания таблицы
	 */
	public static function getTableTitle()
	{
		return '';
	}

	/**
	 * Возвращает массив сущностей полей таблицы базы данных.
	 * Не рекомендуется использовать в API. Используйте getMapArray
	 *
	 * @see static::getMapArray
	 *
	 * @return Fields\ScalarField[] Массив сущностей полей таблицы базы данных
	 */
	abstract protected static function getMap();

	/**
	 * Возвращает обработанный массив сущностей полей таблицы базы данных.
	 * Обрабатывает массив, полученный функцией getMap
	 *
	 * @api
	 *
	 * @return Fields\ScalarField[] Обработанный массив сущностей полей таблицы базы данных
	 */
	final public static function getMapArray()
	{
		$arMap = static::getMap();
		$arMapArray = array();
		foreach ($arMap as $id=>$field)
		{
			$name = $field->getColumnName();
			$arMapArray[$name] = $field;
		}

		return $arMapArray;
	}

	/**
	 * Возвращает массив дефолтных значений таблицы,
	 * которые добавляются в таблицу при установке ядра или пакета
	 *
	 * @api
	 *
	 * @return array Массив дефолтных значений таблицы
	 */
	public static function getValues ()
	{
		return array();
	}

	/**
	 * @deprecated 0.2.0
	 * @return array
	 */
	public static function getArrayDefaultValues ()
	{
		return static::getValues();
	}

	/**
	 * Возвращает массив описывающий связанные с таблицей другие таблицы
	 * и объединяющие их поля
	 *
	 * @api
	 *
	 * @return array Массив связей таблиц
	 */
	public static function getTableLinks ()
	{
		return array();
	}

	/**
	 * Возвращает дополнительный SQL запрос, используемый после создания таблицы
	 *
	 * @return null|string
	 */
	public static function getAdditionalCreateSql ()
	{
		return null;
	}

	/**
	 * Возвращает дополнительный SQL запрос, используемый после удаления таблицы
	 *
	 * @return null|string
	 */
	public static function getAdditionalDeleteSql ()
	{
		return null;
	}

	/**
	 * Добавляет значения в таблицу
	 *
	 * @param array $arAdd      Массив содержащий значения таблицы
	 * @param bool  $bShowSql   Необходимость отобразить sql запрос вместо запроса
	 *
	 * @return Db\DBResult Результат mysql запроса
	 */
	final public static function add ($arAdd, $bShowSql=false)
	{
		try {
			if (isset($arAdd['VALUES']))
			{
				$arAdd = $arAdd['VALUES'];
				throw new Exception\ArgumentTypeException('$arAdd[VALUES]','$arAdd');
			}
		}
		catch (Exception\ArgumentTypeException $e)
		{
			echo $e->showException();
		}

		$query = new Db\Query\QueryInsert($arAdd,static::getClassName());

		if ($bShowSql)
		{
			return $query->getSql();
		}
		else
		{
			return $query->exec();
		}
	}

	/**
	 * Обновляет значения в таблице
	 *
	 * @ignore
	 *
	 * @param mixed $primary Поле PRIMARY таблицы
	 * @param array $arUpdate Массив значений таблицы в поле 'VALUES'
	 * @param bool  @bShowSql Флаг, показать SQL запрос вместо выполнения
	 *
	 * @return Db\DBResult Результат mysql запроса
	 */
	final public static function update ($primary, $arUpdate, $bShowSql=false)
	{
		try {
			if (isset($arUpdate['VALUES']))
			{
				$arUpdate = $arUpdate['VALUES'];
				throw new Exception\ArgumentTypeException('$arUpdate[VALUES]','$arUpdate');
			}
		}
		catch (Exception\ArgumentTypeException $e)
		{
			echo $e->showException();
		}

		$query = new Db\Query\QueryUpdate($primary,$arUpdate,static::getClassName());
		if ($bShowSql)
		{
			return $query->getSql();
		}

		return $query->exec();
	}

	/**
	 * Удаляет запись из таблицы
	 *
	 * @ignore
	 *
	 * @param mixed $primary Поле PRIMARY таблицы
	 * @param bool  $confirm Флаг, подтверждающий удаление всех связанных записей в других таблицах
	 *
	 * @return Db\DBResult Результат mysql запроса
	 */
	final public static function delete ($primary, $confirm=false)
	{
		$query = new Db\Query\QueryDelete($primary,$confirm,static::getClassName());

		return $query->exec();
	}

	/**
	 * Возвращает запись по PRIMARY ключу
	 *
	 * @param mixed $primaryValue   Значение PRIMARY поля
	 * @param string $primaryName   Имя PRIMARY поля
	 * @param array $arSelect       Список возвращаемых полей
	 * @param bool  $showSql        Флаг - показать SQL запрос вместо выборки
	 *
	 * @return array
	 */
	final public static function getByPrimary ($primaryValue, $primaryName=null, array $arSelect = array(), $showSql=false)
	{
		if (is_null($primaryName) || strlen($primaryName)<1)
		{
			$primaryName = static::getPrimaryField();
		}
		$arList['filter'] = array($primaryName => $primaryValue);
		if (!empty($arSelect))
		{
			$arList['select'] = $arSelect;
		}
		$arRes = static::getOne($arList,$showSql);

		return $arRes;
	}

	/**
	 * Возвращает запись по ID
	 *
	 * @param int   $id         Значение поля ID
	 * @param array $arSelect   Список возвращаемых полей
	 * @param bool  $showSql    Флаг - показать SQL запрос вместо выборки
	 *
	 * @return array
	 */
	final public static function getById($id, array $arSelect = array(), $showSql=false)
	{
		return static::getByPrimary($id, 'ID', $arSelect, $showSql);
	}

	/**
	 * Возвращает первое поле PRIMARY таблицы
	 *
	 * @api
	 *
	 * @return string|bool Название поля, либо false
	 */
	final public static function getPrimaryField ()
	{
		$arMap = static::getMap();
		foreach ($arMap as $field)
		{
			if ($field->isPrimary()) {
				return $field->getColumnName();
			}
		}

		return false;
	}

	/**
	 * @deprecated 0.2.0
	 *
	 *
	 * @param string $type Тип объекта Query
	 *
	 * @return Db\Query объект Query заданного типа
	 */
	public static function query ($type)
	{
		return null;
	}

	/**
	 * Функция добавляет в таблицу значения по-умолчанию, описанные в файле таблицы
	 *
	 * @api
	 *
	 * @return bool|Db\DBResult Результат mysql запроса, либо false
	 */
	final public static function insertDefaultRows ()
	{
		$arDefaultValues = static::getValues();
		if (count($arDefaultValues)>0)
		{
			$query = new Db\Query\QueryInsert($arDefaultValues,static::getClassName());
			$res = $query->exec();

			return $res;
		}
		else {
			return false;
		}
	}

	/**
	 * Функция создает таблицу
	 *
	 * @api
	 *
	 * @return Db\DBResult Результат mysql запроса
	 */
	final public static function createTable ()
	{
		$query = new Db\Query\QueryCreate(static::getClassName());
		//msEchoVar($query->getSql());
		$res = $query->exec();
		if ($res->getResult())
		{
			$additionalSql = static::getAdditionalCreateSql();
			if (!is_null($additionalSql))
			{
				$query = new Db\Query\QueryBase($additionalSql);
				$query->exec();
			}

			static::OnAfterCreateTable();
		}

		return $res;
	}

	/**
	 * Функция проверяет описанные связи таблицы, используя запросы к DB
	 *
	 * @api
	 *
	 * @return bool Связи существуют - true, инае - false
	 */
	final public static function checkTableLinks()
	{
		$bLinks = false;

		$helper = new Db\SqlHelper();
		$arLinks = static::getTableLinks();
		$tableName = static::getTableName();
		foreach ($arLinks as $field=>$arLink)
		{
			$sql = "SELECT\n\t".'t.'.$helper->wrapQuotes($field)."\n";
			$sql .= "FROM\n\t".$helper->wrapQuotes($tableName)." t";
			$where = "WHERE\n\t";

			$t=0;
			$bFirst = true;
			foreach ($arLink as $tableName=>$fieldName)
			{
				$t++;
				if ($bFirst)
				{
					$bFirst = false;
				}
				else
				{
					$where .= " AND\n\t";
				}
				$sql .= ",\n\t";
				$sql .= $helper->wrapQuotes($tableName)." t".$t;
				$where .= "t".$t
					.".".$helper->wrapQuotes($fieldName)
					." = t.".$helper->wrapQuotes($field);
			}
			$sql .= "\n".$where;

			$query = new Db\Query\QueryBase($sql);
			$res = $query->exec();
			if ($ar_res = $res->fetch())
			{
				$bLinks = true;
			}
		}

		return $bLinks;
	}

	/**
	 * Вызывается после создания таблицы
	 */
	public static function OnAfterCreateTable (){}

	/**
	 * Осуществляет выборку из таблицы и возвращает 1 запись
	 *
	 * @param array $arParams       Параметры getList
	 * @param bool  $showSql        Флаг - показать SQL запрос вместо выборки
	 *
	 * @return array|string|bool    Массив полей записи, SQL-запрос, либо false
	 */
	final public static function getOne ($arParams=array(),$showSql=false)
	{
		$arParams['limit'] = 1;
		$arRes = static::getList($arParams,$showSql);
		if ($showSql)
		{
			return $arRes;
		}
		elseif ($arRes && isset($arRes[0]))
		{
			$arRes = $arRes[0];
		}

		return $arRes;
	}

	/**
	 * Осуществляет выборку из таблицы значений по указанным параметрам
	 *
	 * @api
	 *
	 * @param array $arParams Параметры запроса к базе данных
	 * @param bool  $showSql  Показать SQL запрос, вместо выборки (для отладки)
	 *
	 * @return array|bool Массив значений таблицы, массив с SQL запросом, либо в случае неудачи false
	 */
	final public static function getList ($arParams=array(),$showSql=false)
	{
		$query = new Db\Query\QuerySelect(static::getClassName(),$arParams);
		if ($showSql)
		{
			return $query->getSql();
		}

		$res = $query->exec();
		$arResult = array();
		while ($ar_res = $res->fetch())
		{
			$arResult[] = $ar_res;
		}

		if (!empty($arResult))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Обертка для вызова функции getList с произвольными параметрами
	 * @see static::getList
	 *
	 * @api
	 *
	 * @return array|bool
	 */
	final public static function getListFunc ()
	{
		try
		{
			if (func_num_args() <= 0)
			{
				throw new Exception\ArgumentNullException('params');
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			$e->showException();
			return false;
		}
		$params = func_get_arg(0);

		return static::getList($params[0]);
	}
}
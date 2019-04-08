<?php
/**
 * Ms\Core\Lib\DataManager
 * Используется для описания и обработки таблиц базы данных.
 * Наследуется в классах описания таблиц ядра и модулей
 *
 * @package Ms\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @since 0.1.0
 */

namespace Ms\Core\Lib;

use Ms\Core\Exception;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Db;
use Ms\Core\Entity\Db\DBResult;

abstract class DataManager
{
	/**
	 * Возвращает имя текущего класса
	 *
	 * @api
	 *
	 * @return string Имя класса
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_class_name
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
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_table_name
	 */
	final public static function getTableName()
	{
		//Разбираем Brand\ModuleName\Tables\NameTable
		$arClass = explode('\\',static::getClassName());
		//Сохраняем Brand
		$name = strtolower($arClass[0]).'_';
		//Сохраняем ModuleName
		$name .= Tools::camelCaseToUnderscore($arClass[1]).'_';
		//Сохраняет NameTable
		$table = Tools::camelCaseToUnderscore($arClass[3]);
		//Удаляем _table
		$name .= str_replace('_table','',$table);

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
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_table_title
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
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_map
	 */
	abstract protected static function getMap();

	/**
	 * Возвращает обработанный массив сущностей полей таблицы базы данных.
	 * Обрабатывает массив, полученный функцией getMap
	 *
	 * @api
	 *
	 * @return Fields\ScalarField[] Обработанный массив сущностей полей таблицы базы данных
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_map_array
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
	 * Задает множественный первичный ключ для таблицы
	 *
	 * @api
	 *
	 * @return null|array
	 * @since 0.2.0
	 */
	public static function getPrimary ()
	{
		return NULL;
	}

	/**
	 * Возвращает массив дефолтных значений таблицы,
	 * которые добавляются в таблицу при установке ядра или модуля
	 *
	 * @api
	 *
	 * @return array Массив дефолтных значений таблицы
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_values
	 */
	public static function getValues ()
	{
		return array();
	}

	/**
	 * Возвращает массив описывающий связанные с таблицей другие таблицы
	 * и объединяющие их поля
	 *
	 * @api
	 *
	 * @return array Массив связей таблиц
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_table_links
	 */
	public static function getTableLinks ()
	{
		return array();
	}

	/**
	 * Возвращает дополнительный SQL запрос, используемый после создания таблицы
	 *
	 * @return null|string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_additional_create_sql
	 */
	public static function getAdditionalCreateSql ()
	{
		return null;
	}

	/**
	 * Возвращает SQL запрос создания триггера для таблицы
	 *
	 * @param string $sAction SQL код действий триггера
	 * @param string $sTime   Время срабатывания триггера ('BEFORE' или 'AFTER')
	 * @param string $sEvent  Событие срабатывания триггера ('INSERT', 'UPDATE' или 'DELETE')
	 *
	 * @return string SQL код создания триггера или пустая строка в случае ошибки
	 */
	final public static function addTriggerSql ($sAction, $sTime='before',$sEvent='update')
	{
		$sTime = strtoupper($sTime);
		$sEvent = strtoupper($sEvent);
		if (
			!in_array($sTime,array('BEFORE','AFTER'))
			|| !in_array($sEvent,array ('INSERT','UPDATE','DELETE'))
		) {
			return null;
		}
		$helper = new Db\SqlHelper(static::getTableName());
		$sql = '
			DROP TRIGGER IF EXISTS '.$helper->wrapQuotes(static::getTableName().'_'.strtolower($sTime).'_'.strtolower($sEvent)).';
			DELIMITER |
			CREATE TRIGGER '.$helper->wrapQuotes(static::getTableName().'_'.strtolower($sTime).'_'.strtolower($sEvent)).' 
			'.$sTime.' '.$sEvent.' ON '.$helper->wrapTableQuotes().' FOR EACH ROW
			BEGIN 
				'.$sAction.'
			END;
			DELIMITER ;
		';

		return $sql;
	}

	/**
	 * Удаляет триггер для таблицы
	 *
	 * @param string $sTime  Время срабатывания триггера ('BEFORE' или 'AFTER')
	 * @param string $sEvent Событие срабатывания триггера ('INSERT', 'UPDATE' или 'DELETE')
	 *
	 * @return string SQL код удаления триггера или пустая строка в случае ошибки
	 */
	final public static function deleteTriggerSql($sTime='before',$sEvent='update')
	{
		$sTime = strtolower($sTime);
		$sEvent = strtolower($sEvent);
		if (
			!in_array($sTime,array ('before','after'))
			||!in_array($sEvent,array ('insert','update','delete'))
		) {
			return null;
		}
		$helper = new Db\SqlHelper();

		return 'DROP TRIGGER IF EXISTS '.$helper->wrapQuotes(static::getTableName().'_'.$sTime.'_'.$sEvent).';';
	}

	/**
	 * Возвращает дополнительный SQL запрос, используемый после удаления таблицы
	 *
	 * @return null|string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_additional_delete_sql
	 */
	public static function getAdditionalDeleteSql ()
	{
		return null;
	}

	/**
	 * Возвращает SQL код, вставляемый при создании таблицы.
	 * Можно использовать для добавления индексов
	 *
	 * @return null|string
	 * @since 0.2.0
	 */
	public static function getInnerCreateSql ()
	{
		return null;
	}

	/**
	 * Добавляет один или несколько одиночных индексов в таблицу
	 *
	 * @param string|array $mFields Один или несколько полей для каждого из которых создаются индексы
	 *
	 * @return null|string
	 * @since 0.2.0
	 */
	public static function addIndexes ($mFields)
	{
		if (!is_array($mFields))
		{
			$mFields = array($mFields);
		}
		elseif (empty($mFields))
		{
			return null;
		}
		$sql = '';

		foreach ($mFields as $sFieldName)
		{
			$helper = new Db\SqlHelper();
			$sFieldName = strtoupper($sFieldName);
			$sql .= 'INDEX '.$helper->wrapQuotes('INDEX_'.$sFieldName)
				.' ('.$helper->wrapQuotes($sFieldName).")\n\t";
		}

		if (strlen($sql)>0)
		{
			return $sql;
		}

		return null;
	}

	/**
	 * Возвращает SQL код уникального поля/полей
	 *
	 * @param string|array $mFields Поле/поля уникальные для таблицы
	 * @param bool         $bIndex  Флаг, создавать индекс
	 * @param null|string  $sName   Имя унимального (индекса), если не задан, создасться автоматически
	 *
	 * @return null|string
	 */
	final public static function addUnique($mFields,$bIndex=false,$sName=null)
	{
		if (!is_array($mFields)&&strlen($mFields)>0)
		{
			$mFields = array ($mFields);
		}
		elseif (!is_array($mFields))
		{
			return null;
		}
		$helper = new Db\SqlHelper();
		$sql = 'UNIQUE ';
		if ($bIndex)
		{
			$sql .= 'INDEX ';
		}
		if (is_null($sName))
		{
			$sName = 'UNIQUE';
			if ($bIndex)
			{
				$sName .= '_INDEX';
			}
			foreach ($mFields as $field)
			{
				$sName .= '_'.$field;
			}
			$sName = strtolower($sName);
		}
		$sql .= $helper->wrapQuotes($sName).' (';
		$bFirst = true;
		foreach ($mFields as $field)
		{
			if ($bFirst)
			{
				$bFirst = false;
			}
			else
			{
				$sql .= ',';
			}
			$sql .= $helper->wrapQuotes($field);
		}
		$sql .= ')';

		return $sql;
	}

	/**
	 * Обработчик события перед добавлением новой записи в таблицу
	 *
	 * @param array $arAdd Массив полей таблицы
	 * @since 0.2.0
	 */
	protected static function OnBeforeAdd ($arAdd) {}

	/**
	 * Обработчик события после попытки добавления новой записи в таблицу
	 *
	 * @param array    $arAdd Массив полей таблицы
	 * @param DBResult $res   Результат выполнения запроса
	 * @since 0.2.0
	 */
	protected static function OnAfterAdd ($arAdd, $res) {}

	/**
	 * Обработчик события перед обновлением записи в таблице
	 *
	 * @param mixed       $primary    Значение PRIMARY поля таблицы
	 * @param array       &$arUpdate  Массив обновляемых полей записи, можно изменить
	 * @param null|string $sSqlWhere  SQL запроса WHERE
	 *
	 * @return mixed|false Для отмены обновления необходимо передать FALSE
	 *
	 * @since 0.2.0
	 */
	protected static function OnBeforeUpdate ($primary, &$arUpdate, &$sSqlWhere=null) {}

	/**
	 * Обработчик события после попытки обновления записи в таблице
	 *
	 * @param mixed    $primary  Значение поля PRIMARY таблицы
	 * @param array    $arUpdate Массив обновляемых полей таблицы
	 * @param DBResult $res      Результат выполнения запроса
	 * @since 0.2.0
	 */
	protected static function OnAfterUpdate ($primary, $arUpdate, $res) {}

	/**
	 * Обработчки события перед удалением записи из таблицы
	 *
	 * @param mixed $primary Значение PRIMARY поля таблицы
	 * @param bool  $confirm Флаг подтверждения удаления связанных записей
	 * @since 0.2.0
	 */
	protected static function OnBeforeDelete ($primary, $confirm) {}

	/**
	 * Обработчки события после попытки удаления записи из таблицы
	 *
	 * @param mixed    $primary Значение PRIMARY поля таблицы
	 * @param bool     $confirm Флаг подтверждения удаления связанных записей
	 * @param DBResult $res     Результат выполнения запроса
	 * @since 0.2.0
	 */
	protected static function OnAfterDelete ($primary, $confirm, $res) {}

	/**
	 * Добавляет значения в таблицу
	 *
	 * @param array $arAdd      Массив содержащий значения таблицы
	 * @param bool  $bShowSql   Необходимость отобразить sql запрос вместо запроса
	 *
	 * @return Db\DBResult|string Результат mysql запроса, либо сам текст запроса
	 * @throws
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_add
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
			//Обрабатываем событие перед добавлением записи
			static::OnBeforeAdd($arAdd);

			$res = $query->exec();

			//Обрабатываем событие после попытки добавления записи
			static::OnAfterAdd($arAdd,$res);

			return $res;
		}
	}

	/**
	 * Обновляет значения в таблице
	 * TODO: Переделать primary на возможность добавлять массив полей со значениями
	 *
	 * @param mixed  $primary   Поле PRIMARY таблицы
	 * @param array  $arUpdate  Массив значений таблицы в поле 'VALUES'
	 * @param bool   $bShowSql  Флаг, показать SQL запрос вместо выполнения
	 * @param string $sSqlWhere SQL код WHERE, если нужно обновить не по primary полю @since 0.2.0
	 *
	 * @return Db\DBResult|string|false Результат mysql запроса, либо текст запроса, либо false
	 * @throws
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_update
	 */
	final public static function update ($primary, $arUpdate, $bShowSql=false, $sSqlWhere=null)
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

		//Обрабатываем событие перед обновлением записи
		$bNext = static::OnBeforeUpdate($primary,$arUpdate, $sSqlWhere);
		if ($bNext === FALSE)
		{
			return FALSE;
		}

		$query = new Db\Query\QueryUpdate($primary,$arUpdate,static::getClassName(),$sSqlWhere);

		if ($bShowSql)
		{
			return $query->getSql();
		}

		$res = $query->exec();

		//Обрабатываем событие после попытки обновления записи
		static::OnAfterUpdate($primary,$arUpdate,$res);

		return $res;
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
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_delete
	 */
	final public static function delete ($primary, $confirm=false)
	{
		$query = new Db\Query\QueryDelete($primary,$confirm,static::getClassName());

		//Обрабатываем событие перед удалением записи из таблицы
		static::OnBeforeDelete($primary,$confirm);

		$res = $query->exec();

		//Обрабатываем событие после попытки удаления записи из таблицы
		static::OnAfterDelete($primary, $confirm, $res);

		return $res;
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
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_by_primary
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
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_by_id
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
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_primary_field
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
	 * Функция добавляет в таблицу значения по-умолчанию, описанные в файле таблицы
	 *
	 * @api
	 *
	 * @param bool $bShowSql Флаг, показывать SQL запрос, вместо выполнения
	 *
	 * @return bool|Db\DBResult Результат mysql запроса, либо false
	 * @throws
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_insert_default_rows
	 */
	final public static function insertDefaultRows ($bShowSql=false)
	{
		$arDefaultValues = static::getValues();
		if (count($arDefaultValues)>0)
		{
			$query = new Db\Query\QueryInsert($arDefaultValues,static::getClassName());
			if ($bShowSql)
			{
				msEchoVar($query->getSql());
				return false;
			}
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
	 * @param bool $bShowSql Флаг, показывать SQL вместо выполнения
	 *
	 * @return Db\DBResult Результат mysql запроса
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_create_table
	 */
	final public static function createTable ($bShowSql=false)
	{
		$query = new Db\Query\QueryCreate(static::getClassName());
		if ($bShowSql)
		{
			msEchoVar($query->getSql());
			$res = '';
		}
		else
		{
			$res = $query->exec();
		}
		if ($bShowSql||$res->getResult())
		{
			$additionalSql = static::getAdditionalCreateSql();
			if (!$bShowSql&&!is_null($additionalSql))
			{
				$query = new Db\Query\QueryBase($additionalSql);
				$query->exec();

				static::OnAfterCreateTable();
			}
			else
			{
				msEchoVar($additionalSql);
			}
		}

		return $res;
	}

	/**
	 * Удаляет таблицу из БД. Возвращает TRUE, если табилца успешно удалена, иначе FALSE
	 *
	 * @return bool
	 */
	final public static function dropTable ()
	{
		$query = new Db\Query\QueryDrop(static::getClassName());

		$res = Events::runEvents('core','OnBeforeDropTable',array (static::getClassName()));
		if ($res === false)
		{
			return false;
		}

		if (!static::OnBeforeDropTable())
		{
			return false;
		}
		$res = $query->exec();

		return ($res->getResult()) ? TRUE : FALSE;
	}

	/**
	 * Метод вызываемый перед удалением таблицы
	 *
	 * @return bool
	 */
	public static function OnBeforeDropTable ()
	{
		return true;
	}

	/**
	 * Функция проверяет описанные связи таблицы, используя запросы к DB
	 *
	 * @api
	 *
	 * @return bool Связи существуют - true, инае - false
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_check_table_links
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
	 *
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_on_after_create_table
	 */
	public static function OnAfterCreateTable (){}

	/**
	 * Осуществляет выборку из таблицы и возвращает 1 запись
	 *
	 * @param array $arParams       Параметры getList
	 * @param bool  $showSql        Флаг - показать SQL запрос вместо выборки
	 *
	 * @return array|string|bool    Массив полей записи, SQL-запрос, либо false
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_one
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
	 * @throws
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/data_manager/method_get_list
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



	/**
	 * Заглушка, чтобы не использовали этот метод
	 */
	final private static function OnBeforeInsert ()
	{
		try
		{
			throw new Exception\NotSupportedException('Method OnBeforeInsert not supported. Use OnBeforeAdd');
		}
		catch (Exception\NotSupportedException $e)
		{
			die($e->showException());
		}
	}

	/**
	 * Заглушка, чтобы не использовали этот метод
	 */
	final private static function OnAfterInsert ()
	{
		try
		{
			throw new Exception\NotSupportedException('Method OnAfterInsert not supported. Use OnAfterAdd');
		}
		catch (Exception\NotSupportedException $e)
		{
			die($e->showException());
		}
	}

}
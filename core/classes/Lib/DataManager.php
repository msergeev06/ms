<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Db;
use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\System\Application;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\Classes\ObjectNotFoundException;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Exceptions\Db\ValidateException;
use Ms\Core\Exceptions\NotSupportedException;
use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Lib\DataManager
 * Используется для описания и обработки таблиц базы данных.
 * Наследуется в классах описания таблиц ядра и модулей
 */
abstract class DataManager
{
    protected static $arExpansionTableName = [];
    protected static $sBaseName = '';

    /**
     * Возвращает имя текущего класса
     *
     * @return string Имя класса
     */
    final public static function getClassName()
    {
        return get_called_class();
    }

    /**
     * Генерирует и возвращает название таблицы в базе
     *
     * @return string название таблицы в базе
     * @example 'ms_core_options'
     *
     */
    final public static function getTableName()
    {
        //Получаем базовое имя таблицы
        $name = static::getTableBaseName();
        //Добавляем расширение имени таблицы, если оно задано
        $name .= static::getExpansionTableName($name);

        return $name;
    }

    /**
     * Генерирует и возвращает базовое название таблицы в БД
     *
     * @return string
     */
    final protected static function getTableBaseName()
    {
        //Разбираем Brand\ModuleName\Tables\NameTable
        $arClass = explode('\\', static::getClassName());
        //Сохраняем Brand
        $name = strtolower($arClass[0]) . '_';
        //Сохраняем ModuleName
        $name .= Application::getInstance()->convertPascalCaseToSnakeCase($arClass[1]) . '_';
        //Сохраняет NameTable
        $table = Application::getInstance()->convertPascalCaseToSnakeCase($arClass[3]);
        //Удаляем _table
        $name .= str_replace('_table', '', $table);

        return $name;
    }

    /**
     * Добавляет расширение имени таблицы
     *
     * @param string $sName Расширенное имя таблицы
     *
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    final public static function setExpansionTableName($sName)
    {
        $baseName = static::getTableBaseName();
        if (!isset($sName) || strlen((string)$sName) <= 0)
        {
            throw new ArgumentNullException('sName');
        }
        elseif (strlen((string)$sName) > 42)
        {
            throw new ArgumentOutOfRangeException('sName', 1, 42);
        }
        else
        {
            static::$arExpansionTableName[$baseName] = strtolower($sName);
        }
    }

    /**
     * Возвращает расширение имени таблицы, если оно задано
     *
     * @param bool $bAddPrefix Добавлять ли перед дополнительным именем префикс '_' (знак подчеркивания)
     *
     * @return string
     */
    final public static function getExpansionTableName(bool $bAddPrefix = true)
    {
        $baseName = static::getTableBaseName();
        if (array_key_exists($baseName, self::$arExpansionTableName))
        {
            if (
                strlen((string)self::$arExpansionTableName[$baseName]) > 1
                && strlen(
                       (string)self::$arExpansionTableName[$baseName]
                   ) <= 42
            )
            {
                if ($bAddPrefix)
                {
                    return '_' . self::$arExpansionTableName[$baseName];
                }
                else
                {
                    return self::$arExpansionTableName[$baseName];
                }
            }
        }

        return '';
    }

    /**
     * Сбрасывает (очищает) расширение имени таблицы
     */
    final public static function clearExpansionTableName()
    {
        $baseName = static::getTableBaseName();
        self::$arExpansionTableName[$baseName] = '';
    }

    /**
     * Возвращает описание таблицы
     *
     * @return string Текст описания таблицы
     * @example 'Опции'
     *
     */
    public static function getTableTitle()
    {
        return '';
    }

    /**
     * Возвращает массив сущностей полей таблицы базы данных.
     * Не рекомендуется использовать в API. Используйте getMapArray
     *
     * @return Fields\ScalarFieldAbstract[] Массив сущностей полей таблицы базы данных
     * @see static::getMapArray
     *
     */
    abstract protected static function getMap();

    /**
     * Возвращает обработанный массив сущностей полей таблицы базы данных.
     * Обрабатывает массив, полученный функцией getMap
     *
     * @return Fields\ScalarFieldAbstract[] Обработанный массив сущностей полей таблицы базы данных
     */
    final public static function getMapArray()
    {
        $arMap = static::getMap();
        $arMapArray = [];
        foreach ($arMap as $id => $field)
        {
            $name = $field->getColumnName();
            $arMapArray[$name] = $field;
        }

        return $arMapArray;
    }

    /**
     * Возвращает объект primary поля
     *
     * @return false|Fields\ScalarFieldAbstract
     */
    public static function getPrimaryField()
    {
        $arMap = static::getMap();
        foreach ($arMap as $field)
        {
            if ($field->isPrimary())
            {
                return $field;
            }
        }

        return false;
    }

    /**
     * Возвращает массив дефолтных значений таблицы,
     * которые добавляются в таблицу при установке ядра или модуля
     *
     * @return array Массив дефолтных значений таблицы
     */
    public static function getValues()
    {
        return [];
    }

    /**
     * Возвращает дополнительный SQL запрос, используемый после создания таблицы
     *
     * @return null|string
     */
    public static function getAdditionalCreateSql()
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
    final public static function addTriggerSql($sAction, $sTime = 'before', $sEvent = 'update')
    {
        $sTime = strtoupper($sTime);
        $sEvent = strtoupper($sEvent);
        if (
            !in_array($sTime, ['BEFORE', 'AFTER'])
            || !in_array($sEvent, ['INSERT', 'UPDATE', 'DELETE'])
        )
        {
            return null;
        }
        $helper = new Db\SqlHelper(static::getTableName());
        $sql = '
			DROP TRIGGER IF EXISTS ' . $helper->wrapQuotes(
                static::getTableName() . '_' . strtolower($sTime) . '_' . strtolower($sEvent)
            ) . ';
			DELIMITER |
			CREATE TRIGGER ' . $helper->wrapQuotes(
                static::getTableName() . '_' . strtolower($sTime) . '_' . strtolower($sEvent)
            ) . ' 
			' . $sTime . ' ' . $sEvent . ' ON ' . $helper->wrapTableQuotes() . ' FOR EACH ROW
			BEGIN 
				' . $sAction . '
			END;
			DELIMITER ;
		';

        return $sql;
    }

    /**
     * Возвращает SQL запрос удаления триггера для таблицы
     *
     * @param string $sTime  Время срабатывания триггера ('BEFORE' или 'AFTER')
     * @param string $sEvent Событие срабатывания триггера ('INSERT', 'UPDATE' или 'DELETE')
     *
     * @return string SQL код удаления триггера или пустая строка в случае ошибки
     */
    final public static function deleteTriggerSql($sTime = 'before', $sEvent = 'update')
    {
        $sTime = strtolower($sTime);
        $sEvent = strtolower($sEvent);
        if (
            !in_array($sTime, ['before', 'after'])
            || !in_array($sEvent, ['insert', 'update', 'delete'])
        )
        {
            return null;
        }
        $helper = new Db\SqlHelper();

        return 'DROP TRIGGER IF EXISTS ' . $helper->wrapQuotes(static::getTableName() . '_' . $sTime . '_' . $sEvent)
               . ';';
    }

    /**
     * Возвращает дополнительный SQL запрос, используемый после удаления таблицы
     *
     * @return null|string
     */
    public static function getAdditionalDeleteSql()
    {
        return null;
    }

    /**
     * Возвращает SQL код, вставляемый при создании таблицы.
     * Можно использовать для добавления индексов
     *
     * @return null|string
     *
     */
    public static function getInnerCreateSql()
    {
        return null;
    }

    /**
     * Добавляет один или несколько одиночных индексов в таблицу
     *
     * @param string|array $mFields Один или несколько полей для каждого из которых создаются индексы
     *
     * @return null|string
     *
     */
    public static function addIndexes($mFields)
    {
        if (!is_array($mFields))
        {
            $mFields = [$mFields];
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
            $sql .= 'INDEX ' . $helper->wrapQuotes('INDEX_' . $sFieldName)
                    . ' (' . $helper->wrapQuotes($sFieldName) . ")\n\t";
        }

        if (strlen($sql) > 0)
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
    final public static function addUnique($mFields, $bIndex = false, $sName = null)
    {
        if (!is_array($mFields) && strlen($mFields) > 0)
        {
            $mFields = [$mFields];
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
                $sName .= '_' . $field;
            }
            $sName = strtolower($sName);
        }
        $sql .= $helper->wrapQuotes($sName) . ' (';
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
     * @param array &$arAdd Массив полей таблицы
     *
     * @return mixed|false Для отмены добавления необходимо передать FALSE
     *
     */
    protected static function OnBeforeAdd(&$arAdd)
    {
        return true;
    }

    /**
     * Обработчик события после попытки добавления новой записи в таблицу
     *
     * @param array    $arAdd Массив полей таблицы
     * @param DBResult $res   Результат выполнения запроса
     *
     */
    protected static function OnAfterAdd($arAdd, $res)
    {
    }

    /**
     * Обработчик события перед обновлением записи в таблице
     *
     * @param mixed        $primary   Значение PRIMARY поля таблицы
     * @param array       &$arUpdate  Массив обновляемых полей записи, можно изменить
     * @param null|string  $sSqlWhere SQL запроса WHERE
     *
     * @return mixed|false Для отмены обновления необходимо передать FALSE
     *
     *
     */
    protected static function OnBeforeUpdate($primary, &$arUpdate, &$sSqlWhere = null)
    {
        return true;
    }

    /**
     * Обработчик события после попытки обновления записи в таблице
     *
     * @param mixed    $primary  Значение поля PRIMARY таблицы
     * @param array    $arUpdate Массив обновляемых полей таблицы
     * @param DBResult $res      Результат выполнения запроса
     *
     */
    protected static function OnAfterUpdate($primary, $arUpdate, $res)
    {
    }

    /**
     * Обработчки события перед удалением записи из таблицы
     *
     * @param mixed $primary Значение PRIMARY поля таблицы
     *
     * @return mixed|false Для отмены удаления необходимо передать FALSE
     *
     */
    protected static function OnBeforeDelete($primary)
    {
        return true;
    }

    /**
     * Обработчки события после попытки удаления записи из таблицы
     *
     * @param mixed    $primary Значение PRIMARY поля таблицы
     * @param DBResult $res     Результат выполнения запроса
     *
     */
    protected static function OnAfterDelete($primary, $res)
    {
    }

    /**
     * Валидирует поля таблицы
     *
     * @param string                          $sFieldName Имя поля таблицы
     * @param mixed                           $mValue     Полученное значение поля таблицы
     * @param string                          $actionType Тип действия (insert, update)
     * @param null|Fields\ScalarFieldAbstract $obField    Сущность поля таблицы
     *
     * @return bool
     * @throws ValidateException
     */
    public static function validateFields($sFieldName, &$mValue, $actionType = 'insert', $obField = null)
    {
        if (!is_null($obField))
        {
            if ($obField->isRequired())
            {
                //Если поле обязательно и если есть список допустимых значений, проверяем их
                $arAllowedValues = $obField->getAllowedValues();
                $arAllowedValuesRange = $obField->getAllowedValuesRange();
                if (!is_null($arAllowedValues))
                {
                    if (in_array($mValue, $arAllowedValues))
                    {
                        return true;
                    }
                    else
                    {
                        throw new ValidateException(
                            $sFieldName,
                            $mValue,
                            $arAllowedValues
                        );
                    }
                }
                elseif (
                    !is_null($arAllowedValuesRange)
                    && array_key_exists('min', $arAllowedValuesRange)
                    && array_key_exists('max', $arAllowedValuesRange)
                )
                {
                    if ($mValue >= $arAllowedValuesRange['min'] && $mValue <= $arAllowedValuesRange['max'])
                    {
                        return true;
                    }
                    else
                    {
                        throw new ValidateException(
                            $sFieldName,
                            $mValue,
                            $arAllowedValuesRange
                        );
                    }
                }
            }

            //Проверяем типы данных
            if ($obField instanceof Fields\BooleanField)
            {
                if (is_bool($mValue))
                {
                    return true;
                }
                elseif (is_bool($obField->normalizeValue($mValue)))
                {
                    return true;
                }
                elseif (!is_null($obField->getDefaultValue($actionType)))
                {
                    return true;
                }
                else
                {
                    throw new ValidateException(
                        $sFieldName,
                        $mValue,
                        null,
                        'Тип поля boolean, значение другого типа'
                    );
                }
            }

            //Проверяем обязательные значения
            if ($obField->isRequired())
            {
                if (is_null($mValue))
                {
                    if ($obField->isRequiredNull())
                    {
                        return true;
                    }
                    elseif ($obField->isAutocomplete())
                    {
                        return true;
                    }
                    elseif (!is_null($obField->getDefaultValue($actionType)))
                    {
                        return true;
                    }
                    else
                    {
                        throw new ValidateException(
                            $sFieldName,
                            $mValue,
                            null,
                            'Передано значение null, однако поле не может быть null, не является автозаполняемым и не имеет значения по-умолчанию'
                        );
                    }
                }
                else
                {
                    return true;
                }
            }
        }

        return true;
    }

    /**
     * Добавляет значения в таблицу
     *
     * @param array $arAdd    Массив содержащий значения таблицы
     * @param bool  $bShowSql Необходимость отобразить sql запрос вместо запроса
     *
     * @return DBResult|string Результат mysql запроса, либо сам текст запроса
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     * @throws ValidateException
     * @throws SqlQueryException
     */
    final public static function add($arAdd, $bShowSql = false)
    {
        //Валидация полей
        $arMap = static::getMapArray();
        if (!empty($arMap))
        {
            if (isset($arAdd[0]) && is_array($arAdd[0]))
            {
                //обрабатываем массив массивов
                for ($i = 0; $i < count($arAdd); $i++)
                {
                    foreach ($arMap as $fieldName => $obField)
                    {
                        static::validateFields($fieldName, $arAdd[$i][$fieldName], 'insert', $obField);
                    }
                }
            }
            else
            {
                foreach ($arMap as $fieldName => $obField)
                {
                    static::validateFields($fieldName, $arAdd[$fieldName], 'insert', $obField);
                }
            }
        }

        $query = new Db\Query\QueryInsert($arAdd, static::getClassName());

        //Обрабатываем системное событие перед добавлением записи
        $bAdd = Events::runEvents('core', 'OnBeforeInsert', [static::getClassName(), &$arAdd]);

        if ($bAdd === false)
        {
            return new DBResult();
        }

        //Обрабатываем событие таблицы перед добавлением записи
        $bAdd = static::OnBeforeAdd($arAdd);

        if ($bAdd === false)
        {
            return new DBResult();
        }

        $res = $query->exec($bShowSql);
        if ($bShowSql)
        {
            return $res;
        }

        //Обрабатываем событие таблицы после попытки добавления записи
        static::OnAfterAdd($arAdd, $res);

        //Обрабатываем системное событие после попытки добавления записи
        Events::runEvents('core', 'OnAfterInsert', [static::getClassName(), $arAdd, $res]);

        return $res;
    }

    /**
     * Обновляет значения в таблице
     * TODO: Переделать primary на возможность добавлять массив полей со значениями
     *
     * @param mixed  $primary   Поле PRIMARY таблицы
     * @param array  $arUpdate  Массив значений таблицы в поле 'VALUES'
     * @param bool   $bShowSql  Флаг, показать SQL запрос вместо выполнения
     * @param string $sSqlWhere SQL код WHERE, если нужно обновить не по primary полю
     *
     * @return DBResult|string Результат mysql запроса, либо текст запроса
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ArgumentTypeException
     * @throws SqlQueryException
     * @throws ValidateException
     * @throws ObjectNotFoundException
     */
    final public static function update($primary, $arUpdate, $bShowSql = false, $sSqlWhere = null)
    {
        //Валидация полей
        $bValidate = true;
        $arMap = static::getMapArray();
        if (!empty($arUpdate))
        {
            foreach ($arUpdate as $fieldName => &$value)
            {
                $bValidate = static::validateFields(
                    $fieldName,
                    $value,
                    'update',
                    $arMap[$fieldName]
                );
            }
            unset($value);
        }
        if (!$bValidate)
        {
            return new DBResult();
        }

        //Обрабатываем системное событие перед обновлением записи в таблице
        $bNext = Events::runEvents(
            'core',
            'OnBeforeUpdate',
            [static::getClassName(), $primary, &$arUpdate, &$sSqlWhere]
        );
        if ($bNext === false)
        {
            return new DBResult();
        }

        //Обрабатываем событие таблицы перед обновлением записи
        $bNext = static::OnBeforeUpdate($primary, $arUpdate, $sSqlWhere);
        if ($bNext === false)
        {
            return new DBResult();
        }

        $query = new Db\Query\QueryUpdate($primary, $arUpdate, static::getClassName(), $sSqlWhere);

        if ($bShowSql)
        {
            return $query->getSql();
        }

        $res = $query->exec();

        //Обрабатываем событие таблицы после попытки обновления записи
        static::OnAfterUpdate($primary, $arUpdate, $res);

        //Обрабатываем системное событие таблицы после попытки обновления записи
        Events::runEvents(
            'core',
            'OnAfterUpdate',
            [static::getClassName(), $primary, $arUpdate, $res]
        );

        return $res;
    }

    /**
     * Удаляет запись из таблицы
     *
     * @param mixed $primary Поле PRIMARY таблицы
     *
     * @return DBResult Результат mysql запроса
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     * @throws SqlQueryException
     */
    final public static function delete($primary)
    {
        //Обрабатываем системное событие перед удалением записи из таблицы
        $bDelete = Events::runEvents('core', 'OnBeforeDelete', [static::getClassName(), $primary]);
        if ($bDelete === false)
        {
            return new DBResult();
        }

        //Обрабатываем событие таблицы перед удалением записи из таблицы
        $bDelete = static::OnBeforeDelete($primary);
        if ($bDelete === false)
        {
            return new DBResult();
        }

        $query = new Db\Query\QueryDelete($primary, static::getClassName());

        $res = $query->exec();

        //Обрабатываем событие таблицы после попытки удаления записи из таблицы
        static::OnAfterDelete($primary, $res);

        //Обрабатываем системное событие после попытки удаления записи из таблицы
        Events::runEvents('core', 'OnAfterDelete', [static::getClassName(), $primary, $res]);

        return $res;
    }

    /**
     * Возвращает запись по PRIMARY ключу
     *
     * @param mixed  $primaryValue Значение PRIMARY поля
     * @param string $primaryName  Имя PRIMARY поля
     * @param array  $arSelect     Список возвращаемых полей
     * @param bool   $showSql      Флаг - показать SQL запрос вместо выборки
     *
     * @return array|false
     */
    final public static function getByPrimary (
        $primaryValue,
        string $primaryName = null,
        array $arSelect = [],
        bool $showSql = false
    ) {
        if (is_null($primaryName) || strlen($primaryName) < 1)
        {
            $primaryName = static::getPrimaryFieldName();
        }
        $arList['filter'] = [$primaryName => $primaryValue];
        if (!empty($arSelect))
        {
            $arList['select'] = $arSelect;
        }
        try
        {
            $arRes = static::getOne($arList, $showSql);
        }
        catch (SystemException $e)
        {
            return false;
        }

        return $arRes;
    }

    /**
     * Возвращает запись по ID
     *
     * @param int   $id       Значение поля ID
     * @param array $arSelect Список возвращаемых полей
     * @param bool  $showSql  Флаг - показать SQL запрос вместо выборки
     *
     * @return array
     */
    final public static function getById(int $id, array $arSelect = [], $showSql = false)
    {
        return static::getByPrimary ($id, 'ID', $arSelect, $showSql);
    }

    /**
     * Возвращает первое поле PRIMARY таблицы
     *
     * @return string|bool Название поля, либо false
     */
    final public static function getPrimaryFieldName ()
    {
        $arMap = static::getMap();
        foreach ($arMap as $field)
        {
            if ($field->isPrimary())
            {
                return $field->getColumnName();
            }
        }

        return false;
    }

    /**
     * Функция добавляет в таблицу значения по-умолчанию, описанные в файле таблицы
     *
     * @param bool $bShowSql Флаг, показывать SQL запрос, вместо выполнения
     *
     * @return bool|DBResult Результат mysql запроса, либо false
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     * @throws SqlQueryException
     */
    final public static function insertDefaultRows ($bShowSql = false)
    {
        $arDefaultValues = static::getValues();
        if (count($arDefaultValues) > 0)
        {
            $query = new Db\Query\QueryInsert($arDefaultValues, static::getClassName());
            if ($bShowSql)
            {
                return $query->getSql();
            }
            $res = $query->exec();

            return $res;
        }
        else
        {
            return false;
        }
    }

    /**
     * Функция создает таблицу
     *
     * @param bool $bShowSql Флаг, показывать SQL вместо выполнения
     *
     * @return DBResult|string Результат mysql запроса
     * @throws SqlQueryException
     */
    final public static function createTable($bShowSql = false)
    {
        $query = new Db\Query\QueryCreate(static::getClassName());
        if ($bShowSql)
        {
            return $query->getSql() . "\n" . (string)static::getAdditionalCreateSql();
        }

        $res = $query->exec();

        if ($res->isSuccess())
        {
            $additionalSql = static::getAdditionalCreateSql();
            if (!empty($additionalSql))
            {
                $query = new Db\Query\QueryBase($additionalSql);
                $query->exec();

                static::OnAfterCreateTable();
            }
        }

        return $res;
    }

    /**
     * Удаляет таблицу из БД. Возвращает TRUE, если табилца успешно удалена, иначе FALSE
     *
     * @param bool $bIgnoreForeignKeys Флаг, означающий необходимо игнорировать ограничения внешних ключей
     *
     * @return DBResult
     * @throws SqlQueryException
     */
    final public static function dropTable($bIgnoreForeignKeys = false)
    {
        $query = new Db\Query\QueryDrop(static::getClassName(), $bIgnoreForeignKeys);

        $res = Events::runEvents(
            'core',
            'OnBeforeDropTable',
            [static::getClassName(), $bIgnoreForeignKeys]
        );
        if ($res === false)
        {
            return new DBResult();
        }

        if (!static::OnBeforeDropTable())
        {
            return new DBResult();
        }

        $res = $query->exec();

        return $res;
    }

    /**
     * Метод вызываемый перед удалением таблицы
     *
     * @return bool
     */
    public static function OnBeforeDropTable()
    {
        return true;
    }

    /**
     * Вызывается после создания таблицы
     *
     * @return bool
     */
    public static function OnAfterCreateTable()
    {
        return true;
    }

    /**
     * Осуществляет выборку из таблицы и возвращает 1 запись
     *
     * @param array $arParams Параметры getList
     * @param bool  $showSql  Флаг - показать SQL запрос вместо выборки
     *
     * @return array|string|bool    Массив полей записи, SQL-запрос, либо false
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     * @throws SqlQueryException
     */
    final public static function getOne($arParams = [], $showSql = false)
    {
        $arParams['limit'] = 1;
        $arRes = static::getList($arParams, $showSql);
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
     * Возвращает количество записей в таблице
     *
     * @return int
     * @throws SqlQueryException
     */
    final public static function count()
    {
        $helper = new Db\SqlHelper(static::getTableName());

        $sql = 'SELECT COUNT(*) as ' . $helper->wrapFieldQuotes('COUNT') . ' FROM ' . $helper->wrapTableQuotes();
        $query = new Db\Query\QueryBase($sql);

        $res = $query->exec();
        if ($ar_res = $res->fetch())
        {
            if (isset($ar_res['COUNT']) && (int)$ar_res['COUNT'] > 0)
            {
                return $ar_res['COUNT'];
            }
        }

        return 0;
    }

    /**
     * Осуществляет выборку из таблицы значений по указанным параметрам
     *
     * @param array $arParams Параметры запроса к базе данных
     * @param bool  $showSql  Показать SQL запрос, вместо выборки (для отладки)
     *
     * @return array|bool Массив значений таблицы, массив с SQL запросом, либо в случае неудачи false
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     * @throws SqlQueryException
     */
    final public static function getList($arParams = [], $showSql = false)
    {
        $query = new Db\Query\QuerySelect(static::getClassName(), $arParams);
        if ($showSql)
        {
            return $query->getSql();
        }

        $res = $query->exec();
        $arResult = [];
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
     *
     * @return array|bool
     *
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     * @throws SqlQueryException
     * @see static::getList
     */
    final public static function getListFunc()
    {
        if (func_num_args() <= 0)
        {
            throw new ArgumentNullException('params');
        }
        $params = func_get_arg(0);

        return static::getList($params[0]);
    }

    /**
     * Делает запрос, чтобы узнать, существует ли таблица с заданным именем
     *
     * @param string|null $tableName
     *
     * @return bool
     * @throws SqlQueryException
     */
    final public static function issetTable(string $tableName = null)
    {
        if (is_null($tableName))
        {
            $tableName = static::getTableName();
        }
        $query = new Db\Query\QueryBase("SHOW TABLES LIKE '" . $tableName . "'");
        $res = $query->exec();

        return ($res->getAffectedRows() > 0);
    }

    /**
     * Пытается переименовать таблицу. Если таблица с новым именем существует, и нельзя использовать индекс, возвращает
     * false. Иначе подбирает индекс таким образом, чтобы таблицы с индексом не существовало и тогда создает ее
     *
     * @param string $newTableName Имя таблицы
     * @param bool $bUseIndex Использовать ли индекс
     *
     * @return bool|string
     * @throws SqlQueryException
     */
    final public static function rename($newTableName, $bUseIndex = false)
    {
        $helper = new Db\SqlHelper();
        if (!static::issetTable($newTableName))
        {
            $sql = 'RENAME TABLE ' . $helper->wrapQuotes(static::getTableName()) . ' TO ' . $helper->wrapQuotes(
                    $newTableName
                );
            $query = new Db\Query\QueryBase($sql);
            $query->exec();

            return (static::issetTable($newTableName) ? $newTableName : false);
        }
        elseif ($bUseIndex)
        {
            $i = 0;
            do
            {
                $i++;
                if ($i > 0 && $i < 10)
                {
                    $expName = $newTableName . '0' . $i;
                }
                else
                {
                    $expName = $newTableName . $i;
                }
                $res = static::issetTable($expName);
            }
            while ($res);

            $sql =
                'RENAME TABLE ' . $helper->wrapQuotes(static::getTableName()) . ' TO ' . $helper->wrapQuotes($expName);
            $query = new Db\Query\QueryBase($sql);
            $query->exec();

            return (static::issetTable($expName) ? $expName : false);
        }
        else
        {
            return false;
        }
    }

    /**
     * Очищает таблицу
     *
     * @return bool
     * @throws SqlQueryException
     */
    final public static function clearTable()
    {
        $helper = new Db\SqlHelper();
        $sql = 'TRUNCATE TABLE ' . $helper->wrapQuotes(static::getTableName()) . ';';
        $query = new Db\Query\QueryBase($sql);
        $res = $query->exec();

        return $res->isSuccess();
    }

    /**
     * Заглушка, чтобы не использовали этот метод
     *
     * @throws NotSupportedException
     */
    final private static function OnBeforeInsert()
    {
        throw new NotSupportedException('Method OnBeforeInsert not supported. Use OnBeforeAdd');
    }

    /**
     * Заглушка, чтобы не использовали этот метод
     *
     * @throws NotSupportedException
     */
    final private static function OnAfterInsert()
    {
        throw new NotSupportedException('Method OnAfterInsert not supported. Use OnAfterAdd');
    }
}
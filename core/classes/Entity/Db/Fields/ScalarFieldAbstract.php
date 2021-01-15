<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Fields\ScalarFieldAbstract
 * Сущность поля базы данных, содержащее скалярные данные
 */
abstract class ScalarFieldAbstract extends FieldAbstract implements IField
{
    const DEFAULT_VALUE_TYPE_CREATE = 'create';

    const DEFAULT_VALUE_TYPE_INSERT = 'insert';

    const DEFAULT_VALUE_TYPE_UPDATE = 'update';

    const DEFAULT_VALUE_TYPE_VALUE  = 'value';

    /**
     * @var null|array Массив разрешенных значений для поля, либо null - не производить валидацию
     */
    protected $allowed_values = null;
    /**
     * @var array|null Массив с диапазоном разрешенных значений для поля, либо null - не производить валидацию
     */
    protected $allowed_values_range = null;
    /**
     * @var string Метод класса таблицы, который валидирует значения поля
     */
    protected $validatorMethod = null;
    /**
     * @var array|null Массив исполняемых функций
     */
    protected $arRun = null;
    /**
     * @var string Название поля в базе данных
     */
    protected $column_name = '';
    /**
     * @var null|callable|mixed Значение поля по-умолчанию для CREATE запроса
     */
    protected $default_create = null;
    /**
     * @var bool Флаг, что значением по-умолчанию для CREATE запроса является SQL код
     */
    protected $default_create_sql = false;
    /**
     * @var null|callable|mixed Значение поля по-умолчанию для INSERT запроса
     */
    protected $default_insert = null;
    /**
     * @var bool Флаг, что значением по-умолчанию для INSERT запроса является SQL код
     */
    protected $default_insert_sql = false;
    /**
     * @var null|callable|mixed Значение поля по-умолчанию для UPDATE запроса
     */
    protected $default_update = null;
    /**
     * @var bool Флаг, что значением по-умолчанию для UPDATE запроса является SQL код
     */
    protected $default_update_sql = false;
    /**
     * @var null|callable|mixed Значение поля по-умолчанию
     */
    protected $default_value = null;
    /**
     * @var bool Флаг, что значеним по-умолчанию является SQL код
     */
    protected $default_value_sql = false;
    /**
     * @var bool Для поля используется auto increment
     */
    protected $is_autocomplete = false;
    /**
     * @var bool Поле является PRIMARY
     */
    protected $is_primary = false;
    /**
     * @var bool Для поля обязательно передавать значение
     */
    protected $is_required = false;
    /**
     * @var bool Значение по умолчанию для обязательного поля NULL
     */
    protected $is_required_null = false;
    /**
     * @var bool Значение в поле должно быть уникальным
     */
    protected $is_unique = false;
    /**
     * @var array|null Варианты значений поля
     */
    protected $values = null;

    /**
     * Запускает метод-валидатор, если он установлен
     *
     * @param mixed  $mValue Полученное значение поля
     * @param string $actionType Тип действия
     *
     * @return bool|mixed
     */
    public function runValidator (&$mValue, $actionType=self::DEFAULT_VALUE_TYPE_INSERT)
    {
        if (!is_null($this->validatorMethod))
        {
            return call_user_func([$this,$this->validatorMethod],[&$mValue, $actionType]);
        }

        return true;
    }

    /**
     * Устанавливает метод-валидатор значения поля БД
     *
     * @param string $validatorMethodName Имя метода-валидатора
     *
     * @return $this
     */
    public function setValidator (string $validatorMethodName)
    {
        if (method_exists($this,$validatorMethodName))
        {
            $this->validatorMethod = $validatorMethodName;
        }

        return $this;
    }

    /**
     * Обрабатывает значение поля после получения значения из базы данных
     *
     * @param mixed               $value
     *
     * @return array|mixed
     * @unittest
     */
    public function fetchDataModification ($value)
    {
        if (!is_null($value))
        {
            $additionalFetchDataModification = $this->getFetchDataModification();
            if (!is_null($additionalFetchDataModification) && is_callable($additionalFetchDataModification))
            {
                $value = call_user_func($additionalFetchDataModification, $value);
            }
            if ($this->isSerialized())
            {
                $value = $this->unserialize($value);
            }
        }

        return $value;
    }

    /**
     * Обрабатывает значение поля перед сохранением в базе данных
     *
     * @param mixed               $value
     *
     * @return mixed|string
     * @unittest
     */
    public function saveDataModification ($value)
    {
        if (!is_null($value))
        {
            $additionalSaveDataModification = $this->getSaveDataModification();
            if (!is_null($additionalSaveDataModification) && is_callable($additionalSaveDataModification))
            {
                $value = call_user_func($additionalSaveDataModification, $value);
            }
            if ($this->isSerialized())
            {
                $value = $this->serialize($value);
            }
        }

        return $value;
    }

    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     */
    public function __construct (string $name)
    {
        parent::__construct($name);
    }

    /**
     * Приводит значение по-умолчанию поля к строке
     *
     * @return string
     * @unittest
     */
    public function __toString ()
    {
        return strval(self::getDefaultValue());
    }

    /**
     * Возвращает массив разрешенных для поля значений, либо null
     *
     * @return array|null
     * @unittest
     */
    public function getAllowedValues ()
    {
        return $this->allowed_values;
    }

    /**
     * Устанавливает список значений, которые может принимать поле
     *
     * @param array $allowedValues
     *
     * @return $this
     * @unittest
     */
    public function setAllowedValues (array $allowedValues): IField
    {
        $this->allowed_values = $allowedValues;

        return $this;
    }

    /**
     * Возвращает массив с диапазоном разрешенных значений для поля, либо null
     *
     * @return array|null
     * @unittest
     */
    public function getAllowedValuesRange ()
    {
        return $this->allowed_values_range;
    }

    /**
     * Устанавливает диапазон значений, которые может принимать поле (для числовых значений)
     *
     * @param float $rangeMin Минимальное значение
     * @param float $rangeMax Максимальное значение
     *
     * @return $this
     * @unittest
     */
    public function setAllowedValuesRange (float $rangeMin, float $rangeMax): IField
    {
        $this->allowed_values_range = ['min' => $rangeMin, 'max' => $rangeMax];

        return $this;
    }

    /**
     * Возвращает название поля в базе данных
     *
     * @return string
     * @unittest
     */
    public function getColumnName (): string
    {
        if (empty($this->column_name))
        {
            return $this->getName();
        }
        else
        {
            return $this->column_name;
        }
    }

    /**
     * Устанавливает название поля в БД
     *
     * @param string $columnName
     *
     * @return $this
     * @unittest
     */
    public function setColumnName (string $columnName): IField
    {
        $this->column_name = $columnName;

        return $this;
    }

    /**
     * Возвращает значение поля по-умолчанию
     *
     * @param string $type Тип значения по-умолчанию
     *
     * @return callable|mixed|null
     * @unittest
     */
    public function getDefaultValue (string $type = self::DEFAULT_VALUE_TYPE_VALUE)
    {
        $type = strtolower($type);
        switch ($type)
        {
            case self::DEFAULT_VALUE_TYPE_CREATE:
                if (!is_null($this->default_create))
                {
                    $default_value = $this->default_create;
                }
                else
                {
                    $default_value = $this->default_value;
                }
                break;
            case self::DEFAULT_VALUE_TYPE_INSERT:
                if (!is_null($this->default_insert))
                {
                    $default_value = $this->default_insert;
                }
                else
                {
                    $default_value = $this->default_value;
                }
                break;
            case self::DEFAULT_VALUE_TYPE_UPDATE:
                if (!is_null($this->default_update))
                {
                    $default_value = $this->default_update;
                }
                else
                {
                    $default_value = $this->default_value;
                }
                break;
            default:
                $default_value = $this->default_value;
                break;
        }


        if (is_callable($default_value))
        {
            return call_user_func($default_value);
        }
        else
        {
            return $default_value;
        }
    }

    /**
     * Устанавливает значение по-умолчанию для всех действий: CREATE, INSERT, UPDATE
     *
     * @param mixed $defaultValue
     *
     * @return $this
     * @unittest
     */
    public function setDefaultValue ($defaultValue): IField
    {
        $this->default_value = $defaultValue;

        return $this;
    }

    /**
     * Возвращает массив исполняемых функций
     *
     * @return array|null
     * @unittest
     */
    public function getRun ()
    {
        return $this->arRun;
    }

    /**
     * Возвращает значение поля в SQL формате
     *
     * @param mixed $value
     *
     * @return string
     * @unittest
     */
    public function getSqlValue ($value): string
    {
        return "'" . $value . "'";
    }

    /**
     * Возвращает флаг того, используется ли для поля auto increment
     *
     * @return bool
     * @unittest
     */
    public function isAutocomplete (): bool
    {
        return $this->is_autocomplete;
    }

    /**
     * Возвращает флаг того, что значение по-умолчанию является функцией SQL
     *
     * @param string $type Тип значения по-умолчанию
     *
     * @return bool
     * @unittest
     */
    public function isDefaultSql (string $type = self::DEFAULT_VALUE_TYPE_VALUE): bool
    {
        $type = strtolower($type);
        switch ($type)
        {
            case self::DEFAULT_VALUE_TYPE_CREATE:
                return $this->default_create_sql;
            case self::DEFAULT_VALUE_TYPE_INSERT:
                return $this->default_insert_sql;
            case self::DEFAULT_VALUE_TYPE_UPDATE:
                return $this->default_update_sql;
            default:
                return $this->default_value_sql;
        }
    }

    /**
     * Возвращает флаг того, является ли поле PRIMARY
     *
     * @return bool
     * @unittest
     */
    public function isPrimary (): bool
    {
        return $this->is_primary;
    }

    /**
     * Возвращает флаг того, является ли поле обязательным
     *
     * @return bool
     * @unittest
     */
    public function isRequired (): bool
    {
        return $this->is_required;
    }

    /**
     * Возвращает флаг того, что значение по-умолчанию в таблице для обязательного поля равно NULL
     *
     * @return bool
     * @unittest
     */
    public function isRequiredNull (): bool
    {
        return $this->is_required_null;
    }

    /**
     * Возвращает флаг того, являются ли значения поля уникальными
     *
     * @return bool
     * @unittest
     */
    public function isUnique (): bool
    {
        return $this->is_unique;
    }

    /**
     * <Описание>
     * //TODO: Что это?
     *
     * @param array $arRun
     *
     * @return $this
     * @unittest
     */
    public function setArRun (array $arRun)
    {
        $this->arRun = $arRun;

        return $this;
    }

    /**
     * Устанавливает флаг того, что значение поля является автоинкрементным
     *
     * @param bool $isAutocomplete
     *
     * @return $this
     * @unittest
     */
    public function setAutocomplete (bool $isAutocomplete = true): IField
    {
        $this->is_autocomplete = $isAutocomplete;

        return $this;
    }

    /**
     * Устанавливает значение по-умолчанию для действия CREATE
     *
     * @param mixed $defaultCreate
     *
     * @return $this
     * @unittest
     */
    public function setDefaultCreate ($defaultCreate): IField
    {
        $this->default_create = $defaultCreate;

        return $this;
    }

    /**
     * Устанавливает флаг того, что значением по-умолчанию для действия CREATE является SQL
     *
     * @param bool $isDefaultCreateSql
     *
     * @return $this
     * @unittest
     */
    public function setDefaultCreateSql (bool $isDefaultCreateSql = true): IField
    {
        $this->default_create_sql = $isDefaultCreateSql;

        return $this;
    }

    /**
     * Устанавливает значение по-умолчанию для действия INSERT
     *
     * @param mixed $defaultInsert
     *
     * @return $this
     * @unittest
     */
    public function setDefaultInsert ($defaultInsert): IField
    {
        $this->default_insert = $defaultInsert;

        return $this;
    }

    /**
     * Устанавливает флаг того, что значение по-умолчанию для действия INSERT является SQL
     *
     * @param bool $isDefaultInsertSql
     *
     * @return $this
     * @unittest
     */
    public function setDefaultInsertSql (bool $isDefaultInsertSql = true): IField
    {
        $this->default_insert_sql = $isDefaultInsertSql;

        return $this;
    }

    /**
     * Устанавливает значение по-умолчанию для действия UPDATE
     *
     * @param mixed $defaultUpdate
     *
     * @return $this
     * @unittest
     */
    public function setDefaultUpdate ($defaultUpdate): IField
    {
        $this->default_update = $defaultUpdate;

        return $this;
    }

    /**
     * Устанавливает флаг того, что значение по-умолчанию для действия INSERT является SQL
     *
     * @param bool $isDefaultUpdateSql
     *
     * @return $this
     * @unittest
     */
    public function setDefaultUpdateSql (bool $isDefaultUpdateSql = true): IField
    {
        $this->default_update_sql = $isDefaultUpdateSql;

        return $this;
    }

    /**
     * Устанавливает флаг того, что описание значения по-умолчанию для всех действий является SQL
     *
     * @param bool $isDefaultValueSql
     *
     * @return $this
     * @unittest
     */
    public function setDefaultValueSql (bool $isDefaultValueSql = true): IField
    {
        $this->default_value_sql = $isDefaultValueSql;

        return $this;
    }

    /**
     * Устанавливает флаг PRIMARY KEY поля
     *
     * @param bool $isPrimary
     *
     * @return $this
     * @unittest
     */
    public function setPrimary (bool $isPrimary = true): IField
    {
        $this->is_primary = $isPrimary;

        return $this;
    }

    /**
     * Устанавливает флаг того, что поля является обязательным
     *
     * @param bool $isRequired
     *
     * @return $this
     * @unittest
     */
    public function setRequired (bool $isRequired = true): IField
    {
        $this->is_required = $isRequired;

        return $this;
    }

    /**
     * Устанавливает флаг того, что значение обязательного поля может быть NULL
     *
     * @param bool $isRequiredNull
     *
     * @return $this
     * @unittest
     */
    public function setRequiredNull (bool $isRequiredNull = true): IField
    {
        $this->is_required_null = $isRequiredNull;

        return $this;
    }

    /**
     * Устанавливает флаг того, что значения поля уникальны
     *
     * @param bool $isUnique
     *
     * @return $this
     * @unittest
     */
    public function setUnique (bool $isUnique = true): IField
    {
        $this->is_unique = $isUnique;

        return $this;
    }

    /**
     * Устанавливает список возможных значений поля
     *
     * @param array $arValues
     *
     * @return $this
     * @unittest
     */
    public function setValues (array $arValues): IField
    {
        $this->values = $arValues;

        return $this;
    }
}
<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Entity\Db\Fields\ScalarField
 * Сущность поля базы данных, содержащее скалярные данные
 */
class ScalarField extends Field
{
	/**
	 * @var null|array Массив разрешенных значений для поля, либо null - не производить валидацию
	 */
	protected $allowed_values = null;
	/**
	 * @var array|null Массив с диапазоном разрешенный значения для поля, либо null - не производить валидацию
	 */
	protected $allowed_values_range = null;
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
	 * Обрабатывает значение поля после получения значения из базы данных
	 *
	 * @param mixed         $value
	 * @param ScalarField   $obj
	 *
	 * @return array|mixed
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			if (!is_null($value))
			{
				$additionalFetchDataModification = $obj->getFetchDataModification();
				if (!is_null($additionalFetchDataModification) && is_callable($additionalFetchDataModification))
				{
					$value = call_user_func($additionalFetchDataModification,$value);
				}
				if ($obj->isSerialized())
				{
					$value = $obj->unserialize($value);
				}
			}
		}

		return $value;
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базе данных
	 *
	 * @param mixed         $value
	 * @param ScalarField   $obj
	 *
	 * @return mixed|string
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			if (!is_null($value))
			{
				$additionalSaveDataModification = $obj->getSaveDataModification();
				if (!is_null($additionalSaveDataModification) && is_callable($additionalSaveDataModification))
				{
					$value = call_user_func($additionalSaveDataModification,$value);
				}
				if ($obj->isSerialized())
				{
					$value = $obj->serialize($value);
				}
			}
		}

		return $value;
	}

    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     *
     * @throws \Ms\Core\Exception\ArgumentNullException
     */
	public function __construct($name)
	{
		parent::__construct($name);
	}

    /**
     * Приводит значение по-умолчанию поля к строке
     *
     * @return string
     */
	public function __toString()
	{
		return strval(self::getDefaultValue());
	}

	/**
	 * Возвращает массив разрешенных для поля значений, либо null
	 *
	 * @return array|null
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
     */
	public function setAllowedValues (array $allowedValues)
	{
		$this->allowed_values = $allowedValues;

		return $this;
	}

	/**
	 * Возвращает массив с диапазоном разрешенных значений для поля, либо null
	 *
	 * @return array|null
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
     */
	public function setAllowedValuesRange (float $rangeMin, float $rangeMax)
	{
		$this->allowed_values_range = ['min'=>$rangeMin, 'max'=>$rangeMax];

		return $this;
	}

	/**
	 * Возвращает название поля в базе данных
	 *
	 * @return string
	 */
	public function getColumnName()
	{
		return $this->column_name;
	}

    /**
     * Устанавливает название поля в БД
     *
     * @param string $columnName
     *
     * @return $this
     */
	public function setColumnName (string $columnName)
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
	 */
	public function getDefaultValue($type='value')
	{
		$type = strtolower($type);
		switch ($type)
		{
			case 'create':
				if (!is_null($this->default_create))
				{
					$default_value = $this->default_create;
				}
				else
				{
					$default_value = $this->default_value;
				}
				break;
			case 'insert':
				if (!is_null($this->default_insert))
				{
					$default_value = $this->default_insert;
				}
				else
				{
					$default_value = $this->default_value;
				}
				break;
			case 'update':
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
	 */
	public function setDefaultValue ($defaultValue)
	{
		$this->default_value = $defaultValue;

		return $this;
	}

	/**
	 * Возвращает массив исполняемых функций
	 *
	 * @ignore
	 *
	 * @return array
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
	 */
	public function getSqlValue ($value)
	{
		return "'".$value."'";
	}

	/**
	 * Возвращает флаг того, используется ли для поля auto increment
	 *
	 * @return bool
	 */
	public function isAutocomplete()
	{
		return $this->is_autocomplete;
	}

	/**
	 * Возвращает флаг того, что значение по-умолчанию является функцией SQL
	 *
	 * @param string $type Тип значения по-умолчанию
	 *
	 * @return bool
	 */
	public function isDefaultSql ($type='value')
	{
		$type = strtolower($type);
		switch ($type)
		{
			case 'create':
				return $this->default_create_sql;
			case 'insert':
				return $this->default_insert_sql;
			case 'update':
				return $this->default_update_sql;
			default:
				return $this->default_value_sql;
		}
	}

	/**
	 * Возвращает флаг того, является ли поле PRIMARY
	 *
	 * @return bool
	 */
	public function isPrimary()
	{
		return $this->is_primary;
	}

	/**
	 * Возвращает флаг того, является ли поле обязательным
	 *
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->is_required;
	}

	/**
	 * Возвращает флаг того, что значение по-умолчанию в таблице для обязательного поля равно NULL
	 *
	 * @return bool
	 */
	public function isRequiredNull()
	{
		return $this->is_required_null;
	}

	/**
	 * Возвращает флаг того, являются ли значения поля уникальными
	 *
	 * @return bool
	 */
	public function isUnique()
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
     */
	public function setAutocomplete (bool $isAutocomplete = true)
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
	 */
	public function setDefaultCreate ($defaultCreate)
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
     */
	public function setDefaultCreateSql (bool $isDefaultCreateSql = true)
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
	 */
	public function setDefaultInsert ($defaultInsert)
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
     */
	public function setDefaultInsertSql (bool $isDefaultInsertSql = true)
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
	 */
	public function setDefaultUpdate ($defaultUpdate)
	{
		$this->default_value = $defaultUpdate;

		return $this;
	}

    /**
     * Устанавливает флаг того, что значение по-умолчанию для действия INSERT является SQL
     *
     * @param bool $isDefaultUpdateSql
     *
     * @return $this
     */
	public function setDefaultUpdateSql (bool $isDefaultUpdateSql = true)
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
     */
	public function setDefaultValueSql (bool $isDefaultValueSql = true)
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
     */
	public function setPrimary (bool $isPrimary = true)
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
     */
	public function setRequired (bool $isRequired = true)
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
     */
	public function setRequiredNull (bool $isRequiredNull = true)
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
     */
	public function setUnique (bool $isUnique = true)
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
     */
	public function setValues (array $arValues)
	{
		$this->values = $arValues;

		return $this;
	}
}
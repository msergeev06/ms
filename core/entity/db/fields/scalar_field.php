<?php
/**
 * Ms\Core\Entity\Db\Fields\ScalarField
 * Сущность поля базы данных, содержащее скалярные данные
 *
 * @package MSergeev\Core
 * @subpackage Entity\Db\Fields
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Class ScalarField
 * @package Ms\Core
 * @subpackage Entity\Db\Fields
 * @extends Field
 *
 * @var bool                $is_primary             Поле является PRIMARY
 * @var bool                $is_unique              Значение в поле должно быть уникальным
 * @var bool                $is_required            Для поля обязательно передавать значение
 * @var bool                $is_autocomplete        Для поля используется auto increment
 * @var string              $column_name            Название поля в базе данных
 * @var array               $arRun                  Массив исполняемых функций
 * @var null|callable|mixed $default_value          Значение поля по-умолчанию
 *
 * @var string              $name                   Название поля в API
 * @var string              $dataType               Тип поля в базе данных
 * @var string              $fieldType              Тип поля в API
 * @var array               $initialParameters      Параметры инициализации
 * @var string              $title                  Описание поля
 * @var bool                $isSerialized           Является ли значение поля сериализованным массивом
 * @var Field               $parentField            Родительское поле
 * @var null|callback       $fetchDataModification  Функция обработки полученных значений из базы
 * @var null|callback       $saveDataModification   Функция обработки перед записью значений в базу
 * @var null|string         $link                   Связь поля таблицы
 *
 * @method Field    getName()                   Возвращает название поля в API
 * @method Field    getTitle()                  Возвращает описание поля
 * @method Field    getDataType()               Возвращает тип поля в базы данных
 * @method Field    getFieldType()              Возвращает тип поля в API
 * @method Field    getParentField()            Возвращает объект родительского поля
 * @method Field    getLink()                   Возвращает строку - связь поля с другим полем
 * @method Field    serialize($value)           Сериализует массив
 * @method Field    unserialize($value)         Десериализирует массив
 * @method Field    isSerialized()              Возвращает флаг, обозначающий факт того, является ли значение данного
 *                                              поля сериализованным массивом
 * @method Field    getFetchDataModification()  Возвращает название функции для обработки значений полученных из базы
 *                                              данных
 * @method Field    getSaveDataModification()   Возвращает название функции для обработки значений перед сохранением в
 *                                              базу данных
 */
class ScalarField extends Field
{
	/**
	 * @var bool Поле является PRIMARY
	 */
	protected $is_primary = false;

	/**
	 * @var bool Значение в поле должно быть уникальным
	 */
	protected $is_unique = false;

	/**
	 * @var bool Для поля обязательно передавать значение
	 */
	protected $is_required = false;

	/**
	 * @var bool Значение по умолчанию для обязательного поля NULL
	 */
	protected $is_required_null = false;

	/**
	 * @var bool Для поля используется auto increment
	 */
	protected $is_autocomplete = false;

	/**
	 * @var string Название поля в базе данных
	 */
	protected $column_name = '';

	/**
	 * @var array Массив исполняемых функций
	 */
	protected $arRun = null;

	/**
	 * @var array Варианты значений поля
	 */
	protected $values = null;

	/**
	 * @var null|callable|mixed Значение поля по-умолчанию
	 */
	protected $default_value = null;

	/**
	 * @var bool Флаг, что значеним по-умолчанию является SQL код
	 */
	protected $default_value_sql = false;

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
	 * @var null|array Массив разрешенных значений для поля, либо null - не производить валидацию
	 */
	protected $allowed_values = null;

	/**
	 * @var array|null Массив с диапазоном разрешенный значения для поля, либо null - не производить валидацию
	 */
	protected $allowed_values_range = null;

	/**
	 * Конструктор
	 *
	 * @param string $name              Имя поля таблицы БД
	 * @param array  $parameters        Параметры поля таблицы БД
	 * @param string $link              Связанное поле вида "таблица.поле"
	 * @param string $onUpdate          Действие при изменении связанного поля
	 * @param string $onDelete          Действие при удалении связанного поля
	 * @param bool   $linkNotForeignKey Флаг, что связь не является FOREIGN KEY
	 */
	public function __construct($name, $parameters = array(),$link=null,$onUpdate='cascade',$onDelete='restrict', $linkNotForeignKey=false)
	{
		parent::__construct($name, $parameters,$link,$onUpdate,$onDelete,$linkNotForeignKey);

		$this->is_primary = (isset($parameters['primary']) && $parameters['primary']);
		$this->is_unique = (isset($parameters['unique']) && $parameters['unique']);
		$this->is_required = (isset($parameters['required']) && $parameters['required']);
		$this->is_required_null = (isset($parameters['required_null']) && $parameters['required_null']);
		$this->is_autocomplete = (isset($parameters['autocomplete']) && $parameters['autocomplete']);

		$this->values = isset($parameters['values']) ? $parameters['values'] : null;
		$this->column_name = isset($parameters['column_name']) ? $parameters['column_name'] : $this->name;

		$this->default_value = isset($parameters['default_value']) ? $parameters['default_value'] : null;
		$this->default_value_sql = (isset($parameters['default_value_sql']) && $parameters['default_value_sql']) ? true : false;

		$this->default_create = isset($parameters['default_create']) ? $parameters['default_create'] : null;
		$this->default_create_sql = (isset($parameters['default_create_sql']) && $parameters['default_create_sql']) ? true : false;

		$this->default_insert = isset($parameters['default_insert']) ? $parameters['default_insert'] : null;
		$this->default_insert_sql = (isset($parameters['default_insert_sql']) && $parameters['default_insert_sql']) ? true : false;

		$this->default_update = isset($parameters['default_update']) ? $parameters['default_update'] : null;
		$this->default_update_sql = (isset($parameters['default_update_sql']) && $parameters['default_update_sql']) ? true : false;

		$this->allowed_values = (isset($parameters['allowed_values']) && is_array($parameters['allowed_values'])) ? $parameters['allowed_values'] : null;

		if (isset($parameters["run"]))
		{
			$this->arRun = $parameters["run"];
		}

		return $this;
	}

	//<editor-fold defaultstate="collapse" desc=">>>>>> Base">
	public function setPrimary (bool $isPrimary = true)
	{
		$this->is_primary = $isPrimary;

		return $this;
	}

	public function setUnique (bool $isUnique = true)
	{
		$this->is_unique = $isUnique;

		return $this;
	}

	public function setRequired (bool $isRequired = true)
	{
		$this->is_required = $isRequired;

		return $this;
	}

	public function setRequiredNull (bool $isRequiredNull = true)
	{
		$this->is_required_null = $isRequiredNull;

		return $this;
	}

	public function setAutocomplete (bool $isAutocomplete = true)
	{
		$this->is_autocomplete = $isAutocomplete;

		return $this;
	}

	public function setValues (array $arValues)
	{
		$this->values = $arValues;

		return $this;
	}

	public function setColumnName (string $columnName)
	{
		$this->column_name = $columnName;

		return $this;
	}
	//</editor-fold>

	//<editor-fold defaultstate="collapse" desc=">>>>>> Set Default Values">
	/**
	 * @param mixed $defaultValue
	 *
	 * @return $this
	 */
	public function setDefaultValue ($defaultValue)
	{
		$this->default_value = $defaultValue;

		return $this;
	}

	public function setDefaultValueSql (bool $isDefaultValueSql = true)
	{
		$this->default_value_sql = $isDefaultValueSql;

		return $this;
	}

	/**
	 * @param mixed $defaultCreate
	 *
	 * @return $this
	 */
	public function setDefaultCreate ($defaultCreate)
	{
		$this->default_create = $defaultCreate;

		return $this;
	}

	public function setDefaultCreateSql (bool $isDefaultCreateSql = true)
	{
		$this->default_create_sql = $isDefaultCreateSql;

		return $this;
	}

	/**
	 * @param mixed $defaultInsert
	 *
	 * @return $this
	 */
	public function setDefaultInsert ($defaultInsert)
	{
		$this->default_insert = $defaultInsert;

		return $this;
	}

	public function setDefaultInsertSql (bool $isDefaultInsertSql = true)
	{
		$this->default_insert_sql = $isDefaultInsertSql;

		return $this;
	}

	/**
	 * @param mixed $defaultUpdate
	 *
	 * @return $this
	 */
	public function setDefaultUpdate ($defaultUpdate)
	{
		$this->default_value = $defaultUpdate;

		return $this;
	}

	public function setDefaultUpdateSql (bool $isDefaultUpdateSql = true)
	{
		$this->default_update_sql = $isDefaultUpdateSql;

		return $this;
	}
	//</editor-fold>

	public function setAllowedValues (array $allowedValues)
	{
		$this->allowed_values = $allowedValues;

		return $this;
	}

	public function setAllowedValuesRange (float $rangeMin, float $rangeMax)
	{
		$this->allowed_values_range = ['min'=>$rangeMin, 'max'=>$rangeMax];

		return $this;
	}

	public function setArRun (array $arRun)
	{
		//TODO: Что это?
		$this->arRun = $arRun;

		return $this;
	}

	/**
	 * Возвращает флаг того, является ли поле PRIMARY
	 *
	 * @api
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
	 * @api
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
	 * @api
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
	 * @api
	 *
	 * @return bool
	 */
	public function isUnique()
	{
		return $this->is_unique;
	}

	/**
	 * Возвращает флаг того, используется ли для поля auto increment
	 *
	 * @api
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
	 * @api
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
	 * Возвращает значение поля по-умолчанию
	 *
	 * @api
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
	 * Возвращает название поля в базе данных
	 *
	 * @api
	 *
	 * @return string
	 */
	public function getColumnName()
	{
		return $this->column_name;
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
	 * Возвращает массив разрешенных для поля значений, либо null
	 *
	 * @return array|null
	 */
	public function getAllowedValues ()
	{
		return $this->allowed_values;
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

	public function __toString()
	{
		return strval(self::getDefaultValue());
	}
}
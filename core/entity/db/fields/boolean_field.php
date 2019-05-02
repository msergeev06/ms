<?php
/**
 * Ms\Core\Entity\Db\Fields\BooleanField
 * Сущность поля базы данных, содержащего булево значение
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Class BooleanField
 * @package Ms\Core
 * @subpackage Entity
 * @extends ScalarField
 *
 * @var array               $values                 Варианты значений поля
 * @var int                 $size                   Размер типа поля в базе данных
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
 * @method ScalarField isPrimary()                  Возвращает флаг того, является ли поле PRIMARY
 * @method ScalarField isRequired()                 Возвращает флаг того, является ли поле обязательным
 * @method ScalarField isUnique()                   Возвращает флаг того, являются ли значения поля уникальными
 * @method ScalarField isAutocomplete()             Возвращает флаг того, используется ли для поля auto increment
 * @method ScalarField getColumnName()              Возвращает название поля в базе данных
 * @method ScalarField getRun()                     Возвращает массив исполняемых функций
 * @method ScalarField setColumnName($column_name)  Задает название поля в базе данных
 * @method ScalarField getDefaultValue()            Возвращает значение поля по-умолчанию
 * @static ScalarField validate($value,$obj)        Осуществляет валидацию данных
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
 * @method Field    getFetchDataModification()  Возвращает название функции для обработки значений полученных из
 *                                              базы данных
 * @method Field    getSaveDataModification()   Возвращает название функции для обработки значений перед сохранением
 *                                              в базу данных
 */
class BooleanField extends ScalarField {
	/**
	 * Value (false, true) equivalent map
	 * @var array
	 */
	protected $values;

	/**
	 * @var int Размер типа поля в базе данных
	 */
	protected $size=1;

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
	function __construct($name, $parameters = array(),$link=null,$onUpdate='cascade',$onDelete='restrict', $linkNotForeignKey=false)
	{
		parent::__construct($name, $parameters,$link,$onUpdate,$onDelete,$linkNotForeignKey);

		$this->dataType = 'varchar';
		$this->fieldType = 'boolean';

		if (empty($parameters['values']))
		{
			$this->values = array(false, true);
		}
		else
		{
			$this->values = $parameters['values'];
		}
	}


	/**
	 * Convert true/false values to actual field values
	 *
	 * @api
	 *
	 * @param boolean|integer|string $value
	 * @return mixed
	 */
	public function normalizeValue($value)
	{
		if (
			(is_string($value) && ($value == '1' || $value == '0'))
			||
			(is_bool($value))
		)
		{
			$value = (int) $value;
		}
		elseif (is_string($value) && ($value == 'true' || $value == 'Y'))
		{
			$value = 1;
		}
		elseif (is_string($value) && ($value == 'false' || $value== 'N'))
		{
			$value = 0;
		}

		if (is_integer($value) && ($value == 1 || $value == 0))
		{
			$value = $this->values[$value];
		}

		return $value;
	}

	/**
	 * Возвращает варианты значений поля
	 *
	 * @api
	 *
	 * @return array
	 */
	public function getValues()
	{
		return $this->values;
	}

	/**
	 * Возвращает размер типа поля в базе данных
	 *
	 * @api
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * Возвращает значение по-умолчанию для базы данных
	 *
	 * @api
	 *
	 * @return null|string
	 */
	public function getDefaultValueDB()
	{
		$value = $this->getDefaultValue();
		if (!is_null($value))
		{
			if ($value === true) {
				return 'Y';
			}
			else {
				return 'N';
			}
		}
		else
		{
			return null;
		}
	}

	/**
	 * Возвращает значение приобразованное в формат SQL запроса
	 *
	 * @param mixed $value значение
	 *
	 * @return string
	 */
	public function getSqlValue($value)
	{
		if ($value === true || $value==='Y' || $value===1)
		{
			return "'Y'";
		}
		else
		{
			return "'N'";
		}
	}

	/**
	 * Обрабатывает значение поля перед записью в базу данных
	 *
	 * @api
	 *
	 * @param mixed $value
	 * @param BooleanField|null $obj
	 *
	 * @return mixed|string
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			$value = $obj->normalizeValue($value);
		}
		if ($value)
		{
			$value = 'Y';
		}
		else
		{
			$value = 'N';
		}
		$value = parent::saveDataModification($value, $obj);

		return $value;
	}

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @api
	 *
	 * @param string $value
	 * @param BooleanField|null $obj
	 *
	 * @return array|mixed
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			$value = $obj->normalizeValue($value);
		}
		$value = parent::fetchDataModification($value, $obj);

		return $value;
	}
}
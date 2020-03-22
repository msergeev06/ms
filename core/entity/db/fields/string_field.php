<?php
/**
 * Ms\Core\Entity\Db\Fields\StringField
 * Сущность поля базы данных, содержащего строку
 *
 * @package Ms\Core
 * @subpackage Entity\Db\Fields
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Entity\Application;
use Ms\Core\Exception;

/**
 * Class StringField
 * @package Ms\Core
 * @subpackage Entity\Db\Fields
 * @extends ScalarField
 *
 * @var int                 $size                   Размер типа varchar базы данных
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
 * @method ScalarField isPrimary()                          Возвращает флаг того, является ли поле PRIMARY
 * @method ScalarField isRequired()                         Возвращает флаг того, является ли поле обязательным
 * @method ScalarField isUnique()                           Возвращает флаг того, являются ли значения поля уникальными
 * @method ScalarField isAutocomplete()                     Возвращает флаг того, используется ли для поля auto increment
 * @method ScalarField getColumnName()                      Возвращает название поля в базе данных
 * @method ScalarField getRun()                             Возвращает массив исполняемых функций
 * @method ScalarField setColumnName($column_name)          Задает название поля в базе данных
 * @method ScalarField getDefaultValue()                    Возвращает значение поля по-умолчанию
 * @static ScalarField saveDataModification($value,$obj)    Обрабатывает значение поля перед сохранением в базе данных
 * @static ScalarField fetchDataModification($value,$obj)   Обрабатывает значение поля после получения значения из
 *                                                          базы данных
 * @static ScalarField validate($value,$obj)                Осуществляет валидацию данных
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
class StringField extends ScalarField
{
	/**
	 * @var int Размер типа varchar базы данных
	 */
	protected $size = 255;

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
	function __construct($name, $parameters = array(),$link=null,$onUpdate='cascade',$onDelete='restrict',$linkNotForeignKey=false)
	{
		parent::__construct($name, $parameters,$link,$onUpdate,$onDelete,$linkNotForeignKey);

		$this->dataType = 'varchar';
		$this->fieldType = 'string';

		if(isset($parameters['size']) && intval($parameters['size']) > 0)
		{
			$this->size = intval($parameters['size']);
		}

		return $this;
	}

	public function setSize (int $size = 255)
	{
		if ((int)$size > 0 && (int)$size<=255)
		{
			$this->size = $size;
		}
		else
		{
			$this->size = 255;
		}

		return $this;
	}

	public function setDefaultValue ($defaultValue)
	{
		if (!is_string($defaultValue))
		{
			throw new Exception\ArgumentTypeException('$defaultValue','string');
		}
		return parent::setDefaultValue($defaultValue);
	}

	public function setDefaultInsert ($defaultInsert)
	{
		if (!is_string($defaultInsert))
		{
			throw new Exception\ArgumentTypeException('$defaultInsert','string');
		}
		return parent::setDefaultInsert($defaultInsert);
	}

	public function setDefaultCreate ($defaultCreate)
	{
		if (!is_string($defaultCreate))
		{
			throw new Exception\ArgumentTypeException('$defaultCreate','string');
		}
		return parent::setDefaultCreate($defaultCreate);
	}

	public function setDefaultUpdate ($defaultUpdate)
	{
		if (!is_string($defaultUpdate))
		{
			throw new Exception\ArgumentTypeException('$defaultUpdate','string');
		}
		return parent::setDefaultUpdate($defaultUpdate);
	}


	/**
	 * Возвращает размер поля в базе данных (в символах)
	 *
	 * @api
	 *
	 * @return int|null
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базе данных
	 *
	 * @param                   $value
	 * @param StringField|null  $obj
	 *
	 * @return mixed|string
	 * @throws Exception\Db\DbException
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		$DB = Application::getInstance()->getConnection();
		$value = parent::saveDataModification($value,$obj);
		//$value = mysql_real_escape_string($value);
		$value = $DB->getConnectionRealEscapeString($value);
		$value = str_replace("%", "\%", $value);

		return $value;
	}

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @param string           $value
	 * @param StringField|null $obj
	 *
	 * @return array|mixed
	 */
	public static function fetchDataModification ($value, $obj = NULL)
	{
		$value = parent::fetchDataModification($value, $obj);

		return $value;
	}
}
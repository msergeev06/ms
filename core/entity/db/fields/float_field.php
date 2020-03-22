<?php
/**
 * Ms\Core\Entity\Db\Fields\FloatField
 * Сущность поля базы данных, содержащего вещественное число
 *
 * @package Ms\Core
 * @subpackage Entity\Db\Fields
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Class FloatField
 * @package MSergeev\Core
 * @subpackage Entity\Db\Fields
 * @extends ScalarField
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
 * @static ScalarField validate($value, $obj)       Осуществляет валидацию данных
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
class FloatField extends ScalarField
{
	/**
	 * @var int Точность, знаков после запятой
	 */
	protected $scale=2;

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
	public function __construct($name, $parameters = array(),$link=null,$onUpdate='cascade',$onDelete='restrict',$linkNotForeignKey=false)
	{
		parent::__construct($name, $parameters,$link,$onUpdate,$onDelete,$linkNotForeignKey);

		$this->dataType = $this->fieldType = 'float';

		if(isset($parameters['scale']))
		{
			$this->scale = intval($parameters['scale']);
		}

		return $this;
	}

	/**
	 * @param int $scale
	 *
	 * @return FloatField
	 */
	public function setScale (int $scale)
	{
		$this->scale = $scale;

		return $this;
	}

	/**
	 * Возвращает точность (количество знаков, после запятой)
	 *
	 * @api
	 *
	 * @return int|null
	 */
	public function getScale()
	{
		return $this->scale;
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базе данных
	 *
	 * @api
	 *
	 * @param                   $value
	 * @param FloatField|null   $obj
	 *
	 * @return float|mixed|string
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			if (!is_null($value))
			{
				$scale = $obj->getScale();
				$value = round($value,$scale);
			}
		}

		return $value;
	}

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @api
	 *
	 * @param                   $value
	 * @param FloatField|null   $obj
	 *
	 * @return array|float|mixed
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			if (!is_null($value))
			{
				$scale = $obj->getScale();
				$value = round($value,$scale);
			}
		}

		return $value;
	}

	/**
	 * Возвращает значение поля в SQL формате
	 *
	 * @param float $value
	 *
	 * @return string
	 */
	public function getSqlValue ($value)
	{
		return (string) $value;
	}
}
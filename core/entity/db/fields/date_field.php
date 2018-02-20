<?php
/**
 * MSergeev\Core\Entity\Db\Fields\DateField
 * Сущность поля базы данных, содержащего дату
 *
 * @package MSergeev\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity\Db\Fields;

use MSergeev\Core\Exception\ArgumentNullException;
use MSergeev\Core\Exception\ArgumentOutOfRangeException;
use MSergeev\Core\Entity\Type\Date;
use MSergeev\Core\Exception\ArgumentTypeException;

/**
 * Class DateField
 * @package MSergeev\Core\Entity\Db\Fields
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
class DateField extends ScalarField
{
	/**
	 * Конструктор
	 *
	 * @param string $name
	 * @param array  $parameters
	 * @since 0.1.0
	 */
	public function __construct($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->dataType = 'date';
		$this->fieldType = 'MSergeev\Core\Entity\Type\Date';
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базу данных
	 *
	 * @api
	 *
	 * @param Date|null $value
	 * @param DateField|null $obj
	 *
	 * @return bool|mixed|string|void
	 * @since 0.1.0
	 */
	public static function saveDataModification ($value, DateField $obj=null)
	{
		if (!is_null($value) && $value=='')
		{
			$value=null;
		}

		if (!is_null($value))
		{
			try
			{
				if (!($value instanceof Date))
				{
					throw new ArgumentTypeException((string)$value,'MSergeev\Core\Entity\Type\Date');
				}
				else
				{
					$value = $value->getDateDB();
				}
			}
			catch (ArgumentTypeException $e)
			{
				die($e->showException());
			}
		}

		return $value;
	}

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @api
	 *
	 * @param Date|string $value
	 * @param DateField|null $obj
	 *
	 * @return array|bool|mixed|string
	 * @since 0.1.0
	 */
	public static function fetchDataModification ($value, DateField $obj=null)
	{
		if (!is_null($value))
		{
			if ($value instanceof Date)
			{
				$date = $value;
			}
			else
			{
				$date = new Date($value, 'db');
			}
			if (!is_null($date->getTimestamp()))
			{
				$value = $date;
			}
		}
		return $value;
	}

	/**
	 * Оставлена для совместимости
	 * @deprecated
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 * @since 0.2.0
	 */
	public static function validate ($value)
	{
		return $value;
	}
}
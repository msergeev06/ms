<?php
/**
 * Ms\Core\Entity\Db\Fields\TextField
 * Сущность поля базы данных, содержащего текст
 *
 * @package Ms\Core
 * @subpackage Entity\Db\Fields
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Class TextField
 * @package Ms\Core
 * @subpackage Entity\Db\Fields
 * @extends StringField
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
 * @method StringField getSize()    Возвращает размер поля в базе данных (в символах)
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
class TextField extends StringField
{
	/**
	 * Конструктор
	 *
	 * @param string $name
	 * @param array  $parameters
	 * @since 0.1.0
	 */
	function __construct($name, $parameters = array())
	{
		parent::__construct($name, $parameters);

		$this->dataType = $this->fieldType = 'text';

	}
}
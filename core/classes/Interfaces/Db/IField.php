<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Interfaces\Db;

use Ms\Core\Entity\Db\Links\LinkedField;

/**
 * Интерфейс Ms\Core\Interfaces\Db\IField
 * Интерфейс полей таблицы БД
 */
interface IField
{
    /**
     * Устанавливает связь поля с записью в той же, либо другой таблице
     *
     * @param LinkedField $linkedField
     *
     * @return IField
     */
    public function setLink (LinkedField $linkedField): IField;

    /**
     * Возвращает объект с описанием связи поля с другим полем
     *
     * @return LinkedField|null
     */
    public function getLink ();

    /**
     * Устанавливает описание поля
     *
     * @param string $title
     *
     * @return IField
     */
    public function setTitle (string $title): IField;

    /**
     * Возвращает описание поля
     *
     * @return string
     */
    public function getTitle (): string;

    /**
     * Устанавливает имя метода, который преобразует полученные из БД данные поля
     *
     * @param string $methodName
     *
     * @return IField
     */
    public function setFetchDataModification (string $methodName): IField;

    /**
     * Возвращает название функции для обработки значений полученных из базы данных
     *
     * @return callable|null
     */
    public function getFetchDataModification ();

    public function fetchDataModification ($value);

    public function saveDataModification ($value);

    /**
     * Устанавливает имя метода, который преобразует данные поля, для сохранения в БД
     *
     * @param string $methodName
     *
     * @return IField
     */
    public function setSaveDataModification (string $methodName): IField;

    /**
     * Возвращает название функции для обработки значений перед сохранением в базу данных
     *
     * @return callable|null
     */
    public function getSaveDataModification ();

    /**
     * Устанавливает флаг того, что поле содержит сериализованные данные
     *
     * @param bool $isSerialized
     *
     * @return IField
     */
    public function setSerialized (bool $isSerialized = true): IField;

    /**
     * Возвращает флаг, обозначающий факт того,
     * является ли значение данного поля сериализованным массивом
     *
     * @return bool
     */
    public function isSerialized (): bool;

    /**
     * Устанавливает название поля в коде
     *
     * @param string $fieldName
     *
     * @return IField
     */
    public function setName (string $fieldName): IField;

    /**
     * Возвращает название поля в коде
     *
     * @return string
     */
    public function getName (): string;

    /**
     * Устанавливает тип поля в БД
     *
     * @param string $fieldDataType
     *
     * @return IField
     */
    public function setDataType (string $fieldDataType): IField;

    /**
     * Возвращает тип поля в БД
     *
     * @return string
     */
    public function getDataType ();

    /**
     * Устанавливает тип поля в коде
     *
     * @param string $fieldType
     *
     * @return mixed
     */
    public function setFieldType (string $fieldType): IField;

    /**
     * Возвращает тип поля в коде
     *
     * @return string
     */
    public function getFieldType ();

    /**
     * Сериализует массив
     *
     * @param $value
     *
     * @return string
     */
    public function serialize ($value): string;

    /**
     * Десериализирует массив
     *
     * @param $value
     *
     * @return array
     */
    public function unserialize ($value): array;

    /**
     * Возвращает имя класса объекта
     *
     * @return string
     */
    public function getClassName (): string;

    /**
     * Возвращает массив разрешенных для поля значений, либо null
     *
     * @return array|null
     */
    public function getAllowedValues ();

    /**
     * Устанавливает список значений, которые может принимать поле
     *
     * @param array $allowedValues
     *
     * @return IField
     */
    public function setAllowedValues (array $allowedValues): IField;

    /**
     * Возвращает массив с диапазоном разрешенных значений для поля, либо null
     *
     * @return array|null
     */
    public function getAllowedValuesRange ();

    /**
     * Устанавливает диапазон значений, которые может принимать поле (для числовых значений)
     *
     * @param float $rangeMin Минимальное значение
     * @param float $rangeMax Максимальное значение
     *
     * @return IField
     */
    public function setAllowedValuesRange (float $rangeMin, float $rangeMax): IField;

    /**
     * Возвращает название поля в базе данных
     *
     * @return string
     */
    public function getColumnName (): string;

    /**
     * Устанавливает название поля в БД
     *
     * @param string $columnName
     *
     * @return IField
     */
    public function setColumnName (string $columnName): IField;

    /**
     * Возвращает значение поля по-умолчанию
     *
     * @param string $type Тип значения по-умолчанию
     *
     * @return callable|mixed|null
     */
    public function getDefaultValue (string $type);

    /**
     * Устанавливает значение по-умолчанию для всех действий: CREATE, INSERT, UPDATE
     *
     * @param mixed $defaultValue
     *
     * @return IField
     */
    public function setDefaultValue ($defaultValue): IField;

    /**
     * Возвращает значение поля в SQL формате
     *
     * @param mixed $value
     *
     * @return string
     */
    public function getSqlValue ($value): string;

    /**
     * Возвращает флаг того, используется ли для поля auto increment
     *
     * @return bool
     */
    public function isAutocomplete (): bool;

    /**
     * Возвращает флаг того, что значение по-умолчанию является функцией SQL
     *
     * @param string $type Тип значения по-умолчанию
     *
     * @return bool
     */
    public function isDefaultSql (string $type): bool;

    /**
     * Возвращает флаг того, является ли поле PRIMARY
     *
     * @return bool
     */
    public function isPrimary (): bool;

    /**
     * Возвращает флаг того, является ли поле обязательным
     *
     * @return bool
     */
    public function isRequired (): bool;

    /**
     * Возвращает флаг того, что значение по-умолчанию в таблице для обязательного поля равно NULL
     *
     * @return bool
     */
    public function isRequiredNull (): bool;

    /**
     * Возвращает флаг того, являются ли значения поля уникальными
     *
     * @return bool
     */
    public function isUnique (): bool;

    /**
     * Устанавливает флаг того, что значение поля является автоинкрементным
     *
     * @param bool $isAutocomplete
     *
     * @return IField
     */
    public function setAutocomplete (bool $isAutocomplete = true): IField;

    /**
     * Устанавливает значение по-умолчанию для действия CREATE
     *
     * @param mixed $defaultCreate
     *
     * @return IField
     */
    public function setDefaultCreate ($defaultCreate): IField;

    /**
     * Устанавливает флаг того, что значением по-умолчанию для действия CREATE является SQL
     *
     * @param bool $isDefaultCreateSql
     *
     * @return IField
     */
    public function setDefaultCreateSql (bool $isDefaultCreateSql = true): IField;

    /**
     * Устанавливает значение по-умолчанию для действия INSERT
     *
     * @param mixed $defaultInsert
     *
     * @return IField
     */
    public function setDefaultInsert ($defaultInsert): IField;

    /**
     * Устанавливает флаг того, что значение по-умолчанию для действия INSERT является SQL
     *
     * @param bool $isDefaultInsertSql
     *
     * @return IField
     */
    public function setDefaultInsertSql (bool $isDefaultInsertSql = true): IField;

    /**
     * Устанавливает значение по-умолчанию для действия UPDATE
     *
     * @param mixed $defaultUpdate
     *
     * @return IField
     */
    public function setDefaultUpdate ($defaultUpdate): IField;

    /**
     * Устанавливает флаг того, что значение по-умолчанию для действия INSERT является SQL
     *
     * @param bool $isDefaultUpdateSql
     *
     * @return IField
     */
    public function setDefaultUpdateSql (bool $isDefaultUpdateSql = true): IField;

    /**
     * Устанавливает флаг того, что описание значения по-умолчанию для всех действий является SQL
     *
     * @param bool $isDefaultValueSql
     *
     * @return IField
     */
    public function setDefaultValueSql (bool $isDefaultValueSql = true): IField;

    /**
     * Устанавливает флаг PRIMARY KEY поля
     *
     * @param bool $isPrimary
     *
     * @return IField
     */
    public function setPrimary (bool $isPrimary = true): IField;

    /**
     * Устанавливает флаг того, что поля является обязательным
     *
     * @param bool $isRequired
     *
     * @return IField
     */
    public function setRequired (bool $isRequired = true): IField;

    /**
     * Устанавливает флаг того, что значение обязательного поля может быть NULL
     *
     * @param bool $isRequiredNull
     *
     * @return IField
     */
    public function setRequiredNull (bool $isRequiredNull = true): IField;

    /**
     * Устанавливает флаг того, что значения поля уникальны
     *
     * @param bool $isUnique
     *
     * @return IField
     */
    public function setUnique (bool $isUnique = true): IField;

    /**
     * Устанавливает список возможных значений поля
     *
     * @param array $arValues
     *
     * @return IField
     */
    public function setValues (array $arValues): IField;

    /**
     * Возвращает SQL код устанавливающий размерность поля, если необходимо, либо пустую строку
     *
     * @return mixed
     */
    public function getSizeSql (): string;
}

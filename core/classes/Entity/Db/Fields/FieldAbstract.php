<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Entity\Db\Links\LinkedField;
use Ms\Core\Interfaces\Db\IField;

/**
 * Абстрактный класс Ms\Core\Entity\Db\Fields\FieldAbstract
 * Сущность поля базы данных
 */
abstract class FieldAbstract implements IField
{
    /**
     * @var string Название поля в таблице БД
     */
    protected $name;
    /**
     * @var string Тип поля в базе данных
     */
    protected $dataType;
    /**
     * @var string Тип поля в API
     */
    protected $fieldType;
    /**
     * @var array Параметры инициализации
     */
    protected $initialParameters = [];
    /**
     * @var string|null Описание поля
     */
    protected $title = null;
    /**
     * @var bool Является ли значение поля сериализованным массивом
     */
    protected $isSerialized = false;
    /**
     * @var FieldAbstract|null Родительское поле
     */
    protected $parentField = null;
    /**
     * @var null|callback Функция обработки полученных значений из базы
     */
    protected $fetchDataModification = null;
    /**
     * @var null|callback Функция обработки перед записью значений в базу
     */
    protected $saveDataModification = null;
    /**
     * @var LinkedField|null Связь поля таблицы
     */
    protected $link = null;

    /**
     * Конструктор. Обрабатывает начальные параметры поля
     *
     * @param string $fieldName
     */
    public function __construct (string $fieldName)
    {
        $this->setName($fieldName);
    }

    /**
     * Устанавливает связь поля с записью в той же, либо другой таблице
     *
     * @param LinkedField $linkedField
     *
     * @return $this
     * @unittest
     */
    public function setLink (LinkedField $linkedField): IField
    {
        $this->link = $linkedField;

        return $this;
    }

    /**
     * Устанавливает описание поля
     *
     * @param string $title
     *
     * @return $this
     * @unittest
     */
    public function setTitle (string $title): IField
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Устанавливает имя метода, который преобразует полученные из БД данные поля
     *
     * @param string $methodName Имя метода
     *
     * @return $this
     * @unittest
     */
    public function setFetchDataModification (string $methodName): IField
    {
        $this->fetchDataModification = $methodName;

        return $this;
    }

    /**
     * Устанавливает имя метода, который преобразует данные поля, для сохранения в БД
     *
     * @param string $methodName Имя метода
     *
     * @return $this
     * @unittest
     */
    public function setSaveDataModification (string $methodName): IField
    {
        $this->saveDataModification = $methodName;

        return $this;
    }

    /**
     * Устанавливает флаг того, что поле содержит сериализованные данные
     *
     * @param bool $isSerialized
     *
     * @return $this
     * @unittest
     */
    public function setSerialized (bool $isSerialized = true): IField
    {
        $this->isSerialized = $isSerialized;

        return $this;
    }

    /**
     * Устанавливает родительское поле
     * //TODO: Что это и для чего?
     *
     * @param string $parentField
     *
     * @return $this
     */
    public function setParentField (string $parentField)
    {
        $this->parentField = $parentField;

        return $this;
    }

    /**
     * Возвращает название поля в коде
     *
     * @return string
     * @unittest
     */
    public function getName (): string
    {
        return $this->name;
    }

    /**
     * Возвращает описание поля
     *
     * @return string
     * @unittest
     */
    public function getTitle (): string
    {
        return $this->title;
    }

    /**
     * Возвращает тип поля в БД
     *
     * @return string
     * @unittest
     */
    public function getDataType ()
    {
        return $this->dataType;
    }

    /**
     * Возвращает тип поля в API
     *
     * @return string
     * @unittest
     */
    public function getFieldType ()
    {
        return $this->fieldType;
    }

    /**
     * Возвращает объект родительского поля
     * //TODO: И это зачем?
     *
     * @return FieldAbstract
     */
    public function getParentField ()
    {
        return $this->parentField;
    }

    /**
     * Возвращает строку - связь поля с другим полем
     *
     * @return LinkedField|null
     * @unittest
     */
    public function getLink ()
    {
        return $this->link;
    }

    /**
     * Сериализует массив
     *
     * @param array|string $value Массив
     *
     * @return string
     * @unittest
     */
    public function serialize ($value): string
    {
        if (!is_string($value))
        {
            $value = serialize($value);
        }

        return $value;
    }

    /**
     * Десериализирует массив
     *
     * @param string $value Сериализованный массив
     *
     * @return array
     * @unittest
     */
    public function unserialize ($value): array
    {
        if (is_array($value))
        {
            return $value;
        }

        return unserialize($value);
    }

    /**
     * Возвращает название функции для обработки значений полученных из базы данных
     *
     * @return callable|null
     * @unittest
     */
    public function getFetchDataModification ()
    {
        return $this->fetchDataModification;
    }

    /**
     * Возвращает флаг, обозначающий факт того,
     * является ли значение данного поля сериализованным массивом
     *
     * @return bool
     * @unittest
     */
    public function isSerialized (): bool
    {
        return $this->isSerialized;
    }

    /**
     * Возвращает название функции для обработки значений перед сохранением в базу данных
     *
     * @return callable|null
     * @unittest
     */
    public function getSaveDataModification ()
    {
        return $this->saveDataModification;
    }

    /**
     * Возвращает имя класса объекта
     *
     * @return string
     * @unittest
     */
    public function getClassName (): string
    {
        return get_called_class();
    }

    /**
     * Устанавливает название поля в коде
     *
     * @param string $fieldName
     *
     * @return IField
     * @unittest
     */
    public function setName (string $fieldName): IField
    {
        $this->name = $fieldName;

        return $this;
    }

    /**
     * Устанавливает тип поля в БД
     *
     * @param string $fieldDataType
     *
     * @return IField
     * @unittest
     */
    public function setDataType (string $fieldDataType): IField
    {
        $this->dataType = $fieldDataType;

        return $this;
    }

    /**
     * Устанавливает тип поля в коде
     *
     * @param string $fieldType
     *
     * @return IField
     * @unittest
     */
    public function setFieldType (string $fieldType): IField
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    /**
     * Возвращает SQL код устанавливающий размерность поля, если необходимо, либо пустую строку
     *
     * @return string
     */
    public function getSizeSql (): string
    {
        return '';
    }
}
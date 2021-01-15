<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Tables;

use Ms\Core\Entity\Db\Fields\ScalarFieldAbstract;
use Ms\Core\Entity\System\Dictionary;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Tables\FieldsCollection
 * Коллекция объектов полей таблицы
 */
class FieldsCollection extends Dictionary
{
    /**
     * Добавляет поле в коллекцию
     *
     * @param IField $field Объект поля таблицы БД
     *
     * @return $this
     * @unittest
     */
    public function addField (IField $field)
    {
        $this->offsetSet($field->getName(), $field);

        return $this;
    }

    /**
     * Удаляет из коллекции объект поля, имя которого передано в параметре, если оно существует в коллекции
     *
     * @param string $fieldName Имя удаляемого поля
     *
     * @return $this
     * @unittest
     */
    public function deleteField (string $fieldName)
    {
        if ($this->isExists($fieldName))
        {
            $this->offsetUnset($fieldName);
        }

        return $this;
    }

    /**
     * Возвращает объект поля БД по его имени
     *
     * @param string $fieldName
     *
     * @return IField|null
     * @unittest
     */
    public function getField (string $fieldName)
    {
        if ($this->offsetExists($fieldName))
        {
            return $this->offsetGet($fieldName);
        }
        elseif (!$this->isEmpty())
        {
            /**
             * @var string $fieldName
             * @var IField $field
             */
            foreach ($this->values as $name => $field)
            {
                if ($field->getColumnName() == $fieldName)
                {
                    return $field;
                }
            }
        }

        return null;
    }

    /**
     * Возвращает поля, для которых установлены значения по-умолчанию, для заданного типа SQL запроса
     *
     * @param string $type Тип SQL запроса
     *
     * @return FieldsCollection
     * @unittest
     */
    public function getFieldsWithDefaultValues (string $type = ScalarFieldAbstract::DEFAULT_VALUE_TYPE_INSERT)
    {
        $collection = new self();
        if (!$this->isEmpty())
        {
            /** @var IField $objField */
            foreach ($this->values as $objField)
            {
                if (!is_null($objField->getDefaultValue($type)))
                {
                    $collection->addField($objField);
                }
            }
        }

        return $collection;
    }

    /**
     * Возвращает список имен полей в коллекции
     *
     * @return array
     * @unittest
     */
    public function getList ()
    {
        if ($this->isEmpty())
        {
            return [];
        }

        $arReturn = [];

        /** @var IField $field */
        foreach ($this->values as $field)
        {
            $arReturn[] = $field->getName();
        }

        return $arReturn;
    }

    /**
     * Возвращает объект PRIMARY поля таблицы
     *
     * @return IField|null
     * @unittest
     */
    public function getPrimaryField ()
    {
        if (!$this->isEmpty())
        {
            /** @var IField $objField */
            foreach ($this->values as $objField)
            {
                if ($objField->isPrimary())
                {
                    return $objField;
                }
            }
        }

        return null;
    }

    /**
     * Возвращает TRUE, если поле с заданным именем существует в коллекции
     *
     * @param string $fieldName Имя поля
     *
     * @return bool
     * @unittest
     */
    public function isExists (string $fieldName)
    {
        if ($this->isEmpty())
        {
            return false;
        }
        elseif ($this->offsetExists($fieldName))
        {
            return true;
        }
        else
        {
            /**
             * @var string $key
             * @var IField $field
             */
            foreach ($this->values as $key => $field)
            {
                if ($field->getColumnName() == $fieldName)
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Добавляет к текущей коллекции данные из переданной коллекции этого же типа
     *
     * @param FieldsCollection $mergedCollection Объединяемая коллекция
     *
     * @return $this
     * @unittest
     */
    public function merge (FieldsCollection $mergedCollection)
    {
        if ($mergedCollection->isEmpty())
        {
            return $this;
        }

        /**
         * @var string $fieldName
         * @var IField $field
         */
        foreach ($mergedCollection as $fieldName => $field)
        {
            $this->offsetSet($fieldName, $field);
        }

        return $this;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Arguments\ArgumentException;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Fields\DateTimeField
 * Сущность поля базы данных, содержащего дату и время
 */
class DateTimeField extends ScalarFieldAbstract
{
    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     */
    public function __construct ($name)
    {
        parent::__construct($name);

        $this->dataType = 'datetime';
        $this->fieldType = Date::class;
    }

    /**
     * Устанавливает значение по-умолчанию для всех действий
     *
     * @param Date|string $defaultValue
     *
     * @return $this
     * @throws ArgumentTypeException
     * @unittest
     */
    public function setDefaultValue ($defaultValue): IField
    {
        if (!($defaultValue instanceof Date) && !is_string($defaultValue))
        {
            throw new ArgumentTypeException(
                '$defaultValue',
                Date::class.'|string'
            );
        }

        parent::setDefaultValue($defaultValue);

        return $this;
    }

    /**
     * Устанавливает значение по-умолчанию для действия CREATE
     *
     * @param Date|string $defaultCreate
     *
     * @return $this
     * @throws ArgumentTypeException
     * @unittest
     */
    public function setDefaultCreate ($defaultCreate): IField
    {
        if (!($defaultCreate instanceof Date) && !is_string($defaultCreate))
        {
            throw new ArgumentTypeException(
                '$defaultCreate',
                Date::class.'|string'
            );
        }

        parent::setDefaultCreate($defaultCreate);

        return $this;
    }

    /**
     * Устанавливает значение по-умолчанию для действия INSERT
     *
     * @param Date|string $defaultInsert
     *
     * @return $this
     * @throws ArgumentTypeException
     * @unittest
     */
    public function setDefaultInsert ($defaultInsert): IField
    {
        if (!($defaultInsert instanceof Date) && !is_string($defaultInsert))
        {
            throw new ArgumentTypeException(
                '$defaultInsert',
                Date::class.'|string'
            );
        }

        parent::setDefaultInsert($defaultInsert);

        return $this;
    }

    /**
     * Устанавливает значение по-умолчанию для действия UPDATE
     *
     * @param null|Date|string $defaultUpdate
     *
     * @return $this
     * @throws ArgumentTypeException
     * @unittest
     */
    public function setDefaultUpdate ($defaultUpdate): IField
    {
        if (!is_null($defaultUpdate) && !($defaultUpdate instanceof Date) && !is_string($defaultUpdate))
        {
            throw new ArgumentTypeException(
                '$defaultUpdate',
                Date::class.'|string'
            );
        }

        parent::setDefaultUpdate($defaultUpdate);

        return $this;
    }

    /**
     * Обрабатывает значение поля перед сохранением в базу данных
     *
     * @param Date               $value
     *
     * @return bool|mixed|string
     * @unittest
     */
    public function saveDataModification ($value)
    {
        if (!is_null($value))
        {
            $value = $value->getDateTimeDB();
        }

        return $value;
    }

    /**
     * Обрабатывает значение поля после получения из базы данных
     *
     * @param string             $value
     *
     * @return Date|null
     * @unittest
     */
    public function fetchDataModification ($value)
    {
        if (!is_null($value))
        {
            try
            {
                $date = new Date($value, 'db_datetime');
            }
            catch (SystemException $e)
            {
                return null;
            }
            if (!is_null($date->getTimestamp()))
            {
                $value = $date;
            }
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     * @unittest
     */
    public function getSqlValue ($value): string
    {
        if ($value instanceof Date)
        {
            return "'" . $value->getDateTimeDB() . "'";
        }
        else
        {
            return "'" . $value . "'";
        }
    }
}
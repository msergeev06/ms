<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Entity\Db\Fields\DateField
 * Сущность поля базы данных, содержащего дату
 */
class DateField extends ScalarFieldAbstract
{
    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     */
    public function __construct ($name)
    {
        parent::__construct($name);

        $this->dataType = 'date';
        $this->fieldType = Date::class;
    }

    /**
     * Обрабатывает значение поля после получения из базы данных
     *
     * @param Date|string $value
     *
     * @return null|Date
     * @unittest
     */
    public function fetchDataModification ($value)
    {
        if (!is_null($value))
        {
            if ($value instanceof Date)
            {
                $date = $value;
            }
            else
            {
                if (Date::checkDate($value))
                {
                    try
                    {
                        $date = new Date($value, 'db');
                    }
                    catch (SystemException $e)
                    {
                        return null;
                    }
                }
                else
                {
                    return null;
                }
            }
            if (($date instanceof Date) && !is_null($date->getTimestamp()))
            {
                $value = $date;
            }
        }

        return $value;
    }

    /**
     * Возвращает значение поля в формате SQL
     *
     * @param Date|string $value
     *
     * @return string
     * @unittest
     */
    public function getSqlValue ($value): string
    {
        if ($value instanceof Date)
        {
            return "'" . $value->getDateDB() . "'";
        }
        else
        {
            return "'" . $value . "'";
        }
    }

    /**
     * Обрабатывает значение поля перед сохранением в базу данных
     *
     * @param Date|null $value
     *
     * @return bool|mixed|string
     * @throws ArgumentTypeException
     * @unittest
     */
    public function saveDataModification ($value)
    {
        if (!is_null($value) && $value == '')
        {
            $value = null;
        }

        if (!is_null($value))
        {
            if (!($value instanceof Date))
            {
                throw new ArgumentTypeException((string)$value, Date::class);
            }
            else
            {
                $value = $value->getDateDB();
            }
        }

        return $value;
    }
}
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
use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Entity\Db\Fields\TimeField
 * Сущность поля базы данных, содержащего время
 */
class TimeField extends ScalarFieldAbstract
{
    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     */
    public function __construct ($name)
    {
        parent::__construct($name);

        $this->dataType = 'time';
        $this->fieldType = Date::class;
    }

    /**
     * Обрабатывает значение поля после получения из базы данных
     *
     * @param $value
     *
     * @return array|mixed
     * @unittest
     */
    public function fetchDataModification ($value)
    {
        if (!is_null($value))
        {
            try
            {
                $value = new Date($value, 'site_time');
            }
            catch (SystemException $e)
            {
                return $value;
            }

            $value = parent::fetchDataModification($value);
        }

        return $value;
    }

    /**
     * Возвращает значение поля в формате SQL
     *
     * @param mixed $value
     *
     * @return string
     * @unittest
     */
    public function getSqlValue ($value): string
    {
        if ($value instanceof Date)
        {
            return "'" . $value->getTimeSite() . "'";
        }
        else
        {
            return "'" . $value . "'";
        }
    }

    /**
     * Обрабатывает значение поля перед сохранением в базу данных
     *
     * @param Date $value
     *
     * @return bool|mixed|string
     * @unittest
     */
    public function saveDataModification ($value)
    {
        if (!is_null($value))
        {
            //$value = self::validate($value, $obj);
            $value = $value->getTime();

            $value = parent::saveDataModification($value);
        }

        return $value;
    }
}
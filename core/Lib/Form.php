<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\Type\Date;

Loc::includeLocFile(__FILE__);

/**
 * Класс Ms\Core\Lib\Form
 * Методы обработки форм
 */
class Form
{
    /**
     * Метод по-умолчанию для проверки значений полей формы. Возвращает всегда true
     *
     * @return bool
     */
    public static function checkAll()
    {
        return true;
    }

    /**
     * Проверяет значение поля формы input type="text" на отсутствие спец символов
     *
     * @param string $value
     *
     * @return bool|string
     */
    public static function checkInputText($value)
    {
        if (preg_match("/[\<|\>]/", $value))
        {
            return Loc::getCoreMessage('error_wrong_symbols');
        }
        else
        {
            return true;
        }
    }

    /**
     * Проверяет значение поля формы textarea на отсутствие спец символов
     *
     * @param string $value
     *
     * @return bool|string
     */
    public static function checkTextArea($value)
    {
        if (preg_match("/[\<|\>]/", $value))
        {
            return Loc::getCoreMessage('error_wrong_symbols');
        }
        else
        {
            return true;
        }
    }

    /**
     * Проверяет значение поля формы input type="number"
     *
     * @param string|float|int $value
     * @param bool|float|int   $step
     * @param bool|float|int   $min
     * @param bool|float|int   $max
     *
     * @return bool|string
     */
    public static function checkInputNumber($value, $step = false, $min = false, $max = false)
    {
        $value = str_replace(',', '.', $value);
        $value = str_replace(' ', '', $value);
        if ($step !== false)
        {
            if (((float)$value % (float)$step))
            {
                return Loc::getCoreMessage('error_step', ['STEP' => $step]);
            }
        }
        if ($min !== false)
        {
            if ((float)$value < (float)$min)
            {
                return Loc::getCoreMessage('error_bigger_or_equal', ['MIN' => $min]);
            }
        }
        if ($max !== false)
        {
            if ((float)$value > (float)$max)
            {
                return Loc::getCoreMessage('error_low_or_equal', ['MAX' => $max]);
            }
        }

        return true;
    }

    /**
     * Проверяет значение поля формы input type="date"
     *
     * @param Date $value
     * @param Date $min
     * @param Date $max
     *
     * @return bool|string
     */
    public static function checkInputDate(Date $value, Date $min = null, Date $max = null)
    {
        if (is_null($min))
        {
            $min = new Date('0000-01-01 00:00:00', 'db_datetime');
        }
        if (is_null($max))
        {
            $max = new Date('9999-12-31 23:59:59', 'db_datetime');
        }
        //		$valueTime = $value->getTimestamp();
        //		$minTime = $min->getTimestamp();
        //		$maxTime = $max->getTimestamp();

        //		if ($valueTime >= $minTime && $valueTime <= $maxTime)
        if ($value >= $min && $value <= $max)
        {
            return true;
        }
        else
        {
            return Loc::getCoreMessage(
                'error_date_between',
                ['START' => $min->getDateSite(), 'END' => $max->getDateSite()]
            );
        }
    }

    /**
     * Проверяет значение поля формы input type="date"
     *
     * @param Date $value
     * @param Date $min
     * @param Date $max
     *
     * @return bool|string
     */
    public static function checkInputMonth(Date $value, Date $min = null, Date $max = null)
    {
        if (is_null($min))
        {
            $min = new Date('0000-01-01 00:00:00', 'db_datetime');
        }
        if (is_null($max))
        {
            $max = new Date('9999-12-31 23:59:59', 'db_datetime');
        }

        if ($value >= $min && $value <= $max)
        {
            return true;
        }
        else
        {
            return Loc::getCoreMessage(
                'error_date_between',
                ['START' => $min->getDateSite(), 'END' => $max->getDateSite()]
            );
        }
    }
}
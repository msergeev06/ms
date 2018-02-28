<?php
/**
 * Ms\Core\Lib\Form
 * Функции обработки форм
 *
 * @package Ms\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\Type\Date;

class Form
{
	/**
	 * Метод по-умолчанию для проверки значений полей формы. Возвращает всегда true
	 *
	 * @return bool
	 */
	public static function checkAll ()
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
	public static function checkInputText ($value)
	{
		if ( preg_match( "/[\<|\>]/", $value) )
		{
			return "Использованы недопустимые символы";
		}
		else
		{
			return true;
		}
	}

	/**
	 * Проверяет значение поля формы input type="number"
	 *
	 * @param string|float|int  $value
	 * @param bool|float|int    $step
	 * @param bool|float|int    $min
	 * @param bool|float|int    $max
	 *
	 * @return bool|string
	 */
	public static function checkInputNumber ($value, $step=false, $min=false, $max=false)
	{
		$value = str_replace(',','.',$value);
		$value = str_replace(' ','',$value);
		if ($step !== false)
		{
			if (((float)$value % (float)$step))
			{
				return "Число должно быть кратно ".$step;
			}
		}
		if ($min !== false)
		{
			if ((float)$value<(float)$min)
			{
				return "Число должно быть больше или равно ".$min;
			}
		}
		if ($max !== false)
		{
			if ((float)$value>(float)$max)
			{
				return "Число должно быть меньше или равно ".$max;
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
	public static function checkInputDate (Date $value, Date $min=null, Date $max=null)
	{
		if (is_null($min))
		{
			$min = new Date('1970-01-01 00:00:00','db_datetime');
		}
		if (is_null($max))
		{
			$max = new Date('2038-01-18 23:00:00','db_datetime');
		}
		$valueTime = $value->getTimestamp();
		$minTime = $min->getTimestamp();
		$maxTime = $max->getTimestamp();

		if ($valueTime >= $minTime && $valueTime <= $maxTime)
		{
			return true;
		}
		else
		{
			return 'Дата должна быть между '.$min->getDateSite().' и '.$max->getDateSite();
		}
	}
}
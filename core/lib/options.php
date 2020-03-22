<?php
/**
 * Ms\Core\Lib\Options
 * Опции ядра и модулей.
 * Используется для хранения и получения различных опций ядра и установленных модулей
 *
 * @package Ms\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @since 0.1.0
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\Db\Query;
use Ms\Core\Entity\Application;
use Ms\Core\Exception\ValidateException;
use Ms\Core\Tables;

class Options
{
	/**
	 * @var array Массив всех известных в данной сессии опций
	 */
	protected static $arOptions;

	/**
	 * Инициализация пакета. Загружает данные из файла ядра default_options.php
	 *
	 * @api
	 */
	public static function init ()
	{
		$arDefaultOptions = include(Application::getInstance()->getSettings()->getCoreRoot()."/default_options.php");

		if ($arDefaultOptions && !empty($arDefaultOptions))
		{
			foreach ($arDefaultOptions as $option => $value)
			{
				self::$arOptions[strtoupper($option)] = $value;
			}
		}
	}

	/**
	 * Функция обертка, возвращающая значение указанной опции в виде строки
	 *
	 * @api
	 *
	 * @param string $optionName Имя опции
	 * @param null|string $optionDefaultValue Значение опции по-умолчанию
	 *
	 * @return bool|string Значение указанной опции, либо false
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/options/method_get_option_str
	 */
	public static function getOptionStr ($optionName, $optionDefaultValue = null)
	{
		$optionName = strtoupper($optionName);
		$optionVal = self::getOption ($optionName, $optionDefaultValue);

		if ($optionVal!==false)
		{
			return strval($optionVal);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Функция обертка, возвращающая значение указанной опции в виде целого числа
	 *
	 * @api
	 *
	 * @param string $optionName Имя опции
	 * @param null|int $optionDefaultValue Значение опции по-умолчанию
	 *
	 * @return bool|int Целочисленное значение указанной опции, либо false
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/options/method_get_option_int
	 */
	public static function getOptionInt($optionName, $optionDefaultValue = null)
	{
		$optionName = strtoupper($optionName);
		$optionVal = self::getOption($optionName, $optionDefaultValue);

		if ($optionVal!==false)
		{
			return intval($optionVal);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Функция обертка, возвращающая значение указанной опции в виде вещественного числа
	 *
	 * @api
	 *
	 * @param string $optionName Имя опции
	 * @param null|float $optionDefaultValue Значение опции по-умолчанию
	 *
	 * @return bool|float Вещественное значение указанной опции, либо false
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/options/method_get_option_float
	 */
	public static function getOptionFloat($optionName, $optionDefaultValue = null)
	{
		$optionName = strtoupper($optionName);
		$optionVal = self::getOption($optionName,$optionDefaultValue);

		if ($optionVal!==false)
		{
			return floatval($optionVal);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Функция обертка, возвращающая значение указанной опции в виде булевого значения
	 *
	 * @api
	 *
	 * @param string   $optionName         Имя опции
	 * @param null|int $optionDefaultValue Значение опции по-умолчанию, где (1 = true, 0 = false)
	 *
	 * @return bool
	 */
	public static function getOptionBool ($optionName, $optionDefaultValue = null)
	{
		$optionName = strtoupper($optionName);
		$optionVal = self::getOption($optionName, $optionDefaultValue);

		if ($optionVal!==false)
		{
			return Tools::validateBoolVal($optionVal);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Функция добавляющая опщии по-умолчанию, без записи новых в DB
	 *
	 * @api
	 *
	 * @param string $optionName  Название опции
	 * @param mixed  $optionValue Значение опции
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/options/method_set_default_option
	 */
	public static function setDefaultOption ($optionName, $optionValue)
	{
		$optionName = strtoupper($optionName);
		self::$arOptions[$optionName] = $optionValue;
	}

	/**
	 * Функция добавляет новые опции в базу данных и в текущую сессию
	 *
	 * @api
	 *
	 * @param string $optionName  Название опции
	 * @param mixed  $optionValue Значение опции
	 *
	 * @return bool true - опция сохранена, false - ошибка сохранения
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/options/method_set_option
	 */
	public static function setOption ($optionName, $optionValue)
	{
		$optionName = strtoupper($optionName);
		if (
			!isset(static::$arOptions[$optionName])
			|| static::$arOptions[$optionName] != $optionValue
		)
		{
			$arInsert = array(
				'NAME' => $optionName,
				'VALUE' => $optionValue
			);
			$result = Tables\OptionsTable::getOne(
				array(
					"filter" => array(
						"NAME" => $optionName
					)
				)
			);
			if ($result)
			{
				try
				{
					$res = Tables\OptionsTable::update($result['ID'],$arInsert);
					if ($res->getResult())
					{
						static::$arOptions[$optionName] = $optionValue;
						return true;
					}
					else
					{
						return false;
					}
				}
				catch (ValidateException $e)
				{
					return false;
				}
			}
			else
			{
				try
				{
					$res = Tables\OptionsTable::add($arInsert);
					if ($res->getResult())
					{
						static::$arOptions[$optionName] = $optionValue;

						return true;
					}
					else
					{
						return false;
					}
				}
				catch (ValidateException $e)
				{
					return false;
				}
			}
		}
		else
		{
			return true;
		}
	}

	/**
	 * Функция возвращает значение опции либо из массива,
	 * либо из базы данных, сохранив в массиве
	 *
	 * @ignore
	 *
	 * @param string $optionName Имя опции
	 * @param null|mixed $optionDefaultValue Значение опции по-умолчанию
	 *
	 * @return bool|mixed Значение опции, либо false
	 */
	protected static function getOption ($optionName, $optionDefaultValue = null)
	{
		$optionName = strtoupper($optionName);
		if (isset(static::$arOptions[$optionName])) {
			return static::$arOptions[$optionName];
		}
		else {
			$result = Tables\OptionsTable::getOne(
				array(
					"filter" => array(
						"NAME" => $optionName
					)
				)
			);
			if ($result)
			{
				static::$arOptions[$optionName] = $result['VALUE'];
				return $result['VALUE'];
			}
			elseif (!is_null($optionDefaultValue))
			{
				return $optionDefaultValue;
			}
			else
			{
				return false;
			}
		}

	}

}
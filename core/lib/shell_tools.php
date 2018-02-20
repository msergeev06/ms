<?php
/**
 * MSergeev
 *
 * Инструменты для работы в shell
 *
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2017 Mikhail Sergeev
 * @since 0.1.0
 */

namespace MSergeev\Core\Lib;

class ShellTools
{
	/**
	 * Цвета шрифта в консоли
	 *
	 * @var array
	 */
	private static $arTextColor = array(
		'black'         => '0;30',
		'dark_gray'     => '1;30',
		'blue'          => '0;34',
		'light_blue'    => '1;34',
		'green'         => '0;32',
		'light_green'   => '1;32',
		'cyan'          => '0;36',
		'light_cyan'    => '1;36',
		'red'           => '0;31',
		'light_red'     => '1;31',
		'purple'        => '0;35',
		'light_purple'  => '1;35',
		'brown'         => '0;33',
		'yellow'        => '1;33',
		'light_gray'    => '0;37',
		'white'         => '1;37'
	);

	/**
	 * Цвета фона в консоли
	 *
	 * @var array
	 */
	private static $arBackgroundColor = array(
		'black'         => '40',
		'red'           => '41',
		'green'         => '42',
		'yellow'        => '43',
		'blue'          => '44',
		'magenta'       => '45',
		'cyan'          => '46',
		'light_gray'    => '47'
	);

	/**
	 * Возвращает строку, которая будет выведена в консоли с нужным цветом текста и фона
	 *
	 * @param string        $string             Заданный текст
	 * @param null|string   $colorText          Код цвета текста
	 * @param null|string   $colorBackground    Код цвета фона
	 *
	 * @return string
	 */
	public static function getColoredString($string, $colorText=null, $colorBackground=null)
	{
		$strReturn = '';
		if (!is_null($colorText) && isset(self::$arTextColor[$colorText]))
		{
			$strReturn .= "\033[".self::$arTextColor[$colorText]."m";
		}
		if (!is_null($colorBackground) && isset(self::$arBackgroundColor[$colorBackground]))
		{
			$strReturn .= "\033[".self::$arBackgroundColor[$colorBackground]."m";
		}
		$strReturn .= $string."\033[0m";

		return $strReturn;
	}

	/**
	 * Возвращает список кодов цвета текста
	 *
	 * @return array
	 */
	public static function getTextColors ()
	{
		return array_keys(self::$arTextColor);
	}

	/**
	 * Возвращает список кодов цвета фона
	 *
	 * @return array
	 */
	public static function getBackgroundColors ()
	{
		return array_keys(self::$arBackgroundColor);
	}
}
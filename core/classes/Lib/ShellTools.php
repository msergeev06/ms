<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2017 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

/**
 * Класс Ms\Core\Lib\ShellTools
 * Инструменты для работы в shell
 */
class ShellTools
{
    const COLOR_TEXT_BLACK            = 'black';
    const COLOR_TEXT_DARK_GRAY        = 'dark_green';
    const COLOR_TEXT_BLUE             = 'blue';
    const COLOR_TEXT_LIGHT_BLUE       = 'light_blue';
    const COLOR_TEXT_GREEN            = 'green';
    const COLOR_TEXT_CYAN             = 'cyan';
    const COLOR_TEXT_LIGHT_CYAN       = 'light_cyan';
    const COLOR_TEXT_RED              = 'red';
    const COLOR_TEXT_LIGHT_RED        = 'light_red';
    const COLOR_TEXT_PURPLE           = 'purple';
    const COLOR_TEXT_LIGHT_PURPLE     = 'light_purple';
    const COLOR_TEXT_BROWN            = 'brown';
    const COLOR_TEXT_YELLOW           = 'yellow';
    const COLOR_TEXT_LIGHT_GRAY       = 'light_gray';
    const COLOR_TEXT_WHITE            = 'white';

    const COLOR_BACKGROUND_BLACK      = 'black';
    const COLOR_BACKGROUND_RED        = 'red';
    const COLOR_BACKGROUND_GREEN      = 'green';
    const COLOR_BACKGROUND_YELLOW     = 'yellow';
    const COLOR_BACKGROUND_BLUE       = 'blue';
    const COLOR_BACKGROUND_MAGENTA    = 'magenta';
    const COLOR_BACKGROUND_CYAN       = 'cyan';
    const COLOR_BACKGROUND_LIGHT_GRAY = 'light_gray';

    /**
     * Цвета фона в консоли
     *
     * @var array
     */
    protected static $arBackgroundColor
        = [
            'black'      => '40',
            'red'        => '41',
            'green'      => '42',
            'yellow'     => '43',
            'blue'       => '44',
            'magenta'    => '45',
            'cyan'       => '46',
            'light_gray' => '47'
        ];
    /**
     * Цвета шрифта в консоли
     *
     * @var array
     */
    protected static $arTextColor
        = [
            'black'        => '0;30',
            'dark_gray'    => '1;30',
            'blue'         => '0;34',
            'light_blue'   => '1;34',
            'green'        => '0;32',
            'light_green'  => '1;32',
            'cyan'         => '0;36',
            'light_cyan'   => '1;36',
            'red'          => '0;31',
            'light_red'    => '1;31',
            'purple'       => '0;35',
            'light_purple' => '1;35',
            'brown'        => '0;33',
            'yellow'       => '1;33',
            'light_gray'   => '0;37',
            'white'        => '1;37'
        ];

    /**
     * Возвращает список кодов цвета фона
     *
     * @return array
     * @unittest
     */
    public static function getBackgroundColors ()
    {
        return array_keys(self::$arBackgroundColor);
    }

    /**
     * Возвращает строку, которая будет выведена в консоли с нужным цветом текста и фона
     *
     * @param string      $string          Заданный текст
     * @param null|string $colorText       Код цвета текста
     * @param null|string $colorBackground Код цвета фона
     *
     * @return string
     * @unittest
     */
    public static function getColoredString ($string, $colorText = null, $colorBackground = null)
    {
        $strReturn = '';
        if (!is_null($colorText) && isset(self::$arTextColor[$colorText]))
        {
            $strReturn .= "\033[" . self::$arTextColor[$colorText] . "m";
        }
        if (!is_null($colorBackground) && isset(self::$arBackgroundColor[$colorBackground]))
        {
            $strReturn .= "\033[" . self::$arBackgroundColor[$colorBackground] . "m";
        }
        $strReturn .= $string . "\033[0m";

        return $strReturn;
    }

    /**
     * Возвращает список кодов цвета текста
     *
     * @return array
     * @unittest
     */
    public static function getTextColors ()
    {
        return array_keys(self::$arTextColor);
    }
}

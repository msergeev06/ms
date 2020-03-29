<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Interfaces\IAllErrors;

/**
 * Класс Ms\Core\Lib\Errors
 * Класс обработки ошибок ядра
 */
class Errors implements IAllErrors
{
    const ERROR_MODULE_WRONG_NAME = 100;
    const ERROR_MODULE_NOT_INSTALLED = 101;
    const ERROR_MODULE_INCLUDE = 102;
    const ERROR_MODULE_NAME_EMPTY_BRAND = 103;
    const ERROR_MODULE_WRONG_SYMBOLS_IN_MODULE_NAME = 104;
    const ERROR_MODULE_NAME_TO_LONG = 105;

    const ERROR_CLASS_WRONG_NAME = 110;
    const ERROR_CLASS_NOT_AUTOLOAD = 111;

    const ARGUMENT_EXCEPTION = 200;
    const VALIDATION_EXCEPTION = 210;

    public static function getError($iErrorCode, $arReplace = [])
    {
        return '[' . $iErrorCode . '] ' . static::getErrorTextByCode($iErrorCode, $arReplace);
    }

    public static function getErrorTextByCode($iErrorCode, $arReplace = []): string
    {
        switch ((int)$iErrorCode)
        {
            case self::ERROR_MODULE_WRONG_NAME:
                $text = 'Неверное имя модуля "#MODULE_NAME#"';
                break;
            case self::ERROR_MODULE_NOT_INSTALLED:
                $text = 'Модуль "#MODULE_NAME#" не установлен';
                break;
            case self::ERROR_MODULE_INCLUDE:
                $text = 'Ошибка подключения модуля "#MODULE_NAME#"';
                break;
            case self::ERROR_MODULE_NAME_EMPTY_BRAND:
                $text = 'В имени модуля отсутствует бренд';
                break;
            case self::ERROR_MODULE_WRONG_SYMBOLS_IN_MODULE_NAME:
                $text = 'Использованы недопустимые символы в имени модуля';
                break;
            case self::ERROR_MODULE_NAME_TO_LONG:
                $text = 'Имя модуля слишком длинное. Допустимая длина #MAX_LENGTH# '
                        .'символов';
                break;
            case self::ERROR_CLASS_WRONG_NAME:
                $text = 'Неверное имя класса "#CLASS_NAME#"';
                break;
            case self::ERROR_CLASS_NOT_AUTOLOAD:
                if (!isset($arReplace['MODULE_NAME']) && isset($arReplace['CLASS_NAME']))
                {
                    $arReplace['MODULE_NAME'] = Modules::getModuleFromNamespace(
                        $arReplace['CLASS_NAME']
                    );
                }
                $text = 'Класс "#CLASS_NAME#" не существует среди автозагружаемых '
                        .'классов модуля "#MODULE_NAME#"';
                break;
            default://ERROR_ERROR
                $text = 'Неизвестная ошибка';
        }

        if (!empty($arReplace))
        {
            foreach ($arReplace as $code => $sReplace)
            {
                $text = str_replace(
                    '#' . strtoupper($code) . '#',
                    $sReplace,
                    $text
                );
            }
        }

        return $text;
    }

}
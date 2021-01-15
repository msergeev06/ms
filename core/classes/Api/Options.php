<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Api;

use Ms\Core\Entity\Options\Options as CoreOptions;
use Ms\Core\Entity\System\Multiton;

/**
 * Класс Ms\Core\Api\Options
 * API для работы с настройками
 */
class Options extends Multiton
{
    /**
     * Возвращает значение настройки, приведя ее к типу string
     *
     * @param string      $moduleName         Имя модуля, добавившего настройку
     * @param string      $optionName         Имя настройки
     * @param string|null $optionDefaultValue Значение по умолчанию настройки
     *
     * @return string|null
     */
    public function getOptionString (string $moduleName, string $optionName, string $optionDefaultValue = null)
    {
        return CoreOptions::getInstance()->getOptionStr($moduleName, $optionName, $optionDefaultValue);
    }

    /**
     * Возвращает значение настройки, приведя ее к типу integer
     *
     * @param string      $moduleName         Имя модуля, добавившего настройку
     * @param string      $optionName         Имя настройки
     * @param int|null    $optionDefaultValue Значение по умолчанию настройки
     *
     * @return int|null
     */
    public function getOptionInt (string $moduleName, string $optionName, int $optionDefaultValue = null)
    {
        return CoreOptions::getInstance()->getOptionInt($moduleName, $optionName, $optionDefaultValue);
    }

    /**
     * Возвращает значение настройки, приведя ее к типу float
     *
     * @param string      $moduleName         Имя модуля, добавившего настройку
     * @param string      $optionName         Имя настройки
     * @param float|null  $optionDefaultValue Значение по умолчанию настройки
     *
     * @return float|null
     */
    public function getOptionFloat (string $moduleName, string $optionName, float $optionDefaultValue = null)
    {
        return CoreOptions::getInstance()->getOptionFloat($moduleName, $optionName, $optionDefaultValue);
    }

    /**
     * Возвращает значение настройки, приведя ее к типу bool
     *
     * @param string      $moduleName         Имя модуля, добавившего настройку
     * @param string      $optionName         Имя настройки
     * @param bool|null   $optionDefaultValue Значение по умолчанию настройки
     *
     * @return bool|null
     */
    public function getOptionBool (string $moduleName, string $optionName, bool $optionDefaultValue = null)
    {
        return CoreOptions::getInstance()->getOptionBool($moduleName, $optionName, $optionDefaultValue);
    }

    /**
     * Устанавливает значение настройки
     *
     * @param string      $moduleName  Имя модуля, добавившего настройку
     * @param string      $optionName  Имя настройки
     * @param mixed|null  $optionValue Значение по умолчанию настройки
     *
     * @return bool
     */
    public function setOption (string $moduleName, string $optionName, $optionValue = null)
    {
        return CoreOptions::getInstance()->setOption($moduleName, $optionName, $optionValue);
    }

    /**
     * Возвращает полное имя настройки в формате БРЕНД_ИМЯМОДУЛЯ_ИМЯ_НАСТРОЙКИ
     *
     * @param string $moduleName Имя модуля, добавившего настройку
     * @param string $optionName Имя настройки
     *
     * @return string
     */
    public function getOptionFullName (string $moduleName, string $optionName)
    {
        return CoreOptions::getInstance()->getOptionFullName($moduleName, $optionName);
    }
}

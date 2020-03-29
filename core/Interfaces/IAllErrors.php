<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Interfaces;

/**
 * Интерфейс Ms\Core\Interfaces\IAllErrors
 * Интерфейс для классов информаторов об ошибках
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_interfaces_all_errors/
 */
interface IAllErrors
{
    public static function getError($iErrorCode, $arReplace = []);

    /**
     * Возвращает текст ошибки по коду
     *
     * @param mixed $iErrorCode Код ошибки
     * @param array $arReplace  Массив подстановок в тексте ошибки
     *
     * @return string
     */
    public static function getErrorTextByCode($iErrorCode, $arReplace = []): string;
}
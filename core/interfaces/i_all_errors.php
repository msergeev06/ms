<?php
/**
 * Ms\Core\Interfaces\AllErrors
 * Интерфейс для классов информаторов об ошибках
 *
 * @package Ms\Core
 * @subpackage Interfaces
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Interfaces;

/**
 * Interface AllErrors
 * @package Ms\Core
 * @subpackage Interfaces
 * @link https://api.dobrozhil.ru/classes/ms_core_interfaces_all_errors/
 */
interface IAllErrors
{
	public static function getError ($iErrorCode, $arReplace=array());

	public static function getErrorTextByCode ($iErrorCode,$arReplace=array());
}
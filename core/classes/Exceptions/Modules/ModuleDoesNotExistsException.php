<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Modules;

use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Exceptions\Modules\ModuleDoesNotExistsException
 * Исключение для случаев, когда модуль не существует
 */
class ModuleDoesNotExistsException extends SystemException
{
	public function __construct (
	    $moduleName = "",
        $code = 0,
        $file = "",
        $line = 0,
        \Exception $previous = null
    ) {
		$message = 'Module "'.$moduleName.'" does not exists!';

		parent::__construct ($message, $code, $file, $line, $previous);
	}
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Modules;

use Ms\Core\Exceptions\SystemException;
use Ms\Core\Lib\Errors;

/**
 * Класс Ms\Core\Exceptions\Modules\WrongModuleNameException
 * Исключение, при неверном написании имени модуля
 */
class WrongModuleNameException extends SystemException
{
    public function __construct ($moduleName, $file = "", $line = 0, \Exception $previous = null)
    {
        parent::__construct(
            "Неверное имя модуля \"".$moduleName."\"",
            Errors::ERROR_MODULE_WRONG_NAME,
            $file,
            $line,
            $previous
        );
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions;

use Ms\Core\Lib\Errors;

/**
 * Класс Ms\Core\Exceptions\ComponentException
 * Исключения подключения компонентов
 */
class ComponentException extends SystemException
{
    public function __construct ($componentName, $message, $file = "", $line = 0, \Exception $previous = null)
    {
        parent::__construct(
            'Component "'.$componentName. '": ' . $message,
            Errors::COMPONENT_EXCEPTION,
            $file,
            $line,
            $previous
        );
    }
}
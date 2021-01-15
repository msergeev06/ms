<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Classes;

use Ms\Core\Exceptions\SystemException;
use Ms\Core\Lib\Errors;

/**
 * Класс Ms\Core\Exceptions\Classes\ObjectNotInstanceOfAClassException
 * Исключение, когда в переменной не объект требуемого класса
 */
class ObjectNotInstanceOfAClassException extends SystemException
{
    public function __construct ($className = "", $file = "", $line = 0, \Exception $previous = null)
    {
        parent::__construct(
            'Object not instance of a class "'.$className.'"',
            Errors::ERROR_OBJECT_NOT_INSTANCE_OF_CLASS,
            $file,
            $line,
            $previous
        );
    }
}
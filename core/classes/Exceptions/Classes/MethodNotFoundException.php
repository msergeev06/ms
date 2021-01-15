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
 * Класс Ms\Core\Exceptions\Classes\MethodNotFoundException
 * Исключение, если не найден метод класса
 */
class MethodNotFoundException extends SystemException
{
    public function __construct ($className, $methodName, $file = "", $line = 0, \Exception $previous = null)
    {
        parent::__construct(
            'Метод "'.$methodName.'" не найден в классе '.$className,
            Errors::ERROR_METHOD_NOT_FOUND,
            $file,
            $line,
            $previous
        );
    }
}
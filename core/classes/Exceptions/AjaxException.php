<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions;

/**
 * Класс Ms\Core\Exceptions\AjaxException
 * Исключения при обработке ajax-запросов
 */
class AjaxException extends SystemException
{
    public function __construct($message = "", $file = "", $line = 0, \Exception $previous = null)
    {
        parent::__construct($message, 0, $file, $line, $previous);
    }
}
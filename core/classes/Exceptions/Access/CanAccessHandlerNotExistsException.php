<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Access;

use Ms\Core\Exceptions\SystemException;
use Ms\Core\Lib\Errors;

/**
 * Класс Ms\Core\Exceptions\Access\CanAccessHandlerNotExistsException
 * Исключения, возникающие когда не обнаружен обработчик прав доступа
 */
class CanAccessHandlerNotExistsException extends SystemException
{
    public function __construct (string $accessName, $file = "", $line = 0, \Exception $previous = null)
    {
        parent::__construct(
            'Обработчик права доступа "'.$accessName.'" не существует',
            Errors::ACCESS_EXCEPTION,
            $file,
            $line,
            $previous
        );
    }
}
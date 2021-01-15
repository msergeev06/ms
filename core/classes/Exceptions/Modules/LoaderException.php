<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Modules;

use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Exceptions\Modules\LoaderException
 * Исключения при попытке подключения модуля
 */
class LoaderException extends SystemException
{
    public function __construct($message = "", \Exception $previous = null)
    {
        parent::__construct($message, 0, '', 0, $previous);
    }
}
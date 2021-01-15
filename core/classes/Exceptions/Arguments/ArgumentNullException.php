<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Arguments;

/**
 * Класс Ms\Core\Exceptions\Arguments\ArgumentNullException
 * Класс исключений, связанных с передачей в функции и методы аргументов с неправильном типом и/или в неправильной форме
 */
class ArgumentNullException extends ArgumentException
{
    /**
     * Конструктор. Создает объект исключения
     *
     * @param string          $parameter Имя параметра, вызвавшего исключение
     * @param \Exception|null $previous  Предыдущее исключение
     *                                   Необязательный, по-умолчанию null
     */
    public function __construct ($parameter, \Exception $previous = null)
    {
        $message = sprintf("Argument '%s' is null or empty", $parameter);

        parent::__construct($message, $parameter, $previous);
    }
}

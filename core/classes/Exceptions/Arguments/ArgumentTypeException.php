<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Arguments;

/**
 * Класс Ms\Core\Exceptions\Arguments\ArgumentTypeException
 * Класс для исключений, возникающих при передаче функции/методу аргумента неверного типа.
 */
class ArgumentTypeException extends ArgumentException
{
    /**
     * @var string Ожидаемый тип переменной
     */
    protected $requiredType;

    /**
     * Конструктор. Создает объект исключения
     *
     * @param string     $parameter     Имя аргумента, вызвавшего исключение
     * @param string     $requiredType  Требуемый тип аргумента
     *                                  Необязательный, по-умолчанию null
     * @param \Exception $previous      Предыдущее исключение
     *                                  Необязательный, по-умолчанию null
     */
    public function __construct ($parameter, $requiredType = "", \Exception $previous = null)
    {
        if (!empty($requiredType))
        {
            $message = sprintf("The value of an argument '%s' must be of type %s", $parameter, $requiredType);
        }
        else
        {
            $message = sprintf("The value of an argument '%s' has an invalid type", $parameter);
        }

        $this->requiredType = $requiredType;

        parent::__construct($message, $parameter, $previous);
    }

    /**
     * Возвращает требуемый тип аргумента
     *
     * @return string
     */
    public function getRequiredType ()
    {
        return $this->requiredType;
    }
}

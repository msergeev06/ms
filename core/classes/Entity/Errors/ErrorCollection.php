<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Errors;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Errors\ErrorCollection
 * Коллекция объектов ошибок
 */
class ErrorCollection extends Dictionary
{
    /**
     * Конструктор класса ErrorCollection
     */
    public function __construct ()
    {
        parent::__construct();

        return $this;
    }

    /**
     * Добавляет ошибку в коллекцию
     *
     * @param Error $obError Объект ошибки
     *
     * @return $this
     * @unittest
     */
    public function addError (Error $obError)
    {
        $this->offsetSet($obError->getCode(), $obError);

        return $this;
    }

    /**
     * Добавляет ошибку в коллекцию, самостоятельно создавая объект ошибки
     *
     * @param string     $errorMessage Сообщение ошибки
     * @param string|int $errorCode    Код ощибки
     *
     * @return $this
     */
    public function addErrorEasy (string $errorMessage, $errorCode)
    {
        $this->offsetSet($errorCode, new Error($errorMessage, $errorCode));

        return $this;
    }

    /**
     * Возвращает ошибку по ее коду
     *
     * @param string $code Код ошибки
     *
     * @return Error|null
     * @unittest
     */
    public function getError (string $code)
    {
        return $this->offsetGet($code);
    }

    /**
     * Проверяет наличие ошибки по ее коду
     *
     * @param string $code Код ошибки
     *
     * @return bool
     * @unittest
     */
    public function issetError (string $code)
    {
        return $this->offsetExists($code);
    }

    /**
     * Удаляет определенную ошибку по ее коду
     *
     * @param string $code Код ошибки
     *
     * @return $this
     * @unittest
     */
    public function unsetError (string $code)
    {
        return $this->offsetUnset($code);
    }
}
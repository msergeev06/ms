<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2017 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Db;

use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Exceptions\Db\DbException
 * Класс для исключений баз данных
 */
class DbException extends SystemException
{
    /**
     * @var string Сообщение базы данных об ошибке
     */
    protected $databaseMessage;

    /**
     * Конструктор. Создает объект исключения
     *
     * @param string          $message              Сообщение исключения
     *                                              Необязательный, по-умолчанию пустая строка
     * @param string          $databaseMessage      Сообщение базы данных
     *                                              Необязательный, по-умолчанию пустая строка
     * @param \Exception|null $previous             Предыдущее исключение
     *                                              Необязательный, по-умолчанию null
     */
    public function __construct ($message = "", $databaseMessage = "", \Exception $previous = null)
    {
        if (($message != "") && ($databaseMessage != ""))
        {
            $message .= ": " . $databaseMessage;
        }
        elseif (($message == "") && ($databaseMessage != ""))
        {
            $message = $databaseMessage;
        }

        $this->databaseMessage = $databaseMessage;

        parent::__construct($message, 400, '', 0, $previous);
    }

    /**
     * Возвращает сообщение базы данных об ошибке
     *
     * @return string
     */
    public function getDatabaseMessage ()
    {
        return $this->databaseMessage;
    }
}

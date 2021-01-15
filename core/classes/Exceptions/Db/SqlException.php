<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2017 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Db;

/**
 * Класс Ms\Core\Exceptions\Db\SqlException
 * Класс для исключений, возникающих, когда база данных возвращает ошибку SQL
 */
class SqlException extends DbException
{
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
        parent::__construct($message, $databaseMessage, $previous);
    }
}

<?php
/**
 * Ms\Core\Exception\Db\ConnectionException
 * Класс для исключений подключения к базе данных
 *
 * @package Ms\Core
 * @subpackage Exception\Db
 * @author Mikhail Sergeev
 * @copyright 2017 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Db;

/**
 * Class ConnectionException
 * @package Ms\Core
 * @subpackage Exception\Db
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_db_connection_exception/
 */
class ConnectionException extends DbException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string        $message            Сообщение исключения
	 *                                          Необязательный, по-умолчанию пустая строка
	 * @param string        $databaseMessage    Сообщение базы данных
	 *                                          Необязательный, по-умолчанию пустая строка
	 * @param \Exception    $previous           Предыдущее исключение
	 *                                          Необязательный, по-умолчанию null
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_db_connection_exception_construct/
	 */
	public function __construct($message = "", $databaseMessage = "", \Exception $previous = null)
	{
		parent::__construct($message, $databaseMessage, $previous);
	}
}

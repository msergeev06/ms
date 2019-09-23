<?php
/**
 * Ms\Core\Exception\Db\DbException
 * Класс для исключений баз данных
 *
 * @package Ms\Core
 * @subpackage Exception\Db
 * @author Mikhail Sergeev
 * @copyright 2017 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Db;

use Ms\Core\Exception\SystemException;

/**
 * Class DbException
 * @package Ms\Core
 * @subpackage Exception\Db
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_db_db_exception/
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
	 * @param string            $message            Сообщение исключения
	 *                                              Необязательный, по-умолчанию пустая строка
	 * @param string            $databaseMessage    Сообщение базы данных
	 *                                              Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null    $previous          Предыдущее исключение
	 *                                              Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_db_db_exception_construct/
	 */
	public function __construct($message = "", $databaseMessage = "", \Exception $previous = null)
	{
		if (($message != "") && ($databaseMessage != ""))
			$message .= ": ".$databaseMessage;
		elseif (($message == "") && ($databaseMessage != ""))
			$message = $databaseMessage;

		$this->databaseMessage = $databaseMessage;

		parent::__construct($message, 400, '', 0, $previous);
	}

	/**
	 * Возвращает сообщение базы данных об ошибке
	 *
	 * @return string
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_db_db_exception_get_database_message/
	 */
	public function getDatabaseMessage()
	{
		return $this->databaseMessage;
	}
}

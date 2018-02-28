<?php
/**
 * Ms\Core\Exception\Db\SqlException
 *
 * @package Ms\Core
 * @subpackage Exception\Db
 * @author Mikhail Sergeev
 * @copyright 2017 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Db;

/**
 * Exception is thrown when database returns a error.
 */
class SqlException extends DbException
{
	/**
	 * @param string $message Application message.
	 * @param string $databaseMessage Database reason.
	 * @param \Exception $previous The previous exception used for the exception chaining.
	 */
	public function __construct($message = "", $databaseMessage = "", \Exception $previous = null)
	{
		parent::__construct($message, $databaseMessage, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

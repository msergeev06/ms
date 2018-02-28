<?php
/**
 * Ms\Core\Exception\Db\DbException
 *
 * @package Ms\Core
 * @subpackage Exception\Db
 * @author Mikhail Sergeev
 * @copyright 2017 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Db;

use Ms\Core\Exception\SystemException;

/**
 * Class DbException is used for all exceptions thrown in database.
 */
class DbException extends SystemException
{
	/** @var string */
	protected $databaseMessage;

	/**
	 * @param string $message Application message.
	 * @param string $databaseMessage Database reason.
	 * @param \Exception $previous The previous exception used for the exception chaining.
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
	 * Returns database specific message provided to the constructor.
	 *
	 * @return string
	 */
	public function getDatabaseMessage()
	{
		return $this->databaseMessage;
	}

	public function getClassName ()
	{
		return __CLASS__;
	}

}

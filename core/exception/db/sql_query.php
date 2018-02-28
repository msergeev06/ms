<?php
/**
 * Ms\Core\Exception\Db\SqlQueryException
 *
 * @package Ms\Core
 * @subpackage Exception\Db
 * @author Mikhail Sergeev
 * @copyright 2017 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Db;

/**
 * Exception is thrown when database returns a error on query execution.
 */
class SqlQueryException extends SqlException
{
	/** @var string */
	protected $query = "";

	/**
	 * @param string $message Application message.
	 * @param string $databaseMessage Database reason.
	 * @param string $query Sql query text.
	 * @param \Exception $previous The previous exception used for the exception chaining.
	 */
	public function __construct($message = "", $databaseMessage = "", $query = "", \Exception $previous = null)
	{
		parent::__construct($message, $databaseMessage, $previous);
		$this->query = $query;
	}

	/**
	 * Returns text of the sql query.
	 *
	 * @return string
	 */
	public function getQuery()
	{
		return $this->query;
	}

	public function showException()
	{
		$html = '<pre><b><i>'.$this->getClassName().':</i></b> "'.$this->getMessage().'"'."\n";
		//$html .= '<b>DataBase message:</b> "'.$this->getDatabaseMessage().'"'."\n";
		$html .= "<b>Query:</b>\n".'"'.$this->getQuery().'"'."\n";
		$html .= "<b>Stack trace:</b>\n".$this->getTraceAsString()."\n";
		$html .= "<b>".$this->getFile()." ".$this->getLine()."</b>";
		$html .= "</pre>";

		return $html;
	}

	public function getClassName ()
	{
		return __CLASS__;
	}

}

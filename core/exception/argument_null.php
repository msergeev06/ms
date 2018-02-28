<?php

namespace Ms\Core\Exception;

/**
 * Exception is thrown when "empty" value is passed to a function that does not accept it as a valid argument.
 */
class ArgumentNullException extends ArgumentException
{
	public function __construct($parameter, \Exception $previous = null)
	{
		$message = sprintf("Argument '%s' is null or empty", $parameter);
		parent::__construct($message, $parameter, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

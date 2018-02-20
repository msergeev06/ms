<?php

namespace MSergeev\Core\Exception;

/**
 * Exception is thrown when a method call is invalid for current state of object.
 */
class InvalidOperationException extends SystemException
{
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct($message, 160, '', 0, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

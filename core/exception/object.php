<?php

namespace Ms\Core\Exception;

/**
 * Exception is thrown when the object can't be constructed.
 */
class ObjectException extends SystemException
{
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct($message, 500, '', 0, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

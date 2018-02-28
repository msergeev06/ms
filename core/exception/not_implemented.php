<?php

namespace Ms\Core\Exception;

/**
 * Exception is thrown when operation is not implemented but should be.
 */
class NotImplementedException extends SystemException
{
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct($message, 140, '', 0, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

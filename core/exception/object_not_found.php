<?php

namespace MSergeev\Core\Exception;

/**
 * Exception is thrown when an object is not present.
 */
class ObjectNotFoundException extends SystemException
{
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct($message, 510, '', 0, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

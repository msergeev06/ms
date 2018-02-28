<?php

namespace Ms\Core\Exception;

/**
 * Exception is thrown when object property is not valid.
 */
class ObjectPropertyException extends ArgumentException
{
	public function __construct($parameter = "", \Exception $previous = null)
	{
		parent::__construct("Object property \"".$parameter."\" not found.", $parameter, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

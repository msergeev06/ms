<?php

namespace Ms\Core\Exception;

/**
 * Exception is thrown when access is denied
 */
class AccessDeniedException extends SystemException
{
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct(($message ?: "Access denied."), 510, '', 0, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

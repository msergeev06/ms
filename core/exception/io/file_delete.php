<?php

namespace MSergeev\Core\Exception\Io;

class FileDeleteException extends IoException
{
	public function __construct($path, \Exception $previous = null)
	{
		$message = sprintf("Error occurred during deleting file '%s'.", $path);
		parent::__construct($message, $path, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

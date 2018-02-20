<?php

namespace MSergeev\Core\Exception\Io;

class FileOpenException extends IoException
{
	public function __construct($path, \Exception $previous = null)
	{
		$message = sprintf("Cannot open the file '%s'.", $path);
		parent::__construct($message, $path, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

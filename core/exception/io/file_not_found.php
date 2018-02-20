<?php

namespace MSergeev\Core\Exception\Io;

class FileNotFoundException extends IoException
{
	public function __construct($path, \Exception $previous = null)
	{
		$message = sprintf("Path '%s' is not found.", $path);
		parent::__construct($message, $path, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

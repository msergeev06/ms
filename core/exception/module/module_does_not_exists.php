<?php

namespace Ms\Core\Exception\Module;

use Ms\Core\Exception\SystemException;

class ModuleDoesNotExistsException extends SystemException
{
	public function __construct ($moduleName = "", $code = 0, $file = "", $line = 0, \Exception $previous = null)
	{
		$message = 'Module "'.$moduleName.'" does not exists!';

		parent::__construct ($message, $code, $file, $line, $previous);
	}
}
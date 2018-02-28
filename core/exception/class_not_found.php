<?php
/**
 * Ms\Core\Exception\ClassNotFoundException
 *
 *
 * @package Ms\Core
 * @subpackage Exception
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Exception;

/**
 * Исключение возникает, когда происходит обращение к классу, который не был добавлен в автозагрузку
 */
class ClassNotFoundException extends SystemException
{
	public function __construct($className = "", \Exception $previous = null)
	{
		$message = sprintf("Class '%s' not found. Check this namespace, or maybe it wasn't added to AutoLoad", $className);
		parent::__construct($message, 510, '', 0, $previous);
	}

	public function getClassName ()
	{
		return __CLASS__;
	}
}

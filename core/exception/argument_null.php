<?php
/**
 * Ms\Core\Exception\ArgumentNullException
 * Класс исключений, связанных с передачей в функции и методы аргументов с неправильном типом и/или в неправильной форме
 *
 * @package Ms\Core
 * @subpackage Exception
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception;

/**
 * Class ArgumentNullException
 * @package Ms\Core
 * @subpackage Exception
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_argument_null_exception/
 */
class ArgumentNullException extends ArgumentException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string          $parameter Имя параметра, вызвавшего исключение
	 * @param \Exception|null $previous  Предыдущее исключение
	 *                                   Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_entity_exception_argument_null_exception_construct/
	 */
	public function __construct($parameter, \Exception $previous = null)
	{
		$message = sprintf("Argument '%s' is null or empty", $parameter);
		parent::__construct($message, $parameter, $previous);
	}
}

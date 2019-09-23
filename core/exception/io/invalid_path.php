<?php
/**
 * Ms\Core\Exception\Io\FileOpenException
 * Класс для исключений, возникающих при ошибках в пути к файлам
 *
 * @package Ms\Core
 * @subpackage Exception\Io
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Io;

/**
 * Class InvalidPathException
 * @package Ms\Core
 * @subpackage Exception\Io
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_io_invalid_path_exception/
 */
class InvalidPathException extends IoException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string            $path       Путь, который вызвал исключение
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null   $previous   Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_io_invalid_path_exception_construct/
	 */
	public function __construct($path, \Exception $previous = null)
	{
		$message = sprintf("Path '%s' is invalid.", $path);
		parent::__construct($message, $path, $previous);
	}
}
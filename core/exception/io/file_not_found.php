<?php
/**
 * Ms\Core\Exception\Io\FileNotFoundException
 * Класс для исключений, возникающих когда файл не был найден
 *
 * @package Ms\Core
 * @subpackage Exception\Io
 * @author Mikhail Sergeev
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Io;

/**
 * Class FileNotFoundException
 * @package Ms\Core
 * @subpackage Exception\Io
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_io_file_not_found_exception/
 */
class FileNotFoundException extends IoException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string            $path       Путь, который вызвал исключение
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null   $previous   Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_io_file_not_found_exception_construct/
	 */
	public function __construct($path, \Exception $previous = null)
	{
		$message = sprintf("Path '%s' is not found.", $path);
		parent::__construct($message, $path, $previous);
	}
}

<?php
/**
 * Ms\Core\Exception\Io\FileOpenException
 * Класс для исключений, возникающих при ошибках открытия файлов
 *
 * @package Ms\Core
 * @subpackage Exception\Io
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Io;

/**
 * Class FileOpenException
 * @package Ms\Core
 * @subpackage Exception\Io
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_io_file_open_exception/
 */
class FileOpenException extends IoException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string            $path       Путь, который вызвал исключение
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null   $previous   Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_io_file_open_exception_construct/
	 */
	public function __construct($path, \Exception $previous = null)
	{
		$message = sprintf("Cannot open the file '%s'.", $path);
		parent::__construct($message, $path, $previous);
	}
}

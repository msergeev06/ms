<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\IO;

/**
 * Класс Ms\Core\Exceptions\IO\FileOpenException
 * Класс для исключений, возникающих при ошибках открытия файлов
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_io_file_open_exception/
 */
class FileOpenException extends IOException
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

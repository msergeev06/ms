<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\IO;

/**
 * Класс Ms\Core\Exceptions\IO\FileDeleteException
 * Класс для исключений, возникающих при попытке удаления файла
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_io_file_delete_exception/
 */
class FileDeleteException extends IOException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string            $path       Путь, который вызвал исключение
	 * @param \Exception|null   $previous   Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_io_file_delete_exception_construct/
	 */
	public function __construct($path, \Exception $previous = null)
	{
		$message = sprintf("Error occurred during deleting file '%s'.", $path);
		parent::__construct($message, $path, $previous);
	}
}

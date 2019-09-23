<?php
/**
 * Ms\Core\Exception\Io\FileNotOpenedException
 * Класс для исключений, возникающих когда файл не был открыт, но должен был
 *
 * @package Ms\Core
 * @subpackage Exception\Io
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Io;

/**
 * Class FileNotOpenedException
 * @package Ms\Core
 * @subpackage Exception\Io
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_io_file_not_opened_exception/
 */
class FileNotOpenedException extends IoException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string            $path       Путь, который вызвал исключение
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null   $previous   Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_io_file_not_opened_exception_construct/
	 */
	public function __construct($path, \Exception $previous = null)
	{
		$message = sprintf("The file '%s' is not opened.", $path);
		parent::__construct($message, $path, $previous);
	}
}

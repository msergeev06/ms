<?php
/**
 * Ms\Core\Exception\Io\FileDeleteException
 * Класс для исключений, возникающих при попытке удаления файла
 *
 * @package Ms\Core
 * @subpackage Exception\Io
 * @author Mikhail Sergeev
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Io;

/**
 * Class FileDeleteException
 * @package Ms\Core
 * @subpackage Exception\Io
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_io_file_delete_exception/
 */
class FileDeleteException extends IoException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string            $path       Путь, который вызвал исключение
	 *                                      Необязательный, по-умолчанию пустая строка
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

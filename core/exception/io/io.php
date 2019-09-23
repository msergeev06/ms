<?php
/**
 * Ms\Core\Exception\Io\IoException
 * Класс исключений, возникающих при ошибках ввода/вывода
 *
 * @package Ms\Core
 * @subpackage Exception\Io
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception\Io;

use Ms\Core\Exception\SystemException;

/**
 * Class IoException
 * @package Ms\Core
 * @subpackage Exception\Io
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_io_io_exception/
 */
class IoException extends SystemException
{
	/**
	 * @var string Путь, который вызвал исключение
	 */
	protected $path;

	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string        $message    Сообщение исключения
	 *                                  Необязательный, по-умолчанию пустая строка
	 * @param string        $path       Путь, который вызвал исключение
	 *                                  Необязательный, по-умолчанию пустая строка
	 * @param \Exception    $previous   Предыдущее исключение
	 *                                  Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_io_io_exception_construct/
	 */
	public function __construct($message = "", $path = "", \Exception $previous = null)
	{
		parent::__construct($message, 120, '', 0, $previous);
		$this->path = $path;
	}

	/**
	 * Возвращает путь, который вызвал исключение
	 *
	 * @return string
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_io_get_path/
	 */
	public function getPath()
	{
		return $this->path;
	}
}

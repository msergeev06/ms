<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\IO;

use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Exceptions\IO\IOException
 * Класс исключений, возникающих при ошибках ввода/вывода
 */
class IOException extends SystemException
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
	 */
	public function getPath()
	{
		return $this->path;
	}
}

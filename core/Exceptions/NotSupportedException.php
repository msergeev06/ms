<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions;

/**
 * Класс Ms\Core\Exceptions\NotSupportedException
 * Класс для исключений, возникающих, когда операция не поддерживается
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_not_supported_exception/
 */
class NotSupportedException extends SystemException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string            $message    Сообщение исключения
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null   $previous   Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_not_supported_exception_construct/
	 */
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct($message, 150, '', 0, $previous);
	}
}

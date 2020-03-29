<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Classes;

use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Exceptions\Classes\NotImplementedException
 * Класс для исключений, возникающих если определенный метод не реализован
 * наследующим классом, хотя должен
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_not_implemented_exception/
 */
class NotImplementedException extends SystemException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string            $message    Сообщение исключения
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null   $previous   Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_not_implemented_exception_construct/
	 */
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct($message, 140, '', 0, $previous);
	}
}

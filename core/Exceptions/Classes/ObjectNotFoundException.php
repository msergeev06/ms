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
 * Класс Ms\Core\Exceptions\Classes\ObjectNotFoundException
 * Класс для исключений, возникающих, когда объекта не существует
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_object_not_found_exception/
 */
class ObjectNotFoundException extends SystemException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string            $message    Сообщение исключения
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null   $previous   Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_object_not_found_exception_construct/
	 */
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct($message, 510, '', 0, $previous);
	}
}

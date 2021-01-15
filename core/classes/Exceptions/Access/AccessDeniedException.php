<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Access;

use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Exceptions\Access\AccessDeniedException
 * Класс для исключений, возникающих когда доступ запрещен
 */
class AccessDeniedException extends SystemException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string $message           Сообщение исключения. Если передается пустая строка - сообщение исключения будет: "Access denied."
	 *                                  Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null $previous Предыдущее исключение
	 *                                  Необязательный, по-умолчанию null
	 */
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct(($message ?: "Access denied."), 510, '', 0, $previous);
	}
}

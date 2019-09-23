<?php
/**
 * Ms\Core\Exception\AccessDeniedException
 * Класс для исключений, возникающих когда доступ запрещен
 *
 * @package Ms\Core
 * @subpackage Exception
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception;

/**
 * Class AccessDeniedException
 * @package Ms\Core
 * @subpackage Exception
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_access_denied_exception/
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
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_access_denied_exception_construct/
	 */
	public function __construct($message = "", \Exception $previous = null)
	{
		parent::__construct(($message ?: "Access denied."), 510, '', 0, $previous);
	}
}

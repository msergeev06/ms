<?php
/**
 * Ms\Core\Exception\ArgumentException
 * Класс исключений, связанных с передачей в функции и методы аргументов с неправильном типом и/или в неправильной форме
 *
 * @package Ms\Core
 * @subpackage Exception
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception;

/**
 * Class ArgumentException
 * @package Ms\Core
 * @subpackage Exception
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_argument_exception/
 */
class ArgumentException extends SystemException
{
	/**
	 * @var string Имя неверно переданного параметра функции/метода
	 */
	protected $parameter;

	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string $message               Сообщение исключения
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param string $parameter             Имя параметра, из-за которого произошло исключение
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null $previous     Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_argument_exception_construct/
	 */
	public function __construct($message = "", $parameter = "", \Exception $previous = null)
	{
		parent::__construct($message, 100, '', 0, $previous);
		$this->parameter = $parameter;
	}

	/**
	 * Возвращает имя параметра
	 *
	 * @return string
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_argument_exception_get_parameter/
	 */
	public function getParameter()
	{
		return $this->parameter;
	}
}

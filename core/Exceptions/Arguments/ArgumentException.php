<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Arguments;

use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Exceptions\Arguments\ArgumentException
 * Класс исключений, связанных с передачей в функции и методы аргументов с неправильном типом и/или в неправильной форме
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
	 * @param string          $message      Сообщение исключения
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param string          $parameter    Имя параметра, из-за которого произошло исключение
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param int             $code         Числовой код ошибки
	 * @param string          $file         Путь к файлу, в котором произошла ошибка
	 * @param int             $line         Строка в которой произошла ошибка
	 * @param \Exception|null $previous     Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_argument_exception_construct/
	 * //TODO: Обновить документацию
	 */
	public function __construct($message = "", $parameter = "", $code=100, $file='', $line=0, \Exception $previous = null)
	{
		parent::__construct($message, $code, $file, $line, $previous);
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

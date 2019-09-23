<?php
/**
 * Ms\Core\Exception\ArgumentTypeException
 * Класс для исключений, возникающих при передаче функции/методу аргумента неверного типа.
 *
 * @package Ms\Core
 * @subpackage Exception
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception;

/**
 * Class ArgumentTypeException
 * @package Ms\Core
 * @subpackage Exception
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_argument_type_exception/
 */
class ArgumentTypeException	extends ArgumentException
{
	/**
	 * @var string Ожидаемый тип переменной
	 */
	protected $requiredType;

	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string     $parameter     Имя аргумента, вызвавшего исключение
	 * @param string     $requiredType  Требуемый тип аргумента
	 *                                  Необязательный, по-умолчанию null
	 * @param \Exception $previous      Предыдущее исключение
	 *                                  Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_argument_type_exception_construct/
	 */
	public function __construct($parameter, $requiredType = "", \Exception $previous = null)
	{
		if (!empty($requiredType))
			$message = sprintf("The value of an argument '%s' must be of type %s", $parameter, $requiredType);
		else
			$message = sprintf("The value of an argument '%s' has an invalid type", $parameter);

		$this->requiredType = $requiredType;

		parent::__construct($message, $parameter, $previous);
	}

	/**
	 * Возвращает требуемый тип аргумента
	 *
	 * @return string
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_argument_type_exception_get_required_type/
	 */
	public function getRequiredType()
	{
		return $this->requiredType;
	}
}

<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Arguments;

/**
 * Класс Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException
 * Класс исключений, связанных с передачей в функции и методы аргументов с неправильном типом и/или в неправильной форме
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_argument_out_of_range_exception/
 */
class ArgumentOutOfRangeException extends ArgumentException
{
	/**
	 * @var mixed Нижний предел значения аргумента
	 */
	protected $lowerLimit;
	/**
	 * @var mixed Верхний предел значения агрумента
	 */
	protected $upperLimit;

	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string     $parameter     Имя параметра, вызвавшего исключение
	 * @param null       $lowerLimit    Нижняя граница значения
	 *                                  Необязателен, по-умолчанию null
	 * @param null       $upperLimit    Верхняя граница значения
	 *                                  Необязательный, по-умолчанию null
	 * @param \Exception $previous      Предыдущее исключение
	 *                                  Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_argument_out_of_range_exception_construct/
	 */
	public function __construct($parameter, $lowerLimit = null, $upperLimit = null, \Exception $previous = null)
	{
		if (is_array($lowerLimit))
			$message = sprintf("The value of an argument '%s' is outside the allowable range of values: %s", $parameter, implode(", ", $lowerLimit));
		elseif (($lowerLimit !== null) && ($upperLimit !== null))
			$message = sprintf("The value of an argument '%s' is outside the allowable range of values: from %s to %s", $parameter, $lowerLimit, $upperLimit);
		elseif (($lowerLimit === null) && ($upperLimit !== null))
			$message = sprintf("The value of an argument '%s' is outside the allowable range of values: not greater than %s", $parameter, $upperLimit);
		elseif (($lowerLimit !== null) && ($upperLimit === null))
			$message = sprintf("The value of an argument '%s' is outside the allowable range of values: not less than %s", $parameter, $lowerLimit);
		else
			$message = sprintf("The value of an argument '%s' is outside the allowable range of values", $parameter);

		$this->lowerLimit = $lowerLimit;
		$this->upperLimit = $upperLimit;

		parent::__construct($message, $parameter, $previous);
	}

	/**
	 * Возвращает нижнюю границу значения аргумента
	 *
	 * @return null|mixed
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_argument_out_of_range_exception_get_lower_limit/
	 */
	public function getLowerLimitType()
	{
		return $this->lowerLimit;
	}

	/**
	 * Возвращает верхнюю границу значения аргумента
	 *
	 * @return mixed|null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_argument_out_of_range_exception_get_upper_limit/
	 */
	public function getUpperType()
	{
		return $this->upperLimit;
	}
}

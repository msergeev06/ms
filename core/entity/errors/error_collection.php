<?php
/**
 * Ms\Core\Entity\Errors\ErrorCollection
 * Объект ошибки
 *
 * @package Ms\Core
 * @subpackage Entity\Errors
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Errors;

use Ms\Core\Entity\Type\Dictionary;

class ErrorCollection extends Dictionary
{
	/**
	 * ErrorCollection constructor.
	 *
	 * @return ErrorCollection
	 */
	public function __construct ()
	{
		parent::__construct();

		return $this;
	}

	/**
	 * Добавляет ошибку в коллекцию
	 *
	 * @param Error $obError Объект ошибки
	 *
	 * @return ErrorCollection
	 */
	public function addError (Error $obError)
	{
		$this->add($obError->getCode(),$obError);

		return $this;
	}

	/**
	 * Возвращает ошибку по ее коду
	 *
	 * @param string $code Код ошибки
	 *
	 * @return mixed|null
	 */
	public function getError (string $code)
	{
		return $this->offsetGet($code);
	}

	/**
	 * Проверяет наличие ошибки по ее коду
	 *
	 * @param string $code Код ошибки
	 *
	 * @return bool
	 */
	public function issetError (string $code)
	{
		return $this->offsetExists($code);
	}

	/**
	 * Удаляет определенную ошибку по ее коду
	 *
	 * @param string $code Код ошибки
	 *
	 * @return Dictionary
	 */
	public function unsetError (string $code)
	{
		return $this->offsetUnset($code);
	}
}
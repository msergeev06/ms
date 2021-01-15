<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Errors;

/**
 * Класс Ms\Core\Entity\Errors\Error
 * Объект ошибки
 */
class Error
{
	/**
	 * Код ошибки
	 * @var string
	 */
	protected $code = 0;

	/**
	 * Сообщение ошибки
	 * @var string
	 */
	protected $message = '';

	/**
	 * Создает новый объект ошибки
	 *
	 * @param string $message Сообщение об ошибке
	 * @param int|string $code Код ошибки
	 *
	 * @return Error
	 */
	public function __construct(string $message = null, string $code = null)
	{
		if (!is_null($message))
		{
			$this->setMessage($message);
		}
		if (!is_null($code))
		{
			$this->setCode($code);
		}

		return $this;
	}

	/**
	 * Возвращает код ошибки
	 *
	 * @return int|string
     * @unittest
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Устанавливаем код ошибки
	 *
	 * @param int|string $code Код ошибки
	 *
	 * @return Error
     * @unittest
	 */
	public function setCode (string $code)
	{
		$this->code = (string)$code;

		return $this;
	}

	/**
	 * Возвращает сообщение об ошибке
	 *
	 * @return string
     * @unittest
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Устанавливает сообщение об ошибке
	 *
	 * @param string $message Сообщение об ошибке
	 *
	 * @return Error
     * @unittest
	 */
	public function setMessage (string $message)
	{
		$this->message = (string)$message;

		return $this;
	}

	/**
	 * Магический метод возвращающий код и сообщение об ошибке в виде строки
	 *
	 * @return string
     * @unittest
	 */
	public function __toString()
	{
		return 'Ошибка ['.$this->getCode().']: '.$this->getMessage();
	}
}
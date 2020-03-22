<?php
/**
 * Ms\Core\Entity\Errors\Error
 * Объект ошибки
 *
 * @package Ms\Core
 * @subpackage Entity\Errors
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Errors;

class Error
{
	/**
	 * Код ошибки
	 * @var string
	 * @access protected
	 */
	protected $code = 0;

	/**
	 * Сообщение ошибки
	 * @var string
	 * @access protected
	 */
	protected $message = '';

	/**
	 * Создает новый объект ошибки
	 *
	 * @param string $message Сообщение об ошибке
	 * @param int|string $code Код ошибки
	 *
	 * @return Error
	 * @access public
	 */
	public function __construct(string $message = null, string $code = null)
	{
		if (!is_null($message))
		{
			$this->message = $message;
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
	 * @access public
	 * @return int|string
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
	 */
	public function setCode (string $code)
	{
		$this->code = (string)$code;

		return $this;
	}

	/**
	 * Возвращает сообщение об ошибке
	 *
	 * @access public
	 * @return string
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
	 */
	public function setMessage (string $message)
	{
		$this->message = (string)$message;

		return $this;
	}

	/**
	 * Магический метод возвращающий код и сообщение об ошибке в виде строки
	 *
	 * @access public
	 * @return string
	 */
	public function __toString()
	{
		return 'Ошибка ['.$this->getCode().']: '.$this->getMessage();
	}
}
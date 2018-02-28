<?php
/**
 * Ms\Core\Entity\Error
 * Объект ошибки
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity;

class Error
{
	/**
	 * Код ошибки
	 * @var int|string
	 * @access protected
	 */
	protected $code;

	/**
	 * Сообщение ошибки
	 * @var string
	 * @access protected
	 */
	protected $message;

	/**
	 * Создает новый объект ошибки
	 *
	 * @param string $message Сообщение об ошибке
	 * @param int|string $code Код ошибки
	 * @access public
	 */
	public function __construct($message, $code = 0)
	{
		$this->message = $message;
		$this->code = $code;
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
	 * Магический метод возвращающий код и сообщение об ошибке в виде строки
	 *
	 * @access public
	 * @return string
	 */
	public function __toString()
	{
		return 'Error ['.$this->getCode().']: '.$this->getMessage();
	}
}
<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

/**
 * Класс Ms\Core\Entity\System\Context
 * Объект текущего контекста
 */
class Context extends Multiton
{
	/**
	 * Объект приложения
	 * @var Application
	 */
	protected $application;

	/**
	 * Объект сервера
	 * @var Server
	 */
	protected $server;

	/**
	 * Конструктор текущего контекста
	 */
	protected function __construct()
	{
		$this->application = Application::getInstance();
		$this->server = Server::getInstance();
	}

	/**
	 * Возвращает объект сервера
	 *
	 * @return Server
	 */
	public function getServer()
	{
		return $this->server;
	}

}
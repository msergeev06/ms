<?php
/**
 * MSergeev\Core\Entity\Context
 * Объект текущего контекста
 *
 * @package MSergeev\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace MSergeev\Core\Entity;

class Context
{
	/**
	 * Объект приложения
	 * @var Application
	 * @access protected
	 */
	protected $application;

	/**
	 * Объект запроса
	 * @var HttpRequest
	 * @access protected
	 */
	protected $request;

	/**
	 * Объект сервера
	 * @var Server
	 * @access protected
	 */
	protected $server;

	/**
	 * Объект окружения
	 * @var Environment
	 * @access protected
	 */
	protected $env;

	/**
	 * Массив параметров
	 * @var array
	 * @access protected
	 */
	protected $params;

	/**
	 * Конструктор текущего контекста
	 *
	 * @param Application $application Объект приложения
	 * @access public
	 */
	public function __construct(Application $application)
	{
		$this->application = $application;
	}

	/**
	 * Метод инициализации основных параметров
	 *
	 * @param HttpRequest $request  Объект запроса
	 * @param Server      $server   Объект сервера
	 * @param array       $params   Параметры контекста
	 * @access public
	 */
	public function initialize (HttpRequest $request, Server $server, $params=array())
	{
		$this->request = $request;
		$this->server = $server;
		if (isset($params['env']))
		{
			$this->env = new Environment($params['env']);
		}
	}

	/**
	 * Возвращает объект запроса
	 *
	 * @access public
	 * @return HttpRequest
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Возвращает объект сервера
	 *
	 * @access public
	 * @return Server
	 */
	public function getServer()
	{
		return $this->server;
	}

	/**
	 * Возвращает объект окружения
	 *
	 * @access public
	 * @return Environment
	 */
	public function getEnv ()
	{
		return $this->env;
	}
}
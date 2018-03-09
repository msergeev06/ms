<?php
/**
 * Ms\Core\Entity\Request
 * ББазовый класс запроса
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity;

use Ms\Core\Lib;
use Ms\Core\Exception;

/**
 * Class Request Содержит текущий запрос
 * @package Ms\Core
 * @subpackage Entity
 * @abstract
 */
abstract class Request extends Type\ParameterDictionary
{
	/**
	 * Объект сервер
	 * @var Server
	 * @access protected
	 */
	protected $server;

	/**
	 * Запрошенная страница
	 * @var string
	 * @access protected
	 */
	protected $requestedPage = null;

	/**
	 * Запрошенный раздел
	 * @var string
	 * @access protected
	 */
	protected $requestedPageDirectory = null;

	/**
	 * Создает объект запроса
	 *
	 * @param Server $server  Объект сервер
	 * @param array  $request Массив параметров запроса
	 * @access public
	 */
	public function __construct(Server $server, array $request)
	{
		parent::__construct($request);

		$this->server = $server;
	}

	/**
	 * Возвращает имя файла скрипта, который сейчас выполняется, относительно корня
	 * Является оберткой функции Server::getPhpSelf
	 *
	 * @uses Server::getPhpSelf
	 * @access public
	 * @return null|string
	 */
	public function getPhpSelf()
	{
		return $this->server->getPhpSelf();
	}

	/**
	 * Возвращает абсолютный путь к скрипту, который в данный момент исполняется
	 * Является оберткой функции Server::getScriptName
	 *
	 * @uses Server::getScriptName
	 *
	 * @access public
	 * @return null|string
	 */
	public function getScriptName()
	{
		return $this->server->getScriptName();
	}

	/**
	 * Возвращает путь к запрошенной странице
	 *
	 * @access public
	 * @throws
	 * @return mixed|null|string
	 */
	public function getRequestedPage()
	{
		if ($this->requestedPage === null)
		{
			$page = $this->getScriptName();
			if (!empty($page))
			{
				$page = Lib\IO\Path::normalize($page);

				if (substr($page, 0, 1) !== "/" && !preg_match("#^[a-z]:[/\\\\]#i", $page))
				{
					$page = "/".$page;
				}
			}
			$this->requestedPage = $page;
		}

		return $this->requestedPage;
	}

	/**
	 * Возвращает путь к запрошенному разделу
	 *
	 * @access public
	 * @return string
	 */
	public function getRequestedPageDirectory()
	{
		if ($this->requestedPageDirectory === null)
		{
			$requestedPage = $this->getRequestedPage();
			$this->requestedPageDirectory = Lib\IO\Path::getDirectory($requestedPage);
		}
		return $this->requestedPageDirectory;
	}

	/**
	 * Возвращает true, есди текущий запрос является AJAX запросом
	 *
	 * @access public
	 * @return bool
	 */
	public function isAjaxRequest()
	{
		return
			$this->server->get("HTTP_MS_AJAX") !== null ||
			$this->server->get("HTTP_X_REQUESTED_WITH") === "XMLHttpRequest";
	}
}
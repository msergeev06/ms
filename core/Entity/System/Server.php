<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

/**
 * Класс Ms\Core\Entity\System\Server
 * Класс сервера
 */
class Server extends Type\ParameterDictionary
{
	/**
	 * Создает объект сервер
	 *
	 * @param array $arServer Массив параметров сервера _SERVER
	 * @access public
	 */
	public function __construct(array $arServer)
	{
		if (isset($arServer["DOCUMENT_ROOT"]))
			$arServer["DOCUMENT_ROOT"] = rtrim($arServer["DOCUMENT_ROOT"], "/\\");

		parent::__construct($arServer);
	}

	/**
	 * Возвращает DocumentRoot сервера
	 *
	 * @access public
	 * @return string | null
	 */
	public function getDocumentRoot()
	{
		return $this->get("DOCUMENT_ROOT");
	}

	/**
	 * Возвращает root сборки
	 *
	 * @access public
	 * @return string | null
	 */
	public function getPersonalRoot()
	{
		$app = Application::getInstance();
		$r = $app->getSettings()->getMsRoot();
		if ($r == null || $r == "")
			$r = $this->getDocumentRoot()."/ms";

		return $r;
	}

	/**
	 * Возвращает содержимое заголовка Host: из текущего запроса, если он есть
	 *
	 * @access public
	 * @return string | null
	 */
	public function getHttpHost()
	{
		return $this->get("HTTP_HOST");
	}

	/**
	 * Возвращает имя хоста, на котором выполняется текущий скрипт
	 *
	 * @access public
	 * @return string | null
	 */
	public function getServerName()
	{
		return $this->get("SERVER_NAME");
	}

	/**
	 * Возвращает IP-адрес сервера, на котором выполняется текущий скрипт
	 *
	 * @access public
	 * @return string | null
	 */
	public function getServerAddr()
	{
		return $this->get("SERVER_ADDR");
	}

	/**
	 * Возвращает порт на компьютере сервера, используемый веб-сервером для соединения
	 *
	 * @access public
	 * @return string | null
	 */
	public function getServerPort()
	{
		return $this->get("SERVER_PORT");
	}

	/**
	 * Возвращает URI, который был передан для того, чтобы получить доступ к этой странице
	 * /index.php/test1/test2?login=yes&back_url_admin=/
	 *
	 * @access public
	 * @return string | null
	 */
	public function getRequestUri()
	{
		return $this->get("REQUEST_URI");
	}

	/**
	 * Возвращает какой метод был использован для запроса страницы;
	 * к примеру 'GET', 'HEAD', 'POST', 'PUT'
	 *
	 * @access public
	 * @return string | null
	 */
	public function getRequestMethod()
	{
		return $this->get("REQUEST_METHOD");
	}

	/**
	 * Возвращает имя файла скрипта, который сейчас выполняется, относительно корня документов
	 * /index.php/test1/test2
	 *
	 * @access public
	 * @return string | null
	 */
	public function getPhpSelf()
	{
		return $this->get("PHP_SELF");
	}

	/**
	 * Возвращает путь, к текущему исполняемому скрипту
	 * /index.php
	 *
	 * @access public
	 * @return string | null
	 */
	public function getScriptName()
	{
		return $this->get("SCRIPT_NAME");
	}

	/**
	 * Переписывает параметры URI новыми данными
	 *
	 * @param string $url               URI, который был передан для того, чтобы получить доступ к этой странице
	 * @param string $queryString       Строка запросов, если есть, с помощью которой была получена страница
	 * @param string $redirectStatus    Статус редиректа
	 * @access public
	 */
	public function rewriteUri($url, $queryString, $redirectStatus = null)
	{
		$this->values["REQUEST_URI"] = $url;
		$this->values["QUERY_STRING"] = $queryString;
		if ($redirectStatus != null)
			$this->values["REDIRECT_STATUS"] = $redirectStatus;
	}

	/**
	 * @param        $url
	 * @param string $queryString
	 * @access public
	 */
	public function transferUri($url, $queryString = "")
	{
		$this->values["REAL_FILE_PATH"] = $url;
		if ($queryString != "")
		{
			if (!isset($this->values["QUERY_STRING"]))
				$this->values["QUERY_STRING"] = "";
			if (isset($this->values["QUERY_STRING"]) && ($this->values["QUERY_STRING"] != ""))
				$this->values["QUERY_STRING"] .= "&";
			$this->values["QUERY_STRING"] .= $queryString;
		}
	}

	/**
	 * @return string
	 */
	public function getRemoteAddr ()
	{
		return $this->get('REMOTE_ADDR');
	}

	/**
	 * @return string
	 */
	public function getHttpReferer ()
	{
		return $this->get('HTTP_REFERER');
	}
}
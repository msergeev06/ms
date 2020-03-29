<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

use Ms\Core\Lib;

/**
 * Класс Ms\Core\Entity\System\HttpRequest
 * Объект HTTP запроса
 */
class HttpRequest extends Request
{
	/**
	 * GET параметры
	 * @var Type\ParameterDictionary
	 * @access protected
	 */
	protected $queryString;

	/**
	 * POST параметры
	 * @var Type\ParameterDictionary
	 * @access protected
	 */
	protected $postData;

	/**
	 * Данные загруженных файлов
	 * @var Type\ParameterDictionary
	 * @access protected
	 */
	protected $files;

	/**
	 * Данные COOKIE
	 * @var Type\ParameterDictionary
	 * @access protected
	 */
	protected $cookies;

	/**
	 * Необработанные данные COOKIE
	 * @var Type\ParameterDictionary
	 * @access protected
	 */
	protected $cookiesRaw;

	/**
	 * Создает объект запроса
	 *
	 * @param Server $server        Объект сервера
	 * @param array $queryString    Массив _GET
	 * @param array $postData       Массив _POST
	 * @param array $files          Массив _FILES
	 * @param array $cookies        Массив _COOKIE
	 * @access public
	 */
	public function __construct(Server $server, array $queryString, array $postData, array $files, array $cookies)
	{
		$request = array_merge($queryString, $postData);
		parent::__construct($server, $request);

		$this->queryString = new Type\ParameterDictionary($queryString);
		$this->postData = new Type\ParameterDictionary($postData);
		$this->files = new Type\ParameterDictionary($files);
		$this->cookiesRaw = new Type\ParameterDictionary($cookies);
		$this->cookies = new Type\ParameterDictionary($this->prepareCookie($cookies));
	}

	/**
	 * Возвращает запрашиваемый GET параметер текущего запроса
	 *
	 * @param string $name Имя параметра
	 * @access public
	 * @return null|string
	 */
	public function getQuery($name)
	{
		return $this->queryString->get($name);
	}

	/**
	 * Возвращает список GET параметров текущего запроса
	 *
	 * @access public
	 * @return Type\ParameterDictionary
	 */
	public function getQueryList()
	{
		return $this->queryString;
	}

	/**
	 * Возвращает запрашиваемый POST параметр текущего запроса
	 *
	 * @param string $name Имя параметра
	 * @access public
	 * @return null|string
	 */
	public function getPost($name)
	{
		return $this->postData->get($name);
	}

	/**
	 * Возвращает список POST параметров текущего запроса
	 *
	 * @access public
	 * @return Type\ParameterDictionary
	 */
	public function getPostList()
	{
		return $this->postData;
	}

	/**
	 * Возвращает параметр FILES текущего запроса
	 *
	 * @param string $name Имя параметра
	 * @access public
	 * @return null|string
	 */
	public function getFile($name)
	{
		return $this->files->get($name);
	}

	/**
	 * Возвращает список параметров FILES текущего запроса
	 *
	 * @access public
	 * @return Type\ParameterDictionary
	 */
	public function getFileList()
	{
		return $this->files;
	}

	/**
	 * Возвращает параметр COOKIE текущего запроса
	 *
	 * @param string $name Имя параметра
	 * @access public
	 * @return null|string
	 */
	public function getCookie($name)
	{
		return $this->cookies->get($name);
	}

	/**
	 * Устанавливает параметер COOKIE
	 * Является оберткой функции \setcookie
	 *
	 * @param string   $name                Имя cookie
	 * @param string   $value [optional]    Значение cookie
	 * @param int      $expire [optional]   Время истечения cookie
	 * @param string   $path [optional]     Путь доступности cookie
	 * @param string   $domain [optional]   Домен доступности cookie
	 * @param bool     $secure [optional]   Флаг безопасного cookie
	 * @param bool     $httpOnly [optional] Флаг только http
	 *
	 * @access public
	 * @return bool
	 */
	public function setCookie ($name, $value=null,$expire=null,$path=null,
	                           $domain=null, $secure=null,$httpOnly=null)
	{
		static $cookiePrefix = "ms_";

		if ($res = setcookie($cookiePrefix.$name,$value,$expire,$path,$domain,$secure,$httpOnly))
		{
			$this->cookiesRaw->addRaw($cookiePrefix.$name,$value);
			$this->cookies->add($name,$value);
		}

		return $res;
	}

	/**
	 * Возвращает список COOKIE параметров текущего запроса
	 *
	 * @access public
	 * @return Type\ParameterDictionary
	 */
	public function getCookieList()
	{
		return $this->cookies;
	}

	/**
	 * Возвращает необработанный параметр COOKIE
	 *
	 * @param string $name Имя параметра
	 *
	 * @access public
	 * @return null|string
	 */
	public function getCookieRaw($name)
	{
		return $this->cookiesRaw->get($name);
	}

	/**
	 * Возвращает необработанный массив параметров COOKIE
	 *
	 * @access public
	 * @return Type\ParameterDictionary
	 */
	public function getCookieRawList()
	{
		return $this->cookiesRaw;
	}

	/**
	 * Возвращает IP-адрес, с которого пользователь просматривает текущую страницу
	 *
	 * @access public
	 * @return string
	 */
	public function getRemoteAddress()
	{
		return $this->server->get("REMOTE_ADDR");
	}

	/**
	 * Возвращает URI, который был передан для того, чтобы получить доступ к этой странице
	 * Является оберткой функции Server::getRequestUri
	 *
	 * @uses Server::getRequestUri
	 *
	 * @access public
	 * @return null|string
	 */
	public function getRequestUri()
	{
		return $this->server->getRequestUri();
	}

	/**
	 * Возвращает какой метод был использован для запроса страницы;
	 * к примеру 'GET', 'HEAD', 'POST', 'PUT'.
	 * Является оберткой функции Server::getRequestMethod
	 *
	 * @uses Server::getRequestMethod
	 *
	 * @access public
	 * @return null|string
	 */
	public function getRequestMethod()
	{
		return $this->server->getRequestMethod();
	}

	/**
	 * Проверяет, что текущий метод запроса является POST
	 *
	 * @access public
	 * @return bool
	 */
	public function isPost()
	{
		return ($this->getRequestMethod() == "POST");
	}

	/**
	 * Возвращает user агента HTTP заголовка запроса
	 *
	 * @access public
	 * @return null|string
	 */
	public function getUserAgent()
	{
		return $this->server->get("HTTP_USER_AGENT");
	}

	/**
	 * Возвращает содержимое заголовка Accept-Language: из текущего запроса, если он есть.
	 * Например: 'en'.
	 *
	 * @access public
	 * @return array
	 */
	public function getAcceptedLanguages()
	{
		static $acceptedLanguages = array();

		if (empty($acceptedLanguages))
		{
			$acceptedLanguagesString = $this->server->get("HTTP_ACCEPT_LANGUAGE");
			$arAcceptedLanguages = explode(",", $acceptedLanguagesString);
			foreach ($arAcceptedLanguages as $langString)
			{
				$arLang = explode(";", $langString);
				$acceptedLanguages[] = $arLang[0];
			}
		}

		return $acceptedLanguages;
	}

	/**
	 * Возвращает текущую страницу, обработав параметры запроса URI.
	 *
	 * @ignore
	 * @access public
	 * @throws
	 * @return string
	 */
	public function getRequestedPage()
	{
		if ($this->requestedPage === null)
		{
			if(($uri = $this->getRequestUri()) == '')
			{
				$this->requestedPage = parent::getRequestedPage();
			}
			else
			{
				$parsedUri = new Web\Uri("http://".$this->server->getHttpHost().$uri);
				$this->requestedPage = Lib\IO\Path::normalize(static::decode($parsedUri->getPath()));
			}
		}
		return $this->requestedPage;
	}

	/**
	 * Returns url-decoded and converted to the current encoding URI of the request (except the query string).
	 *
	 * @access public
	 * @return string
	 */
	public function getDecodedUri()
	{
		$parsedUri = new Web\Uri("http://".$this->server->getHttpHost().$this->getRequestUri());

		$uri = static::decode($parsedUri->getPath());

		if(($query = $parsedUri->getQuery()) <> '')
		{
			$uri .= "?".$query;
		}

		return $uri;
	}

	/**
	 * @param $url
	 *
	 * @return bool|string
	 */
	protected static function decode($url)
	{
		return Lib\Text\Encoding::convertEncodingToCurrent(urldecode($url));
	}

	/**
	 * Возвращает имя сервера без номера порта
	 *
	 * @access public
	 * @return string
	 */
	public function getHttpHost()
	{
		static $host = null;

		if ($host === null)
		{
			//scheme can be anything, it's used only for parsing
			$url = new Web\Uri("http://".$this->server->getHttpHost());
			$host = $url->getHost();
			$host = trim($host, "\t\r\n\0 .");
		}

		return $host;
	}

	/**
	 * Возвращает флаг того, что используется HTTPS
	 *
	 * @access public
	 * @return bool
	 */
	public function isHttps()
	{
		if($this->server->get("SERVER_PORT") == 443)
		{
			return true;
		}

		$https = $this->server->get("HTTPS");
		if($https !== null && strtolower($https) == "on")
		{
			return true;
		}

		return (Application::getInstance()->getSettings()->getSiteProtocol() === 'https');
	}

	public function modifyByQueryString($queryString)
	{
		if ($queryString != "")
		{
			parse_str($queryString, $vars);

			$this->values += $vars;
			$this->queryString->values += $vars;
		}
	}

	/**
	 * Обрабатывает параметры COOKIE, отбрасывая префикс ms_
	 *
	 * @param array $cookies
	 * @access protected
	 * @return array
	 */
	protected function prepareCookie(array $cookies)
	{
		static $cookiePrefix = null;
		if ($cookiePrefix === null)
			$cookiePrefix = "ms_";

		$cookiePrefixLength = strlen($cookiePrefix);

		$cookiesNew = array();
		foreach ($cookies as $name => $value)
		{
			if (strpos($name, $cookiePrefix) !== 0)
				continue;

			$cookiesNew[substr($name, $cookiePrefixLength)] = $value;
		}
		return $cookiesNew;
	}
}
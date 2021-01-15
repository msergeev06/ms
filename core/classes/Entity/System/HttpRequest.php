<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

use Ms\Core\Entity\Web\Uri;
use Ms\Core\Lib;

/**
 * Класс Ms\Core\Entity\System\HttpRequest
 * Объект HTTP запроса
 */
class HttpRequest extends Request
{
	/**
	 * GET параметры
	 * @var ParameterDictionary
	 */
	protected $queryString;

	/**
	 * POST параметры
	 * @var ParameterDictionary
	 */
	protected $postData;

	/**
	 * Данные загруженных файлов
	 * @var ParameterDictionary
	 */
	protected $files;

	/**
	 * Данные COOKIE
	 * @var ParameterDictionary
	 */
	protected $cookies;

	/**
	 * Необработанные данные COOKIE
	 * @var ParameterDictionary
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
	 */
	public function __construct(Server &$server, array &$queryString, array &$postData, array &$files, array &$cookies)
	{
		$request = array_merge($queryString, $postData);
		parent::__construct($server, $request);

		$this->queryString = new ParameterDictionary($queryString);
		$this->postData = new ParameterDictionary($postData);
		$this->files = new ParameterDictionary($files);
		$this->cookiesRaw = new ParameterDictionary($cookies);
		$this->cookies = new ParameterDictionary($this->prepareCookie($cookies));
	}

	/**
	 * Возвращает запрашиваемый GET параметер текущего запроса
	 *
	 * @param string $name Имя параметра
	 * @return null|string
	 */
	public function getQuery($name)
	{
		return $this->queryString->get($name);
	}

	/**
	 * Возвращает список GET параметров текущего запроса
	 *
	 * @return ParameterDictionary
	 */
	public function getQueryList()
	{
		return $this->queryString;
	}

	/**
	 * Возвращает запрашиваемый POST параметр текущего запроса
	 *
	 * @param string $name Имя параметра
	 * @return null|string
	 */
	public function getPost($name)
	{
		return $this->postData->get($name);
	}

	/**
	 * Возвращает список POST параметров текущего запроса
	 *
	 * @return ParameterDictionary
	 */
	public function getPostList()
	{
		return $this->postData;
	}

	/**
	 * Возвращает параметр FILES текущего запроса
	 *
	 * @param string $name Имя параметра
	 * @return null|string
	 */
	public function getFile($name)
	{
		return $this->files->get($name);
	}

	/**
	 * Возвращает список параметров FILES текущего запроса
	 *
	 * @return ParameterDictionary
	 */
	public function getFileList()
	{
		return $this->files;
	}

	/**
	 * Возвращает параметр COOKIE текущего запроса
	 *
	 * @param string $name Имя параметра
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
	 * @return ParameterDictionary
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
	 * @return null|string
	 */
	public function getCookieRaw($name)
	{
		return $this->cookiesRaw->get($name);
	}

	/**
	 * Возвращает необработанный массив параметров COOKIE
	 *
	 * @return ParameterDictionary
	 */
	public function getCookieRawList()
	{
		return $this->cookiesRaw;
	}

	/**
	 * Возвращает IP-адрес, с которого пользователь просматривает текущую страницу
	 *
	 * @return string
	 */
	public function getRemoteAddress()
	{
		return $this->server->getRemoteAddr();
	}

	/**
	 * Возвращает URI, который был передан для того, чтобы получить доступ к этой странице
	 * Является оберткой функции Server::getRequestUri
	 *
	 * @uses Server::getRequestUri
	 *
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
	 * @return null|string
	 */
	public function getRequestMethod()
	{
		return $this->server->getRequestMethod();
	}

	/**
	 * Проверяет, что текущий метод запроса является POST
	 *
	 * @return bool
	 */
	public function isPost()
	{
		return ($this->getRequestMethod() == "POST");
	}

	/**
	 * Возвращает user агента HTTP заголовка запроса
	 *
	 * @return null|string
	 */
	public function getUserAgent()
	{
		return $this->server->getUserAgent();
	}

	/**
	 * Возвращает содержимое заголовка Accept-Language: из текущего запроса, если он есть.
	 * Например: 'en'.
	 *
	 * @return array
	 */
	public function getAcceptedLanguages()
	{
		static $acceptedLanguages = array();

		if (empty($acceptedLanguages))
		{
			$acceptedLanguagesString = $this->server->getAcceptLanguage();
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
     * @return string
     * @throws \Ms\Core\Exceptions\IO\InvalidPathException
     * @ignore
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
				$parsedUri = new Uri("http://".$this->server->getHttpHost().$uri);
				$this->requestedPage = Lib\IO\Path::normalize(static::decode($parsedUri->getPath()));
			}
		}
		return $this->requestedPage;
	}

	/**
	 * Returns url-decoded and converted to the current encoding URI of the request (except the query string).
	 *
	 * @return string
	 */
	public function getDecodedUri()
	{
		$parsedUri = new Uri("http://".$this->server->getHttpHost().$this->getRequestUri());

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
	 * @return string
	 */
	public function getHttpHost()
	{
		static $host = null;

		if ($host === null)
		{
			//scheme can be anything, it's used only for parsing
			$url = new Uri("http://".$this->server->getHttpHost());
			$host = $url->getHost();
			$host = trim($host, "\t\r\n\0 .");
		}

		return $host;
	}

	/**
	 * Возвращает флаг того, что используется HTTPS
	 *
	 * @return bool
	 */
	public function isHttps()
	{
		if($this->server->getServerPort() == 443)
		{
			return true;
		}

		$https = $this->server->getHttps();
		if($https !== null && strtolower($https) == "on")
		{
			return true;
		}

		return (Application::getInstance()->getSettings()->getSiteProtocol() === 'https');
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
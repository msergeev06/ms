<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2017 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

/**
 * Класс Ms\Core\Entity\System\Server
 * Класс сервера
 */
class Server extends Multiton
{
    /** @var array */
    protected $arServer = null;

    /**
     * Инициализирует объект Server
     *
     * @param array $arServer Массив параметров сервера _SERVER
     *
     * @return Server
     */
    public function init (array &$arServer)
    {
        $this->arServer = &$arServer;

        return $this;
    }

    /**
     * Возвращает TRUE, если указанный ключ существует в параметрах сервера, иначе возвращает FALSE
     *
     * @param string $key Ключ
     *
     * @return bool
     */
    public function isExists(string $key)
    {
        return array_key_exists($key, $this->arServer);
    }

    /**
     * Возвращает содержимое заголовка Host: из текущего запроса, если он есть
     *
     * @return string | null
     */
    public function getHttpHost()
    {
        return $this->get("HTTP_HOST");
    }

    public function getHttps ()
    {
        return $this->get("HTTPS");
    }

    /**
     * Возвращает имя хоста, на котором выполняется текущий скрипт
     *
     * @return string | null
     */
    public function getServerName()
    {
        return $this->get("SERVER_NAME");
    }

    /**
     * Возвращает IP-адрес сервера, на котором выполняется текущий скрипт
     *
     * @return string | null
     */
    public function getServerAddr()
    {
        return $this->get("SERVER_ADDR");
    }

    /**
     * Возвращает порт на компьютере сервера, используемый веб-сервером для соединения
     *
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
     * @return string | null
     */
    public function getScriptName()
    {
        return $this->get("SCRIPT_NAME");
    }

    /**
     * Переписывает параметры URI новыми данными
     *
     * @param string $url            URI, который был передан для того, чтобы получить доступ к этой странице
     * @param string $queryString    Строка запросов, если есть, с помощью которой была получена страница
     * @param string $redirectStatus Статус редиректа
     *
     * @return void
     */
    public function rewriteUri($url, $queryString, $redirectStatus = null)
    {
        $this->arServer["REQUEST_URI"] = $url;
        $this->arServer["QUERY_STRING"] = $queryString;
        if ($redirectStatus != null)
        {
            $this->arServer["REDIRECT_STATUS"] = $redirectStatus;
        }
    }

    /**
     * @param        $url
     * @param string $queryString
     *
     * @return Server
     */
    public function transferUri($url, $queryString = "")
    {
        $this->arServer["REAL_FILE_PATH"] = $url;
        if ($queryString != "")
        {
            if (!isset($this->values["QUERY_STRING"]))
            {
                $this->arServer["QUERY_STRING"] = "";
            }
            if (isset($this->values["QUERY_STRING"]) && ($this->arServer["QUERY_STRING"] != ""))
            {
                $this->arServer["QUERY_STRING"] .= "&";
            }
            $this->arServer["QUERY_STRING"] .= $queryString;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRemoteAddr()
    {
        return $this->get('REMOTE_ADDR');
    }

    /**
     * @return string
     */
    public function getHttpReferer()
    {
        return $this->get('HTTP_REFERER');
    }

    /**
     * @return string
     */
    public function getUserAgent ()
    {
        return $this->get('HTTP_USER_AGENT');
    }

    /**
     * @return string
     */
    public function getAcceptLanguage()
    {
        return $this->get('HTTP_ACCEPT_LANGUAGE');
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key)
    {
        if (isset($this->arServer[$key]))
        {
            return $this->arServer[$key];
        }

        return null;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

/**
 * Класс Ms\Core\Entity\System\CookieController
 * Вспомогательный класс для работы с Cookie
 */
class CookieController
{
    protected $prefix = '';

    public function __construct (string $prefix = '')
    {
        $this->setPrefix($prefix);
    }

    public function clearPrefix ()
    {
        $this->prefix = '';

        return $this;
    }

    public function isset (string $name)
    {
        $name = str_replace($this->getPrefix(),'',$name);

        return isset($_COOKIE[$this->prefix . $name]);
    }

    /**
     * Возвращает значение Cookie по имени без префикса
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getCookie (string $name)
    {
        $name = str_replace($this->getPrefix(),'',$name);
        if ($this->isset($name))
        {
            return $_COOKIE[$this->prefix . $name];
        }

        return null;
    }

    /**
     * Возвращает установленный префикс для Cookie
     *
     * @return string
     */
    public function getPrefix ()
    {
        return $this->prefix;
    }

    /**
     * Устанавливает префикс для Cookie
     *
     * @param string $prefix Префикс Cookie
     *
     * @return $this
     */
    public function setPrefix (string $prefix)
    {
        if (substr($prefix,-1,1) != '_')
        {
            $prefix .= '_';
        }
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Устанавливает cookie
     *
     * @param Cookie $cookie Объект с описанием Cookie
     *
     * @return bool
     */
    public function setCookie (Cookie $cookie)
    {
        // msDebugNoAdmin($cookie);
        return setcookie(
            $this->getPrefix() . $cookie->getName(),
            $cookie->getValue(),
            $cookie->getExpires(),
            $cookie->getPath(),
            $cookie->getDomain(),
            $cookie->isSecure(),
            $cookie->isHttpOnly()
        );
    }
}
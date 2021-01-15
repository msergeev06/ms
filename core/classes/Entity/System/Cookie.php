<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Entity\System\Cookie
 * Обертка для использования технологии cookie
 */
class Cookie
{
    /** @var string */
    protected $name = null;
    /** @var string  */
    protected $value = "";
    /** @var int  */
    protected $expires = 0;
    /** @var string  */
    protected $path = "/";
    /** @var string  */
    protected $domain = "";
    /** @var bool  */
    protected $secure = false;
    /** @var bool  */
    protected $httpOnly = false;

    public function __construct (string $name, string $value = "", Date $expiredDate = null)
    {
        $this->name = $name;
        $this->value = (string)$value;
        if (!is_null($expiredDate))
        {
            $this->expires = $expiredDate->getTimestamp();
        }
        else
        {
            $date = (new Date())->modify('+1 hour');
            $this->expires = $date->getTimestamp();
        }
    }

    /**
     * Возвращает имя cookie
     *
     * @return string
     */
    public function getName (): string
    {
        return $this->name;
    }

    /**
     * Устанавливает имя cookie
     *
     * @param string $name
     *
     * @return Cookie
     */
    public function setName (string $name): Cookie
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Возвращает значение cookie
     *
     * @return string
     */
    public function getValue (): string
    {
        return $this->value;
    }

    /**
     * Устанавливает значение cookie
     *
     * @param string $value
     *
     * @return Cookie
     */
    public function setValue (string $value): Cookie
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Возвращает timestamp, когда истекает cookie
     *
     * @return int
     */
    public function getExpires (): int
    {
        return $this->expires;
    }

    /**
     * Возвращает объект Date, содержащий дату/время истечения cookie. Если возникла ошибка, вернет null
     *
     * @return Date|null
     */
    public function getExpiresDate (): Date
    {
        try
        {
            return new Date($this->expires, 'U');
        }
        catch (SystemException $e)
        {
            return null;
        }
    }

    /**
     * Устанавливает время истечения cookie, принимая timestamp
     *
     * @param int $expires
     *
     * @return Cookie
     */
    public function setExpires (int $expires): Cookie
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Устанавливает время истечения cookie, принимая объект Date
     *
     * @param Date $expires
     *
     * @return Cookie
     */
    public function setExpiresDate (Date $expires): Cookie
    {
        $this->expires = $expires->getTimestamp();

        return $this;
    }

    /**
     * Возвращает путь к директории на сервере, из которой будут доступны cookie
     *
     * @return string
     */
    public function getPath (): string
    {
        return $this->path;
    }

    /**
     * Устанавливает путь к директории на сервере, из которой будут доступны cookie
     *
     * @param string $path
     *
     * @return Cookie
     */
    public function setPath (string $path = '/'): Cookie
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Возвращает домен, которому доступны cookie
     *
     * @return string
     */
    public function getDomain (): string
    {
        return $this->domain;
    }

    /**
     * Устанавливает домен, которому доступны cookie
     *
     * @param string $domain
     *
     * @return Cookie
     */
    public function setDomain (string $domain): Cookie
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Возвращает флаг, указывающий на то, должна ли cookie передаваться от клиента по защищенному соединению HTTPS
     *
     * @return bool
     */
    public function isSecure (): bool
    {
        return $this->secure;
    }

    /**
     * Устанавливает флаг, указывающий на то, должна ли cookie передаваться от клиента по защищенному соединению HTTPS
     *
     * @param bool $secure
     *
     * @return Cookie
     */
    public function setSecure (bool $secure): Cookie
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * Возвращает флаг, указывающий на то, что cookie будут доступны только через HTTP-протокол
     *
     * @return bool
     */
    public function isHttpOnly (): bool
    {
        return $this->httpOnly;
    }

    /**
     * Устанавливает флаг, указывающий на то, что cookie будут доступны только через HTTP-протокол
     *
     * @param bool $httpOnly
     *
     * @return Cookie
     */
    public function setHttpOnly (bool $httpOnly): Cookie
    {
        $this->httpOnly = $httpOnly;

        return $this;
    }
}
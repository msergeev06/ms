<?php

namespace Ms\Core\Entity\Type;

use Ms\Core\Exception\ArgumentOutOfRangeException;

class Ip
{
	/** @var int */
	protected $longIp = null;

	/** @var string */
	protected $strIp = null;

	/**
	 * Ip constructor.
	 *
	 * @param string|null $ip IP-адрес вида 127.0.0.0
	 *
	 * @return Ip
	 * @throws ArgumentOutOfRangeException
	 */
	public function __construct (string $ip = null)
	{
		if (!is_null($ip))
		{
			$this->setIpFromString($ip);
		}

		return $this;
	}

	/**
	 * Устанавливает значение объекта, принимая IP-адрес вида 127.0.0.0
	 *
	 * @param string $ip IP-адрес
	 *
	 * @return Ip
	 * @throws ArgumentOutOfRangeException
	 */
	public function setIpFromString (string $ip)
	{
		$long = ip2long($ip);
		if ($long == -1 || $long === false)
		{
			throw new ArgumentOutOfRangeException('ip');
		}
		else
		{
			$this->longIp = $long;
			$this->strIp = $ip;
		}

		return $this;
	}

	/**
	 * Устанавливает значение объекта, принимая IP-адрес в виде числа
	 *
	 * @param int $ip IP-адрес
	 *
	 * @return Ip
	 */
	public function setIpFromLong (int $ip)
	{
		$this->longIp = (int)$ip;
		$this->strIp = long2ip($ip);

		return $this;
	}

	/**
	 * Устанавливает значение объекта, принимая доменное имя и преобразуя его в IP-адрес
	 *
	 * @param string $domain Домен
	 *
	 * @return Ip
	 * @throws ArgumentOutOfRangeException
	 */
	public function setFromDomain (string $domain)
	{
		$this->setIpFromString(gethostbyname($domain));

		return $this;
	}

	/**
	 * Возвращает IP-адрес в виде строки
	 *
	 * @return string
	 */
	public function getIP ()
	{
		return ''.(string)$this->strIp;
	}

	/**
	 * Возвращает IP-адрес в виде числа
	 *
	 * @return int
	 */
	public function getLongIP ()
	{
		return (int)$this->longIp;
	}

	/**
	 * Магический метод, возвращающий IP-адрес в виде строки
	 *
	 * @return string
	 */
	public function __toString ()
	{
		return $this->getIP();
	}
}
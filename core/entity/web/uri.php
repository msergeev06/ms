<?php
/**
 * MSergeev\Core\Entity\Web\Uri
 *
 * @package MSergeev\Core
 * @subpackage Entity\Web
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace MSergeev\Core\Entity\Web;

class Uri
{
	/**
	 * @var string
	 */
	protected $scheme;

	/**
	 * @var
	 */
	protected $host;

	/**
	 * @var int
	 */
	protected $port;

	/**
	 * @var
	 */
	protected $user;

	/**
	 * @var
	 */
	protected $pass;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var
	 */
	protected $query;

	/**
	 * @var
	 */
	protected $fragment;

	/**
	 * @param string $url
	 * @since 0.2.0
	 */
	public function __construct($url)
	{
		if(strpos($url, "/") === 0)
		{
			//we don't support "current scheme" e.g. "//host/path"
			$url = "/".ltrim($url, "/");
		}

		$parsedUrl = parse_url($url);

		if($parsedUrl !== false)
		{
			$this->scheme = (isset($parsedUrl["scheme"])? strtolower($parsedUrl["scheme"]) : "http");
			$this->host = $parsedUrl["host"];
			if(isset($parsedUrl["port"]))
			{
				$this->port = $parsedUrl["port"];
			}
			else
			{
				$this->port = ($this->scheme == "https"? 443 : 80);
			}
			$this->user = $parsedUrl["user"];
			$this->pass = $parsedUrl["pass"];
			$this->path = ((isset($parsedUrl["path"])? $parsedUrl["path"] : "/"));
			$this->query = $parsedUrl["query"];
			$this->fragment = $parsedUrl["fragment"];
		}
	}

	/**
	 * Return the URI without a fragment.
	 * @return string
	 * @since 0.2.0
	 */
	public function getLocator()
	{
		$url = "";
		if($this->host <> '')
		{
			$url .= $this->scheme."://".$this->host;

			if(($this->scheme == "http" && $this->port <> 80) || ($this->scheme == "https" && $this->port <> 443))
			{
				$url .= ":".$this->port;
			}
		}

		$url .= $this->getPathQuery();

		return $url;
	}

	/**
	 * Return the URI with a fragment, if any.
	 * @return string
	 * @since 0.2.0
	 */
	public function getUri()
	{
		$url = $this->getLocator();

		if($this->fragment <> '')
		{
			$url .= "#".$this->fragment;
		}

		return $url;
	}

	/**
	 * Returns the fragment.
	 * @return string
	 * @since 0.2.0
	 */
	public function getFragment()
	{
		return $this->fragment;
	}

	/**
	 * Returns the host.
	 * @return string
	 * @since 0.2.0
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Sets the host
	 * @param string $host Host name.
	 * @return $this
	 * @since 0.2.0
	 */
	public function setHost($host)
	{
		$this->host = $host;
		return $this;
	}

	/**
	 * Returns the password.
	 * @return string
	 * @since 0.2.0
	 */
	public function getPass()
	{
		return $this->pass;
	}

	/**
	 * Sets the password.
	 * @param string $pass Password,
	 * @return $this
	 * @since 0.2.0
	 */
	public function setPass($pass)
	{
		$this->pass = $pass;
		return $this;
	}

	/**
	 * Returns the path.
	 * @return string
	 * @since 0.2.0
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Sets the path.
	 * @param string $path
	 * @return $this
	 * @since 0.2.0
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * Returns the path with the query.
	 * @return string
	 * @since 0.2.0
	 */
	public function getPathQuery()
	{
		$pathQuery = $this->path;
		if($this->query <> "")
		{
			$pathQuery .= '?'.$this->query;
		}
		return $pathQuery;
	}

	/**
	 * Returns the port number.
	 * @return string
	 * @since 0.2.0
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * Returns the query.
	 * @return string
	 * @since 0.2.0
	 */
	public function getQuery()
	{
		return $this->query;
	}

	/**
	 * Returns the scheme.
	 * @return string
	 * @since 0.2.0
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * Returns the user.
	 * @return string
	 * @since 0.2.0
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Sets the user.
	 * @param string $user User.
	 * @return $this
	 * @since 0.2.0
	 */
	public function setUser($user)
	{
		$this->user = $user;
		return $this;
	}

	/**
	 * Deletes parameters from the query.
	 * @param array $params Parameters to delete.
	 * @return $this
	 * @since 0.2.0
	 */
	public function deleteParams(array $params)
	{
		if($this->query <> '')
		{
			$currentParams = array();
			parse_str($this->query, $currentParams);

			foreach($params as $param)
			{
				unset($currentParams[$param]);
			}

			$this->query = http_build_query($currentParams, "", "&");
		}
		return $this;
	}

	/**
	 * Adds parameters to query or replaces existing ones.
	 * @param array $params Parameters to add.
	 * @return $this
	 * @since 0.2.0
	 */
	public function addParams(array $params)
	{
		$currentParams = array();
		if($this->query <> '')
		{
			parse_str($this->query, $currentParams);
		}

		$currentParams = array_replace($currentParams, $params);

		$this->query = http_build_query($currentParams, "", "&");

		return $this;
	}
}
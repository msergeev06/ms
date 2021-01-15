<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Web;

/**
 * Класс Ms\Core\Entity\Web\Uri
 */
class Uri
{
    /**
     * @var
     */
    protected $fragment;
    /**
     * @var
     */
    protected $host;
    /**
     * @var
     */
    protected $pass;
    /**
     * @var string
     */
    protected $path;
    /**
     * @var int
     */
    protected $port;
    /**
     * @var
     */
    protected $query;
    /**
     * @var string
     */
    protected $scheme;
    /**
     * @var
     */
    protected $user;

    /**
     * @param string $url
     */
    public function __construct ($url)
    {
        if (strpos($url, "/") === 0)
        {
            //we don't support "current scheme" e.g. "//host/path"
            $url = "/" . ltrim($url, "/");
        }

        $parsedUrl = parse_url($url);

        if ($parsedUrl !== false)
        {
            $this->scheme = (isset($parsedUrl["scheme"]) ? strtolower($parsedUrl["scheme"]) : "http");
            $this->host = $parsedUrl["host"];
            if (isset($parsedUrl["port"]))
            {
                $this->port = $parsedUrl["port"];
            }
            else
            {
                $this->port = ($this->scheme == "https" ? 443 : 80);
            }
            $this->user = $parsedUrl["user"];
            $this->pass = $parsedUrl["pass"];
            $this->path = ((isset($parsedUrl["path"]) ? $parsedUrl["path"] : "/"));
            $this->query = $parsedUrl["query"];
            $this->fragment = $parsedUrl["fragment"];
        }
    }

    /**
     * Adds parameters to query or replaces existing ones.
     *
     * @param array $params Parameters to add.
     *
     * @return $this
     */
    public function addParams (array $params)
    {
        $currentParams = [];
        if ($this->query <> '')
        {
            parse_str($this->query, $currentParams);
        }

        $currentParams = array_replace($currentParams, $params);

        $this->query = http_build_query($currentParams, "", "&");

        return $this;
    }

    /**
     * Deletes parameters from the query.
     *
     * @param array $params Parameters to delete.
     *
     * @return $this
     */
    public function deleteParams (array $params)
    {
        if ($this->query <> '')
        {
            $currentParams = [];
            parse_str($this->query, $currentParams);

            foreach ($params as $param)
            {
                unset($currentParams[$param]);
            }

            $this->query = http_build_query($currentParams, "", "&");
        }

        return $this;
    }

    /**
     * Returns the fragment.
     *
     * @return string
     */
    public function getFragment ()
    {
        return $this->fragment;
    }

    /**
     * Returns the host.
     *
     * @return string
     */
    public function getHost ()
    {
        return $this->host;
    }

    /**
     * Return the URI without a fragment.
     *
     * @return string
     */
    public function getLocator ()
    {
        $url = "";
        if ($this->host <> '')
        {
            $url .= $this->scheme . "://" . $this->host;

            if (($this->scheme == "http" && $this->port <> 80) || ($this->scheme == "https" && $this->port <> 443))
            {
                $url .= ":" . $this->port;
            }
        }

        $url .= $this->getPathQuery();

        return $url;
    }

    /**
     * Returns the password.
     *
     * @return string
     */
    public function getPass ()
    {
        return $this->pass;
    }

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath ()
    {
        return $this->path;
    }

    /**
     * Returns the path with the query.
     *
     * @return string
     */
    public function getPathQuery ()
    {
        $pathQuery = $this->path;
        if ($this->query <> "")
        {
            $pathQuery .= '?' . $this->query;
        }

        return $pathQuery;
    }

    /**
     * Returns the port number.
     *
     * @return string
     */
    public function getPort ()
    {
        return $this->port;
    }

    /**
     * Returns the query.
     *
     * @return string
     */
    public function getQuery ()
    {
        return $this->query;
    }

    /**
     * Returns the scheme.
     *
     * @return string
     */
    public function getScheme ()
    {
        return $this->scheme;
    }

    /**
     * Return the URI with a fragment, if any.
     *
     * @return string
     */
    public function getUri ()
    {
        $url = $this->getLocator();

        if ($this->fragment <> '')
        {
            $url .= "#" . $this->fragment;
        }

        return $url;
    }

    /**
     * Returns the user.
     *
     * @return string
     */
    public function getUser ()
    {
        return $this->user;
    }

    /**
     * Sets the host
     *
     * @param string $host Host name.
     *
     * @return $this
     */
    public function setHost ($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Sets the password.
     *
     * @param string $pass Password,
     *
     * @return $this
     */
    public function setPass ($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * Sets the path.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath ($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Sets the user.
     *
     * @param string $user User.
     *
     * @return $this
     */
    public function setUser ($user)
    {
        $this->user = $user;

        return $this;
    }
}
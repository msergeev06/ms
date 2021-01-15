<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules\Versions;

/**
 * Класс Ms\Core\Entity\Modules\Version
 * Описывает версию модуля
 */
class Version
{
    const REGULAR_VERSION = '/^([^\d]*)(0|[1-9]?\d*)\.?(0|\*|[1-9]?\d*)\.?(0|\*|[1-9]?\d*)(.*)$/';

    /** @var string  */
    protected $rawVersion = '';
    /** @var null|string */
    protected $operator = null;
    /** @var null|int */
    protected $major = null;
    /** @var null|int */
    protected $minor = null;
    /** @var null|int */
    protected $patch = null;
    /** @var null|string */
    protected $other = null;

    public function __construct(string $version)
    {
        $this->normalizeVersion($version);
        $this->rawVersion = $version;
        $this->parseVersion();
    }

    /**
     * Возвращает необработанную версию модуля
     *
     * @return string
     * @unittest
     */
    public function getRawVersion ()
    {
        return $this->rawVersion;
    }

    /**
     * Возвращает оператор, если он был задан, либо NULL
     *
     * @return string|null
     * @unittest
     */
    public function getOperator ()
    {
        return $this->operator;
    }

    /**
     * Возвращает МАЖОРНУЮ версию
     *
     * @return int|null
     * @unittest
     */
    public function getMajor ()
    {
        return $this->major;
    }

    /**
     * Возвращает МИНОРНУЮ версию
     *
     * @return int|null
     * @unittest
     */
    public function getMinor ()
    {
        return $this->minor;
    }

    /**
     * Возвращает ПАТЧ версию
     *
     * @return int|null
     * @unittest
     */
    public function getPatch ()
    {
        return $this->patch;
    }

    /**
     * Возвращает дополнительные данные версии, если они заданы, либо NULL
     *
     * @return string|null
     * @unittest
     */
    public function getOther ()
    {
        return $this->other;
    }

    /**
     * Возвращает собранную из частей версию. Не заданные части превращаются в 0
     *
     * @return bool|string
     * @unittest
     */
    public function getModuleVersion ()
    {
        if (is_null($this->major))
        {
            return false;
        }
        $version = ''.$this->major;
        if (is_null($this->minor))
        {
            $version .= '.0.0';

            return $version;
        }
        else
        {
            $version .= '.'.$this->minor;
        }
        if (is_null($this->patch))
        {
            $version .= '.0';

            return $version;
        }
        else
        {
            $version .= '.'.$this->patch;
        }

        return $version;
    }

    /**
     * Возвращает чистую версию. Незаданные части превращаются в *
     *
     * @return bool|string
     * @unittest
     */
    public function getClearVersion ()
    {
        if (is_null($this->major))
        {
            return false;
        }
        $version = ''.$this->major;
        if (is_null($this->minor))
        {
            $version .= '.*';

            return $version;
        }
        else
        {
            $version .= '.'.$this->minor;
        }
        if (is_null($this->patch))
        {
            $version .= '.*';

            return $version;
        }
        else
        {
            $version .= '.'.$this->patch;
        }

        return $version;
    }

    protected function normalizeVersion (string &$version)
    {
        $version = strtolower(trim($version));
    }

    protected function parseVersion ()
    {
        if (preg_match(self::REGULAR_VERSION, $this->rawVersion, $matches))
        {
            if (
                isset($matches[1])
                && $matches[1] != ''
            ) {
                $this->operator = $matches[1];
            }
            if (
                isset($matches[2])
                && $matches[2] != ''
                && $matches[2] != '*'
                && (int)$matches[2] >= 0
            ) {
                $this->major = (int)$matches[2];
            }
            if (
                !is_null($this->major)
                && isset($matches[3])
                && $matches[3] != ''
                && $matches[3] != '*'
                && (int)$matches[3] >= 0
            ) {
                $this->minor = (int)$matches[3];
            }
            if (
                !is_null($this->minor)
                && isset($matches[4])
                && $matches[4] != ''
                && $matches[4] != '*'
                && (int)$matches[4] >= 0
            ) {
                $this->patch = (int)$matches[4];
            }
            if (
                !is_null($this->patch)
                && isset($matches[5])
                && $matches[5] != ''
            ) {
                $this->other = $matches[5];
            }
        }
        $this->clearOperator ();
    }

    protected function clearOperator ()
    {
        if (is_null($this->operator) || $this->operator == '')
        {
            return;
        }
        $this->operator = strtolower($this->operator);
        $this->operator = str_replace('v.', '', $this->operator);
        $this->operator = str_replace('v', '', $this->operator);
    }
}
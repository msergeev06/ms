<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules\Versions;

use Ms\Core\Entity\Type\Date;

/**
 * Класс Ms\Core\Entity\Modules\Versions\ModuleVersion
 * Класс, описывающий версию модуля
 */
class ModuleVersion
{
    /** @var Version */
    protected $version = null;
    /** @var Date */
    protected $versionDate = null;

    public function __construct (string $version = null, Date $versionDate = null)
    {
        if (!is_null($version))
        {
            $this->version = new Version($version);
        }
        else
        {
            $this->version = new Version('0.1.0');
        }
        if (!is_null($versionDate))
        {
            $this->versionDate = $versionDate;
        }
        else
        {
            $this->versionDate = new Date();
        }
    }

    /**
     * Возвращает объект версии модуля
     *
     * @return Version
     * @unittest
     */
    public function getVersion (): Version
    {
        return $this->version;
    }

    /**
     * Возвращает номер версии модуля
     *
     * @return bool|string
     * @unittest
     */
    public function getVersionNumber ()
    {
        return $this->version->getModuleVersion();
    }

    /**
     * Устанавливает значение свойства version
     *
     * @param Version $version
     *
     * @return ModuleVersion
     * @unittest
     */
    public function setVersion (Version $version): ModuleVersion
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Возвращает значение свойства versionDate
     *
     * @return Date
     * @unittest
     */
    public function getVersionDate (): Date
    {
        return $this->versionDate;
    }

    /**
     * Устанавливает значение свойства versionDate
     *
     * @param Date $versionDate
     *
     * @return ModuleVersion
     * @unittest
     */
    public function setVersionDate (Date $versionDate): ModuleVersion
    {
        $this->versionDate = $versionDate;

        return $this;
    }
}
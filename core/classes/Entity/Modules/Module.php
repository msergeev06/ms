<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules;

use Ms\Core\Entity\Modules\Versions\ModuleVersion;
use Ms\Core\Entity\Modules\Versions\Version;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Arguments\ArgumentException;
use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Entity\Modules\Module
 * Описывает модуль системы
 */
class Module
{
    /** @var string */
    protected $moduleName = null;
    /** @var null|string */
    protected $path = null;
    /** @var ModuleVersion */
    protected $moduleVersion = null;
    /** @var bool */
    protected $included = false;
    /** @var bool */
    protected $installed = false;

    /**
     * Конструктор класса Module
     *
     * @param string $moduleName Имя модуля
     */
    public function __construct (string $moduleName)
    {
        try
        {
            $this->moduleName = strtolower($moduleName);
            $this->moduleVersion = new ModuleVersion('0.1.0', new Date());
        }
        catch (SystemException $e)
        {
        }
    }

    /**
     * Возвращает имя модуля
     *
     * @return string
     * @unittest
     */
    public function getModuleName ()
    {
        return $this->moduleName;
    }

    /**
     * Устанавливает путь к файлам модуля
     *
     * @param string $path
     *
     * @return $this
     * @unittest
     */
    public function setModulePath (string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Возвращает путь к файлам модуля
     *
     * @return string|null
     * @unittest
     */
    public function getModulePath ()
    {
        return $this->path;
    }

    /**
     * Устанавливает объект, описывающий версию модуля
     *
     * @param ModuleVersion $moduleVersion
     *
     * @return $this
     * @unittest
     */
    public function setModuleVersion (ModuleVersion $moduleVersion)
    {
        $this->moduleVersion = $moduleVersion;

        return $this;
    }

    /**
     * Возвращает объект, описывающий версию модуля
     *
     * @return ModuleVersion
     * @unittest
     */
    public function getModuleVersion ()
    {
        return $this->moduleVersion;
    }

    /**
     * Возвращает номер версии модуля
     *
     * @return bool|string
     * @unittest
     */
    public function getModuleVersionNumber ()
    {
        return $this->moduleVersion->getVersionNumber();
    }

    /**
     * Возвращает дату версии модуля
     *
     * @return Date
     * @unittest
     */
    public function getModuleVersionDate ()
    {
        return $this->moduleVersion->getVersionDate();
    }

    /**
     * Устанавливает флаг, указывающий на то, был ли модуль уже подключен
     *
     * @param bool $included
     *
     * @return $this
     * @unittest
     */
    public function setIncluded (bool $included = true)
    {
        $this->included = $included;

        return $this;
    }

    /**
     * Возвращает TRUE, если модуль уже был подключен, иначе возвращает FALSE
     *
     * @return bool
     * @unittest
     */
    public function isIncluded ()
    {
        return $this->included;
    }

    /**
     * Устанавливает флаг, указывающий на то, установлен ли текущий модуль
     *
     * @param bool $installed
     *
     * @return $this
     * @unittest
     */
    public function setInstalled (bool $installed = true)
    {
        $this->installed = $installed;

        return $this;
    }

    /**
     * Возвращает TRUE, если модуль установлен, иначе FALSE
     *
     * @return bool
     * @unittest
     */
    public function isInstalled ()
    {
        return $this->installed;
    }

    /**
     * Возвращает описание модуля
     *
     * @return array
     * @unittest
     */
    public function getModuleInfo ()
    {
        $filename = $this->path . '/'.$this->moduleName.'.php';
        if (file_exists($filename))
        {
            return include ($filename);
        }

        return [];
    }

    /**
     * Возвращает название модуля, если оно задано, либо имя модуля
     *
     * @return string
     * @unittest
     */
    public function getModuleInfoName ()
    {
        $arModuleInfo = $this->getModuleInfo();
        if (empty($arModuleInfo) || !array_key_exists('NAME',$arModuleInfo))
        {
            return $this->moduleName;
        }

        return $arModuleInfo['NAME'];
    }

    /**
     * Возвращает описание модуля, если оно задано, либо пустую строку
     *
     * @return string
     * @unittest
     */
    public function getModuleInfoDescription ()
    {
        $arModuleInfo = $this->getModuleInfo();
        if (empty($arModuleInfo) || !array_key_exists('DESCRIPTION',$arModuleInfo))
        {
            return '';
        }

        return $arModuleInfo['DESCRIPTION'];
    }

    /**
     * Возвращает ссылку на официальную страницу модуля, если она задана, либо пустую строку
     *
     * @return string
     * @unittest
     */
    public function getModuleInfoUrl ()
    {
        $arModuleInfo = $this->getModuleInfo();
        if (empty($arModuleInfo) || !array_key_exists('URL',$arModuleInfo))
        {
            return '';
        }

        return $arModuleInfo['URL'];
    }

    /**
     * Возвращает ссылку на официальную документацию по модулю, если она задана, либо пустую строку
     *
     * @return string
     * @unittest
     */
    public function getModuleInfoUrlDocs ()
    {
        $arModuleInfo = $this->getModuleInfo();
        if (empty($arModuleInfo) || !array_key_exists('DOCS',$arModuleInfo))
        {
            return '';
        }

        return $arModuleInfo['DOCS'];
    }

    /**
     * Возвращает имя автора модуля, если оно задано, либо пустую строку
     *
     * @return string
     * @unittest
     */
    public function getModuleInfoAuthor ()
    {
        $arModuleInfo = $this->getModuleInfo();
        if (empty($arModuleInfo) || !array_key_exists('AUTHOR',$arModuleInfo))
        {
            return '';
        }

        return $arModuleInfo['AUTHOR'];
    }

    /**
     * Возвращает email автора, если он задан, либо пустую строку
     *
     * @return string
     * @unittest
     */
    public function getModuleInfoAuthorEmail ()
    {
        $arModuleInfo = $this->getModuleInfo();
        if (empty($arModuleInfo) || !array_key_exists('AUTHOR_EMAIL',$arModuleInfo))
        {
            return '';
        }

        return $arModuleInfo['AUTHOR_EMAIL'];
    }
}
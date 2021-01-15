<?php
/**
 * //TODO: Дописать, когда будет на чем тестировать
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules;

use Ms\Core\Entity\System\Settings;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\Modules\ModuleDoesNotExistsException;
use Ms\Core\Lib\Errors;

/**
 * Класс Ms\Core\Entity\Modules\Installer
 * Установщик модулей
 */
class Installer
{
    /** @var string|null  */
    protected $moduleName = null;
    /** @var string */
    protected $modulePath = null;
    /** @var ModuleInstaller|null Установщик конкретного модуля */
    protected $moduleInstaller = null;
    /** @var bool  */
    protected $success = false;

    public function __construct(string $moduleName)
    {
        if (!Loader::issetModule($moduleName))
        {
            throw new ModuleDoesNotExistsException(
                $moduleName,
                Errors::ERROR_MODULE_NOT_EXIST,
                __FILE__,
                __LINE__
            );
        }

        $this->moduleName = $moduleName;
        if ($path = Loader::getInstance()->getModulePath($moduleName))
        {
            $this->modulePath = (string)$path;
        }
        else
        {
            $this->modulePath = Settings::getInstance()->getModulesRoot() . '/' . strtolower($moduleName);
        }

        if (file_exists($this->modulePath . '/install/installer.php'))
        {
            $this->moduleInstaller = include_once ($this->modulePath . '/install/installer.php');
            if (!($this->moduleInstaller instanceof ModuleInstaller))
            {
                throw new ArgumentTypeException(
                    'ModuleInstaller',
                    '\Ms\Core\Entity\Modules\ModuleInstaller'
                );
            }
        }
        else
        {
            try
            {
                $this->moduleInstaller = new ModuleInstaller($moduleName);
            }
            catch (ModuleDoesNotExistsException $e){}
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
     * Возвращает путь к папке с файлами модуля
     *
     * @return string
     * @unittest
     */
    public function getModulePath ()
    {
        return $this->modulePath;
    }

    /**
     * Возвращает объект-установщик модуля, либо NULL
     *
     * @return ModuleInstaller|null
     * @unittest
     */
    public function getModuleInstaller ()
    {
        return $this->moduleInstaller;
    }

    /**
     * Задает объект-установщик модуля
     *
     * @param ModuleInstaller $installer
     *
     * @return $this
     * @unittest
     */
    public function setModuleInstaller (ModuleInstaller $installer)
    {
        $this->moduleInstaller = $installer;

        return $this;
    }

    /**
     * Запускает процесс установки модуля, если он не установлен еще
     *
     * @return $this
     * @unittest
     */
    public function install ()
    {
        if (file_exists($this->modulePath . '/installed'))
        {
            $this->success = true;

            return $this;
        }

        $bResult = $this->moduleInstaller->doInstall();

        if ($bResult)
        {
            $this->createInstalledFile();
            $this->moduleInstaller->startInstallWizard();
        }

        $this->success = $bResult;

        return $this;
    }

    /**
     * Запускает процесс удаления модуля, если он установлен. Затем запускает процесс установки модуля
     *
     * @param bool  $clearDbData Флаг - удалять данные модуля из БД, при удалении модуля
     * @param array $arParams    Дополнительные параметры
     *
     * @return $this
     * @unittest
     */
    public function reInstall (bool $clearDbData = true, array &$arParams = [])
    {
        if (file_exists($this->modulePath . '/installed'))
        {
            $bFirstResult = $this->unInstall($clearDbData, $arParams);
        }
        else
        {
            $bFirstResult = true;
        }

        if ($bFirstResult)
        {
            $bSecondResult = $this->install();
        }
        else
        {
            $bSecondResult = false;
        }

        $this->success = ($bFirstResult && $bSecondResult);

        return $this;
    }

    /**
     * Запускает процесс удаления модуля, если он установлен
     *
     * @param bool  $clearDbData Флаг - удалять данные модуля из БД
     * @param array $arParams    Дополнительные параметры
     *
     * @return $this
     * @unittest
     */
    public function unInstall (bool $clearDbData = true, array &$arParams = [])
    {
        if (!file_exists($this->modulePath . '/installed'))
        {
            $this->success = true;

            return $this;
        }

        if ($bResult = $this->moduleInstaller->doUnInstall($clearDbData, $arParams))
        {
            $this->deleteInstalledFile();
        }

        $this->success = $bResult;

        return $this;
    }

    /**
     * Возвращает TRUE, если установка/переустановка/удаление модуля прошло успешно, иначе FALSE
     *
     * @return bool
     * @unittest
     */
    public function isSuccess ()
    {
        return $this->success;
    }

    protected function createInstalledFile ()
    {
        try
        {
            if (!file_exists($this->modulePath . '/installed'))
            {
                $date = new Date();
                $f1 = fopen($this->modulePath . '/installed', 'w');
                fwrite($f1, 'Installed: ' . $date->getDateTimeSite());
                fclose($f1);
            }
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    protected function deleteInstalledFile ()
    {
        try
        {
            if (file_exists($this->modulePath . '/installed'))
            {
                unlink($this->modulePath . '/installed');
            }
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }
}
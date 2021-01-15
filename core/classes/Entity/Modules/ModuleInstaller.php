<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules;

use Ms\Core\Exceptions\Modules\ModuleDoesNotExistsException;
use Ms\Core\Lib\Errors;

/**
 * Класс Ms\Core\Entity\Modules\ModuleInstaller
 * Установщик конкретного модуля, может наследоваться другими модулями
 */
class ModuleInstaller
{
    protected $moduleName = null;
    protected $modulePath = null;

    /**
     * Конструктор класса ModuleInstaller
     *
     * @param string $moduleName
     *
     * @throws ModuleDoesNotExistsException
     */
    public function __construct (string $moduleName)
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
        $this->modulePath = Loader::getInstance()->getModulePath($moduleName);
    }

    /**
     * Вызывает поочередно методы copyFiles, createTables, addEvents.
     * Если какой-то из них вернет false, прерывает оставшиеся вызовы и возвращает false
     *
     * @return bool
     * @unittest
     */
    public function doInstall ()
    {
        if (!$this->copyFiles())
        {
            return false;
        }
        if (!$this->createTables())
        {
            return false;
        }
        if (!$this->addEvents())
        {
            return false;
        }

        return true;
    }

    /**
     * Вызывает поочередно методы deleteFiles, dropTables, clearEvents
     * Если какой-то из этих методов вернет false, прерывает вызов оставшихся методов и возвращает false
     *
     * @param bool  $clearDbData Флаг обозначающий необходимость удалить данные из БД
     * @param array $arParams    Дополнительные параметры
     *
     * @return bool
     * @unittest
     */
    public function doUnInstall (bool $clearDbData = true, array $arParams = [])
    {
        if (!$this->deleteFiles($arParams))
        {
            return false;
        }
        if (!$this->dropTables($clearDbData, $arParams))
        {
            return false;
        }
        if (!$this->clearEvents($arParams))
        {
            return false;
        }

        return true;
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
     * Возвращает путь к файлам модуля
     *
     * @return string
     * @unittest
     */
    public function getModulePath ()
    {
        return $this->modulePath;
    }

    /**
     * Метод-заглушка для старта "Мастера установки"
     *
     * @return bool
     * @unittest
     */
    public function startInstallWizard ()
    {
        return true;
    }

    /**
     * Метод-заглушка для старта "Мастера удаления"
     *
     * @param array $arParams Дополнительные параметры
     *
     * @return bool
     * @unittest
     */
    public function startUnInstallWizard (array &$arParams = [])
    {
        return true;
    }

    /**
     * Метод-заглушка для процесса добавления обработчиков событий
     *
     * @return bool
     */
    protected function addEvents ()
    {
        return true;
    }

    /**
     * Метод-заглушка для процесса удаления обработчиков событий
     *
     * @param array $arParams Дополнительные параметры
     *
     * @return bool
     */
    protected function clearEvents (array $arParams)
    {
        return true;
    }

    /**
     * Метод-заглушка для процесса копирования файлов при установке модуля
     *
     * @return bool
     */
    protected function copyFiles ()
    {
        return true;
    }

    /**
     * Метод-заглушка для процесса создания таблиц модуля
     *
     * @return bool
     */
    protected function createTables ()
    {
        return true;
    }

    /**
     * Метод-заглушка для процесса удаления файлов при удалении модуля
     *
     * @param array $arParams Дополнительные параметры
     *
     * @return bool
     */
    protected function deleteFiles (array $arParams = [])
    {
        return true;
    }

    /**
     * Метод-заглушка для процесса удаления таблиц в БД
     *
     * @param bool  $clearDbData Флаг, обозначающий необходимость удаления таблиц в БД
     * @param array $arParams    Дополнительные параметры
     *
     * @return bool
     */
    protected function dropTables (bool $clearDbData = true, array $arParams = [])
    {
        return true;
    }
}
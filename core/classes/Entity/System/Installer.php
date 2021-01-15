<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

use Ms\Core\Api\ApiAdapter;
use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\Modules\Loader;
use Ms\Core\Exceptions\Classes\ClassNotFoundException;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Lib\IO\Files;
use Ms\Core\Entity\Modules\Modules;

/**
 * Класс Ms\Core\Entity\System\Installer
 * Установщик модулей и их параметров
 */
class Installer extends Multiton
{
    /**
     * Копирует файл/файлы в указанный файл/папку
     *
     * @param string $fromPath         Копируемая директория или файл
     * @param string $toPath           Директория или файл назначения
     * @param bool   $bRewrite         Перезаписывать существующие файлы (по умолчанию TRUE, перезаписывать)
     * @param bool   $bRecursive       Рекурсивное копирование (по умолчанию TRUE, рекурсивное копирование)
     * @param bool   $bDeleteAfterCopy Удалить исходные файлы после копирования
     *                                 (по умолчанию FALSE, не удалять)
     *
     * @return bool При удачном копировании возвращает TRUE, иначе FALSE
     */
    public function copyFiles (
        $fromPath,
        $toPath,
        $bRewrite = true,
        $bRecursive = true,
        $bDeleteAfterCopy = false
    ) {
        return Files::copyDirFiles(
            $fromPath,
            $toPath,
            $bRewrite,
            $bRecursive,
            $bDeleteAfterCopy
        );
    }

    /**
     * Создает backup таблиц базы данных указанного модуля
     * //TODO: Протестировать
     *
     * Использует для своей работы функцию exec
     *
     * @link http://php.net/manual/ru/function.exec.php
     *
     * @param string $moduleName
     */
    public function createBackupDbForModule ($moduleName)
    {
        $app = Application::getInstance();
        $DB = $app->getConnection();

        $arTables = Modules::getInstance()->getModuleTableNames($moduleName);
        exec(
            $DB->getDumpCommand(
                $app->getSettings()->getDirBackupDb(),
                false,
                $moduleName,
                $arTables
            )
        );
    }

    /**
     * Создает таблицы ядра
     */
    public function createCoreTables ()
    {
        $strTablesNamespace = "Ms\\Core\\Tables\\";
        $dir = Application::getInstance()->getSettings()->getCoreRoot() . '/Tables/';
        $arTables = Files::getListFiles($dir, ['.readme']);

        foreach ($arTables as $fileTable)
        {
            $className = Modules::getInstance()->getTableClassByFileName($fileTable);

            $runClass = $strTablesNamespace . $className;
            try
            {
                $this->getOrm($runClass)->createTable();
                $this->getOrm($runClass)->insertDefaultRows();
            }
            catch (SystemException $e)
            {
            }
        }
    }

    /*	public static function restoreFromDump ()
        {
            //mysql -uUSER -pPASS DATABASE < /path/to/dump.sql //Проверенно, работает
            //gunzip < /path/to/outputfile.sql.gz | mysql -u USER -pPASSWORD DATABASE //Не проверял
        }*/

    /**
     * Создает таблицы указанного модуля
     *
     * @param string $strModuleName Имя модуля
     *
     * @return bool
     * @throws \Ms\Core\Exceptions\Classes\ClassNotFoundException
     */
    public function createModuleTables ($strModuleName)
    {
        $strModuleName = strtolower($strModuleName);
        if (!$strNamespace = Modules::getInstance()->getModuleNamespaceTables($strModuleName))
        {
            return false;
        }
        try
        {
            if (!Loader::includeModule($strModuleName))
            {
                return false;
            }
        }
        catch (SystemException $e)
        {
            return false;
        }

        if (!$arTables = Modules::getInstance()->getModuleTableFiles($strModuleName))
        {
            return false;
        }

        foreach ($arTables as $fileTable)
        {
            $className = Modules::getInstance()->getTableClassByFileName($fileTable);
            if (
            !class_exists($strNamespace . $className)
            )
            {
                throw new ClassNotFoundException($className);
            }

            $runClass = $strNamespace . $className;
            try
            {
                $this->getOrm($runClass)->createTable();
                $this->getOrm($runClass)->insertDefaultRows();
            }
            catch (SystemException $e)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Удаляет таблицы указанного модуля
     *
     * @param string $strModuleName        Имя модуля
     * @param bool   $bIgnoreForeignFields Флаг - игнорировать поля, являющиеся FOREIGN KEY
     *
     * @return bool
     */
    public function dropModuleTables ($strModuleName, bool $bIgnoreForeignFields = false)
    {
        $strModuleName = strtolower($strModuleName);
        if (!$strNamespace = Modules::getInstance()->getModuleNamespaceTables($strModuleName))
        {
            return false;
        }
        try
        {
            if (!Loader::includeModule($strModuleName))
            {
                return false;
            }
        }
        catch (SystemException $e)
        {
            return false;
        }

        if (!$arTables = Modules::getInstance()->getModuleTableFiles($strModuleName))
        {
            return false;
        }

        foreach ($arTables as $fileTable)
        {
            $className = Modules::getInstance()->getTableClassByFileName($fileTable);
            if (
            !class_exists($strNamespace . $className)
            )
            {
                return false;
            }

            $runClass = $strNamespace . $className;
            try
            {
                $res = $this->getOrm($runClass)->dropTable($bIgnoreForeignFields);
            }
            catch (SqlQueryException $e)
            {
                $res = new DBResult();
            }
            if (!$res->isSuccess())
            {
                return false;
            }
        }

        return true;
    }

    private function getOrm (string $className)
    {
        return ApiAdapter::getInstance()->getDbApi()->getTableOrmByClass($className);
    }
}
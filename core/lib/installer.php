<?php
/**
 * Ms\Core\Lib\Installer
 * Установщик модулей и их параметров
 *
 * @package Ms\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @since 0.1.0
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\Application;
use Ms\Core\Exception\ClassNotFoundException;
use Ms\Core\Lib\IO\Files;

class Installer
{
	/**
	 * Создает таблицы указанного модуля
	 *
	 * @api
	 *
	 * @param string $strModuleName Имя модуля
	 *
	 * @return bool
	 * @since 0.2.0
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/installer/method_create_module_tables
	 */
	public static function createModuleTables ($strModuleName)
	{
		$strModuleName = strtolower($strModuleName);
		if (!$strNamespace = Modules::getModuleNamespaceTables($strModuleName))
		{
			return false;
		}
		if (!Loader::includeModule($strModuleName))
		{
			return false;
		}

		if (!$arTables = Modules::getModuleTableFiles($strModuleName)){
			return false;
		}

		foreach ($arTables as $fileTable)
		{
			try {
				$className = Modules::getTableClassByFileName($fileTable);
				if (!Loader::classExists($strNamespace.$className) && !class_exists($strNamespace.$className))
				{
					throw new ClassNotFoundException($className);
				}
			}
			catch (ClassNotFoundException $e)
			{
				die($e->showException());
			}

			/** @var DataManager $runClass */
			$runClass = $strNamespace.$className;
			$runClass::createTable();
			$runClass::insertDefaultRows();
		}

		return true;
	}

	/**
	 * Создает backup таблиц базы данных указанного модуля
	 *
	 * Использует для своей работы функцию exec
	 * @link http://php.net/manual/ru/function.exec.php
	 *
	 * @param string $moduleName
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/installer/method_create_backup_db_for_module
	 */
	public static function createBackupDbForModule ($moduleName)
	{
		$app = Application::getInstance();
		$DB = $app->getConnection();

		$arTables = Modules::getModuleTableNames($moduleName);
		exec($DB->getDumpCommand($app->getSettings()->getDirBackupDb(),false,$moduleName,$arTables));
	}

/*	public static function restoreFromDump ()
	{
		//mysql -uUSER -pPASS DATABASE < /path/to/dump.sql //Проверенно, работает
		//gunzip < /path/to/outputfile.sql.gz | mysql -u USER -pPASSWORD DATABASE //Не проверял
	}*/

	/**
	 * Создает таблицы ядра
	 *
	 * @api
	 *
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/installer/method_create_core_tables
	 */
	public static function createCoreTables ()
	{
		$strTablesNamespace = "Ms\\Core\\Tables\\";
		$dir = Application::getInstance()->getSettings()->getCoreRoot().'/tables/';
		$arTables = Files::getListFiles($dir,array('.readme'));

		foreach ($arTables as $fileTable)
		{
			$className = Modules::getTableClassByFileName($fileTable);

			/** @var DataManager $runClass */
			$runClass = $strTablesNamespace.$className;
			$runClass::createTable();
			$runClass::insertDefaultRows();
		}
	}

	/**
	 * Удаляет таблицы указанного модуля
	 *
	 * @param string $strModuleName Имя модуля
	 *
	 * @return bool
	 */
	public static function dropModuleTables ($strModuleName)
	{
		$strModuleName = strtolower($strModuleName);
		if (!$strNamespace = Modules::getModuleNamespaceTables($strModuleName))
		{
			return false;
		}
		if (!Loader::includeModule($strModuleName))
		{
			return false;
		}

		if (!$arTables = Modules::getModuleTableFiles($strModuleName)){
			return false;
		}

		foreach ($arTables as $fileTable)
		{
			try {
				$className = Modules::getTableClassByFileName($fileTable);
				if (!Loader::classExists($strNamespace.$className) && !class_exists($strNamespace.$className))
				{
					throw new ClassNotFoundException($className);
				}
			}
			catch (ClassNotFoundException $e)
			{
				die($e->showException());
			}

			/** @var DataManager $runClass */
			$runClass = $strNamespace.$className;
			$bDelete = $runClass::dropTable();
			if ($bDelete !== true)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Копирует файл/файлы в указанный файл/папку
	 *
	 * @param string $fromPath Копируемая директория или файл
	 * @param string $toPath Директория или файл назначения
	 * @param bool   $bRewrite Перезаписывать существующие файлы (по умолчанию TRUE, перезаписывать)
	 * @param bool   $bRecursive Рекурсивное копирование (по умолчанию TRUE, рекурсивное копирование)
	 * @param bool   $bDeleteAfterCopy Удалить исходные файлы после копирования (по умолчанию FALSE, не удалять)
	 *
	 * @return bool При удачном копировании возвращает TRUE, иначе FALSE
	 */
	public static function copyFiles ($fromPath, $toPath, $bRewrite=TRUE, $bRecursive=TRUE, $bDeleteAfterCopy=FALSE)
	{
		return Files::copyDirFiles($fromPath, $toPath, $bRewrite, $bRecursive, $bDeleteAfterCopy);
	}
}
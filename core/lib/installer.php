<?php
/**
 * MSergeev\Core\Lib\Installer
 * Установщик модулей и их параметров
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @since 0.1.0
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Entity\Application;
use MSergeev\Core\Lib\IO\Files;

class Installer
{
	/**
	 * Создает таблицы указанного модуля
	 *
	 * @api
	 *
	 * @param string $strModuleName Имя пакета
	 *
	 * @return bool
	 * @since 0.2.0
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
			$className = Modules::getTableClassByFileName($fileTable);

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
	 */
	public static function createCoreTables ()
	{
		$strTablesNamespace = "MSergeev\\Core\\Tables\\";
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

}
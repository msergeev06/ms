<?php
/**
 * MSergeev\Core\Lib\Modules
 * Класс для работы с модулями
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace MSergeev\Core\Lib;


use MSergeev\Core\Entity\Application;
use MSergeev\Core\Entity\ErrorCollection;
use MSergeev\Core\Lib\IO\Files;

class Modules
{
	/**
	 * Имя модуля должно быть вида:
	 * [буква][буква|цифра|ничего].[буква][буква|цифра|тире|подчеркивание]
	 * первый символ только буквы от a до z
	 * далее любое количество символов (буквы a-z и цифры 0-9)
	 * точка (.) в качестве разделителя бренда и имени модуля
	 * после точки первый символ - буква от a до z
	 * далее любое количество символов (буквы a-z, цифры 0-9 и символ подчёркивания)
	 */
	const REGULAR_EXPRESSION = '/^([a-z]{1}[a-z0-9]*)[.]{1}([a-z]{1}[a-z0-9_]*)$/';

	/**
	 * Максимально допустимая длина имени модуля, включая бренд
	 */
	const MAX_LENGTH_MODULE_NAME = 100;

	/**
	 * @var string
	 */
	private static $modulesRoot=null;

	/**
	 * Инициализирует необходимые переменные
	 */
	protected static function init ()
	{
		if (is_null(static::$modulesRoot))
		{
			static::$modulesRoot = Application::getInstance()->getSettings()->getModulesRoot();
		}
	}

	/**
	 * Проверяет имя модуля на соответствие стандартам
	 *
	 * @api
	 *
	 * @param string $moduleName
	 *
	 * @return bool
	 */
	public static function checkModuleName ($moduleName)
	{
		static::init();
		$moduleName = strtolower($moduleName);

		//Проверяем на наличае бренда
		if (strpos($moduleName,'.')===false)
		{
			//static::$errorCollection->add('В имени модуля отсутствует бренд','MODULE_NAME_EMPTY_BRAND');
			return false;
		}

		//Проверяем на использование только разрешенных символов и верный синтаксис
		if (!preg_match(self::REGULAR_EXPRESSION, $moduleName))
		{
			//static::$errorCollection->add('Использованы недопустимые символы в имени модуля');
			return false;
		}

		//Проверяем на допустимую длинну
		if (strlen($moduleName)>self::MAX_LENGTH_MODULE_NAME)
		{
			//static::$errorCollection->add('Имя модуля слишком длинное. Допустимая длина '.self::MAX_LENGTH_MODULE_NAME.' символов');
			//return false;
		}

		return true;
	}

	/**
	 * Разбирает имя модуля на бренд и имя
	 *
	 * Разбирает имя модуля на бренд и имя и возвращает в массиве с ключами BRAND и MODULE соответственно.
	 * В случае ошибки возвращает false
	 *
	 * @api
	 *
	 * @param string $moduleName
	 *
	 * @return array|bool
	 */
	public static function parseModuleName ($moduleName)
	{
		static::init();
		$moduleName = strtolower($moduleName);

		//Если имя модуля соответствует стандартам
		if (static::checkModuleName($moduleName))
		{
			$matches = null;
			preg_match(self::REGULAR_EXPRESSION,$moduleName, $matches);
			if (isset($matches[1]) && isset($matches[2]))
			{
				return array('BRAND'=>$matches[1],'MODULE'=>$matches[2]);
			}
		}

		return false;
	}

	/**
	 * Возвращает Namespace модуля вида [Brand]\Modules\[ModuleName]\
	 *
	 * @api
	 *
	 * @param string $moduleName - полное имя модуля
	 *
	 * @return bool|string
	 */
	public static function getModuleNamespace ($moduleName)
	{
		static::init();
		$moduleName = strtolower($moduleName);

		//Если имя модуля соответствует стандартам и нормально разобралось
		if ($arModule = static::parseModuleName($moduleName))
		{
			//Если бренд ms, используем особое начало namespace
			if ($arModule['BRAND']=='ms')
			{
				$namespace = 'MSergeev\\';
			}
			//Иначе берем бренд, как начало namespace
			else
			{
				$namespace = Tools::setFirstCharToBig($arModule['BRAND']).'\\';
			}
			$module = $arModule['MODULE'];
			//Если имя модуля разделено символом подчёркивания, то в namespace оно должно
			//быть в верхнем CamelCase, где каждое слово пишется с большой буквы
			if (strpos($module,'_')!==false)
			{
				$arName = explode('_',$module);
				$module = '';
				foreach ($arName as $name)
				{
					$module.=Tools::setFirstCharToBig($name);
				}
			}
			else
			{
				$module = Tools::setFirstCharToBig($module);
			}
			$namespace.='Modules\\'.$module.'\\';

			return $namespace;
		}

		return false;
	}

	/**
	 * Возвращает namespace для таблиц модуля
	 *
	 * @api
	 *
	 * @param string $moduleName
	 *
	 * @return bool|string
	 */
	public static function getModuleNamespaceTables ($moduleName)
	{
		static::init();
		$moduleName = strtolower($moduleName);

		//Если namespace модуля успешно получен
		if ($namespace = static::getModuleNamespace($moduleName))
		{
			$namespace.='Tables\\';
			return $namespace;
		}

		return false;
	}

	/**
	 * Возвращает список файлов таблиц модуля
	 *
	 * @api
	 *
	 * @param string $moduleName
	 *
	 * @return array|bool
	 */
	public static function getModuleTableFiles ($moduleName)
	{
		static::init();

		if (!static::checkModuleName($moduleName))
		{
			return false;
		}
		if (!Loader::includeModule($moduleName))
		{
			return false;
		}

		$arTables = Files::getListFiles(
			static::getPathToModuleTablesFiles($moduleName),
			array('.readme')
		);

		return ($arTables) ? $arTables : false;
	}

	/**
	 * Возвращает путь к файлам с описанием  таблиц модуля
	 *
	 * @api
	 *
	 * @param string $moduleName
	 *
	 * @return bool|string
	 */
	public static function getPathToModuleTablesFiles ($moduleName)
	{
		if (static::checkModuleName($moduleName))
		{
			$modulesRoot = Application::getInstance()->getSettings()->getModulesRoot();

			return $modulesRoot.'/'.$moduleName.'/tables';
		}

		return false;
	}

	/**
	 * Возвращает класс таблицы по имени файла с описанием таблицы
	 *
	 * @api
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public static function getTableClassByFileName ($filename)
	{
		$filename = str_replace('.php','',$filename);
		$arClass = explode("_",$filename);
		if ($arClass && !empty($arClass))
		{
			$className = "";
			foreach ($arClass as $strName)
			{
				$className .= Tools::setFirstCharToBig($strName);
			}
		}
		else
		{
			$className = Tools::setFirstCharToBig($arClass);
		}
		$className .= "Table";

		return $className;
	}

	/**
	 * Возвращает список имен таблиц модуля, либо false
	 *
	 * @api
	 *
	 * @param string $moduleName полное имя модуля
	 *
	 * @return array|bool
	 */
	public static function getModuleTableNames ($moduleName)
	{
		static::init();

		if (!$strNamespace = Modules::getModuleNamespaceTables($moduleName))
		{
			return false;
		}
		if ($arTableFiles = static::getModuleTableFiles($moduleName))
		{
			$arNames = array();
			foreach ($arTableFiles as $fileTable)
			{
				$className = static::getTableClassByFileName($fileTable);

				/** @var DataManager $runClass */
				$runClass = $strNamespace.$className;
				$arNames[] = $runClass::getTableName();
			}
			if (!empty($arNames))
			{
				return $arNames;
			}
		}

		return false;
	}

	/**
	 * Возвращает полный путь к директории загрузки пользовательских файлов модуля
	 *
	 * @api
	 *
	 * @param string $moduleName   Имя модуля
	 *
	 * @return string|bool  Путь, либо false
	 */
	public static function getUpload ($moduleName)
	{
		$moduleName = strtolower($moduleName);
		if (!is_null($moduleName))
		{
			$uploadDir = Application::getInstance()->getSettings()->getUploadDir();
			return $uploadDir.'/modules/'.str_replace('.','_',$moduleName);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает имя модуля по namespace
	 *
	 * @api
	 *
	 * @param string $namespace
	 *
	 * @return null|string
	 */
	public static function getModuleFromNamespace ($namespace)
	{
		//Удаляем возможный первый символ \ перед namespace
		if ($namespace[0] == '\\')
		{
			$namespace = substr ($namespace, 1, strlen ($namespace));
		}
		$namespace = str_replace ('\\\\', '\\', $namespace);
		$arName = explode ('\\', $namespace);
		if (strtolower ($arName[1]) == 'modules' && isset($arName[2]))
		{
			return strtolower ($arName[0]).'.'
			.strtolower (preg_replace ('/(?<=.)[A-Z]/', '_$0', $arName[2]));
		} elseif (strtolower ($arName[1]) == 'core')
		{
			return 'core';
		} else
		{
			return NULL;
		}
	}

	/**
	 * Запускает файл установки модуля, возвращая результат запуска
	 *
	 * @param string $moduleName
	 *
	 * @return bool
	 */
	public static function installModule ($moduleName)
	{
		static::init();
		if (static::checkModuleName($moduleName))
		{
			if (file_exists(static::$modulesRoot.'/'.$moduleName.'/install/install.php'))
			{
				$installReturn = include(static::$modulesRoot.'/'.$moduleName.'/install/install.php');
				return !($installReturn === false);
			}
		}

		return false;
	}

	/**
	 * Запускает файл удаления модуля, возвращая результат запуска
	 *
	 * @param string $moduleName
	 *
	 * @return bool
	 */
	public static function unInstallModule ($moduleName)
	{
		static::init();
		if (static::checkModuleName($moduleName))
		{
			if (file_exists(static::$modulesRoot.'/'.$moduleName.'/install/uninstall.php'))
			{
				$installReturn = include(static::$modulesRoot.'/'.$moduleName.'/install/uninstall.php');
				return !($installReturn === false);
			}
		}

		return false;
	}

	/**
	 * Возвращает TRUE если текущая версия больше или равна требуемой
	 * $versionCurrent >= $versionRequired
	 *
	 * @param string $versionCurrent Текущая версия модуля. Формат "XX.XX.XX"
	 * @param string $versionRequired Требуемая версия модуля. Формат "XX.XX.XX"
	 *
	 * @return bool
	 */
	public static function checkVersion($versionCurrent, $versionRequired)
	{
		$arr1 = explode(".",$versionCurrent);
		$arr2 = explode(".",$versionRequired);
		if (intval($arr2[0])>intval($arr1[0])) return false;
		elseif (intval($arr2[0])<intval($arr1[0])) return true;
		else
		{
			if (intval($arr2[1])>intval($arr1[1])) return false;
			elseif (intval($arr2[1])<intval($arr1[1])) return true;
			else
			{
				if (intval($arr2[2])>intval($arr1[2])) return false;
				elseif (intval($arr2[2])<intval($arr1[2])) return true;
				else return true;
			}
		}
	}
}
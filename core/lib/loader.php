<?php
/**
 * Ms\Core\Lib\Loader
 * Класс для подключения модулей
 *
 * @package Ms\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @copyright 2018 Mikhail Sergeev
 * @since 0.1.0
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\Application;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exception;

class Loader
{
	/**
	 * @var array Список установленных модулей и их параметров
	 */
	private static $arModules;

	/**
	 * @var string Полный путь к установленным модулям
	 */
	private static $modulesRoot;

	/**
	 * @var string Полный путь к директории загрузки пользовательских файлов
	 */
	private static $uploadRoot;

	/**
	 * @var array Список уже загруженных модулей
	 */
	private static $arIncludedModules;

	/**
	 * @var array Массив для автозагрузки классов модулей
	 */
	private static $arAutoLoadClasses=array();

	/**
	 * Инициализация модулей. Создает список установленных модулей
	 */
	public static function init ()
	{
		$app = Application::getInstance();
		//Определяет основные пути для модулей
		static::$modulesRoot = $app->getSettings()->getModulesRoot();
		static::$uploadRoot = $app->getSettings()->getUploadDir();
		if (is_dir(static::$modulesRoot))
		{
			if ($dh = opendir(static::$modulesRoot))
			{
				//Смотрим все папки модулей
				while (($file = @readdir($dh)) !== false)
				{
					if ($file != "." && $file != ".." && $file != '.readme')
					{
						//Если папка соответствует требованиям названия модуля
						if (Modules::checkModuleName($file))
						{
							//Сохраняем название модуля
							static::$arModules[$file] = array();
							//Если существует файл версии модуля, обрабатываем
							if (file_exists(static::$modulesRoot.'/'.$file.'/version.php'))
							{
								static::$arModules[$file]['INSTALLED_VERSION'] = include(static::$modulesRoot.'/'.$file.'/version.php');
								if (isset(static::$arModules[$file]['INSTALLED_VERSION']['VERSION_DATE']))
								{
									static::$arModules[$file]['INSTALLED_VERSION']['VERSION_DATE'] = new Date(static::$arModules[$file]['INSTALLED_VERSION']['VERSION_DATE'],'db');
								}
							}
						}
					}
				}
				@closedir($dh);
			}
		}
	}

	/**
	 * Подключает файл, содержащий требуемый класс, если он был добавлен
	 *
	 * @param string $className
	 *
	 * @throw Exception\ClassNotFoundException Если класс не был добавлен в автозагрузку
	 */
	public static function autoLoadClasses ($className)
	{
		try
		{
			if (isset(static::$arAutoLoadClasses[$className]))
			{
				include_once(static::$arAutoLoadClasses[$className]);
			}
			else
			{
				throw new Exception\ClassNotFoundException($className);
			}
		}
		catch (Exception\ClassNotFoundException $e)
		{
			die($e->showException());
		}
	}

	/**
	 * Добавляет массив классов в автозагрузку
	 *
	 * @api
	 *
	 * @param array $arClasses - массив классов
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loader/method_add_auto_load_classes
	 */
	public static function addAutoLoadClasses (array $arClasses)
	{
		if (!empty($arClasses))
		{
			foreach ($arClasses as $className=>$filename)
			{
				if (!isset(static::$arAutoLoadClasses[$className]) && trim($className)!='')
				{
					static::$arAutoLoadClasses[$className] = $filename;
				}
			}
		}
	}

	/**
	 * Проверяет, существует ли указанный класс в автозагрузке
	 *
	 * @param string $className
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public static function classExists($className)
	{
		return (isset(static::$arAutoLoadClasses[$className]));
	}

	/**
	 * Возвращает номер версии модуля, если она задана, либо false
	 *
	 * @api
	 *
	 * @param string $moduleName Имя модуля
	 *
	 * @return string|bool Строковое значение версии, либо false
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loader/method_get_module_version
	 */
	public static function getModuleVersion ($moduleName)
	{
		if (Modules::checkModuleName($moduleName))
		{
			if (isset(static::$arModules[$moduleName]['INSTALLED_VERSION']['VERSION']))
			{
				return static::$arModules[$moduleName]['INSTALLED_VERSION']['VERSION'];
			}
		}

		return false;
	}

	/**
	 * Возвращает дату версии модуля, если она задана, либо false
	 *
	 * @api
	 *
	 * @param string $moduleName Имя модуля
	 *
	 * @return string|bool Строковое представление даты версии пакета, либо false
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loader/method_get_module_version_date
	 */
	public static function getModuleVersionDate ($moduleName)
	{
		if (Modules::checkModuleName($moduleName))
		{
			if (isset(static::$arModules[$moduleName]['INSTALLED_VERSION']['VERSION_DATE']))
			{
				return static::$arModules[$moduleName]['INSTALLED_VERSION']['VERSION_DATE'];
			}
		}

		return false;
	}

	/**
	 * Возвращает информацию о модуле, если она существует
	 *
	 * @api
	 *
	 * @param string $moduleName
	 *
	 * @return bool
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loader/method_get_module_info
	 */
	public static function getModuleInfo ($moduleName)
	{
		if (
			Modules::checkModuleName($moduleName)
			&& isset(static::$arModules[$moduleName]['INFO'])
		)
		{
			return static::$arModules[$moduleName]['INFO'];
		}

		return false;
	}

	/**
	 * Возвращает данные о версии всех установленных модулей, либо false
	 *
	 * @api
	 *
	 * @return array|bool
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loader/method_get_array_modules_versions
	 */
	public static function getArrayModulesVersions ()
	{
		$arVersions = array();
		foreach (static::$arModules as $package=>$arData)
		{
			if (isset($arData['INSTALLED_VERSION']))
			{
				$arVersions[$package] = $arData['INSTALLED_VERSION'];
			}
		}

		if (!empty($arVersions))
		{
			return $arVersions;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Подключает указанный модуль
	 *
	 * @api
	 *
	 * @param string $nameModule Имя пакета
	 *
	 * @return bool true - если пакет подключен или уже был подключен, иначе false
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loader/method_include_module
	 */
	public static function includeModule ($nameModule=null)
	{
		//Если имя модуля задано, модуль установлен и не был загружен ранее
		if (!is_null($nameModule) && isset(static::$arModules[$nameModule]) && !isset(static::$arIncludedModules[$nameModule]))
		{
			//Существует ли файл зависимостей модулей
			if (file_exists(static::$modulesRoot.'/'.$nameModule."/required.php"))
			{
				//Подключаем файл зависимостей модулей
				$arIncludeModules = include(static::$modulesRoot.'/'.$nameModule."/required.php");
				//Если массив зависимостей не пуст
				if (!empty($arIncludeModules))
				{
					//Если массив обязательных модулей не пуст
					if (!empty($arIncludeModules['required']))
					{
						if (!static::includeAddModules($nameModule,$arIncludeModules['required'],true))
						{
							return false;
						}
					}

					//Если массив дополнительных модулей не пуст
					if (!empty($arIncludeModules['additional']))
					{
						static::includeAddModules($nameModule,$arIncludeModules['additional']);
					}
				}
			}

			//Подключаем основной файл пакета
			include(static::$modulesRoot.'/'.$nameModule."/include.php");
			//Если у пакета есть файл Опций по-умолчанию
			if (file_exists(static::$modulesRoot.$nameModule."/default_options.php"))
			{
				//Сохраняем их
				$arModuleDefaultOptions = include(static::$modulesRoot.'/'.$nameModule."/default_options.php");
				if (isset($arModuleDefaultOptions) && !empty($arModuleDefaultOptions))
				{
					foreach ($arModuleDefaultOptions as $optionName=>$optionValue)
					{
						if (strpos($optionName,$nameModule.'_')===false)
						{
							Options::setDefaultOption($nameModule.'_'.$optionName,$optionValue);
						}
						else
						{
							Options::setDefaultOption($optionName,$optionValue);
						}
					}
				}
			}
			//ставим флаг загрузки модуля
			static::$arIncludedModules[$nameModule] = true;

			return true;
		}
		elseif (isset(static::$arIncludedModules[$nameModule]))
		{
			return true;
		}
		elseif (strtolower($nameModule)=='core')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Подключает зависимые модули
	 *
	 * @param string $nameModule - имя основного модуля
	 * @param array  $arAdd - список зависимостей модуля
	 * @param bool   $required - флаг обязательности модулей
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	private static function includeAddModules ($nameModule, array $arAdd, $required = false)
	{
		//Смотрим все описанные модули
		foreach ($arAdd as $module=>$version)
		{
			//Если указан просто модуль без версии, значит подойдет любая версия этого модуля
			if (is_numeric($module))
			{
				$requiredModule = $version;
				$requiredVersion = null;
			}
			//Иначе нужно проверять также версию
			else
			{
				$requiredModule = $module;
				$version = str_replace('v.','',$version);
				$requiredVersion = str_replace('v','',$version);
			}

			//Если требуемый модуль не установлен и он обязательный, умираем с ошибкой
			if (!isset(static::$arModules[$requiredModule]) && $required)
			{
				die("ERROR-[".$nameModule."]: Необходимо установить обязательный модуль [".$requiredModule."]");
			}

			//Если версия была указана, нужно проверить какая версия установлена
			if (!is_null($requiredVersion))
			{
				//Получаем номер версии обязательного модуля
				$checkVersion = static::getModuleVersion($requiredModule);
				//Если требуемый модуль установлен, но его версия ниже, чем требуется, также умираем с ошибкой
				//(хотя этого и не должно случится)
				if (!Modules::checkVersion($checkVersion,$requiredVersion) && $required)
				{
					die("ERROR-[".$nameModule."]: Для подключения модуля требуется обязательный модуль [".$requiredModule."],"
						." версии v".$requiredVersion.". Однако в данный используется версия v".$checkVersion.". "
						."Необходимо обновить модуль [".$requiredModule."] до требуемой версии!");
				}
			}

			//Если требуемый модуль не был еще подключен
			if (!isset(static::$arIncludedModules[$requiredModule]))
			{
				//Подключаем требуемый модуль
				if (!static::includeModule($requiredModule) && $required)
				{
					//Если подключить модуль не удалось, возвращаем false;
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Проверяет, установлен ли указанный модуль и соответствует ли установленная версия требуемой
	 *
	 * @api
	 *
	 * @param string $nameModule Имя модуля
	 * @param string|null $requiredVersion Требуемая версия
	 *
	 * @return bool TRUE - установлен, FALSE в противном случае
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loader/method_isset_module
	 */
	public static function issetModule ($nameModule, $requiredVersion=null)
	{
		if ($nameModule == 'ms.core') return true;

		if (isset(static::$arModules[$nameModule]))
		{
			if (!is_null($requiredVersion))
			{
				$requiredVersion = str_replace('v.','',$requiredVersion);
				$requiredVersion = str_replace('v','',$requiredVersion);
				$currentVersion = static::getModuleVersion($nameModule);
				if (!Modules::checkVersion($currentVersion,$requiredVersion))
				{
					return false;
				}
			}

			return true;
		}

		return false;
	}
}
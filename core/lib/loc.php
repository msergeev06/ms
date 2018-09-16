<?php
/**
 * Ms\Core\Lib\Loc
 * Локализация ядра и модулей
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

class Loc
{
	/**
	 * @var array Массив сообщений
	 */
	protected static $arMessage;

	/**
	 * @var array Список подключенных файлов
	 */
	protected static $arIncludedFiles = array();

	/**
	 * Подключает файл локализации
	 *
	 * @param string $filename путь к файлу, который требует локализации
	 * @param string $prefix префикс для кодов фраз
	 *
	 * @return bool
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loc/method_include_loc_file
	 */
	public static function includeLocFile ($filename, $prefix='ms_')
	{
		if (!isset(static::$arIncludedFiles[$filename]))
		{
			$fileNameStart = $filename;
//			msDebugNoAdmin($filename);
			static::$arIncludedFiles[$filename]=true;
			$filename = static::prepareLocFile($filename,$prefix);
			if ($filename == $fileNameStart)
			{
				msEchoVar('ERROR: '.$filename.' == '.$fileNameStart);
				return false;
			}
//			msDebugNoAdmin($filename);
			if (!$filename || !file_exists($filename))
			{
				return false;
			}

			$arMess = include($filename);

			if (!empty($arMess))
			{
				foreach ($arMess as $code=>$loc)
				{
					if (strpos($code,$prefix)!==false)
					{
						static::$arMessage[strtoupper($code)] = $loc;
					}
					else
					{
						static::$arMessage[strtoupper($prefix.$code)] = $loc;
					}
				}
			}
			//msDebugNoAdmin(static::$arMessage);
		}
		//msDebugNoAdmin(static::$arIncludedFiles);

		return true;
	}

	/**
	 * Возвращает локализованный текст, заменяя теги указанными значениями
	 *
	 * @api
	 *
	 * @param string $name      Код сообщения
	 * @param array  $arReplace Массив замен вида код_тега=>замена
	 *
	 * @return mixed
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loc/method_get_message
	 */
	public static function getMessage ($name,$arReplace=array())
	{
		if (isset(static::$arMessage[strtoupper($name)]))
		{
			$message = static::$arMessage[strtoupper($name)];
		}
		else
		{
			$message = '['.strtoupper($name).']';
		}
		if (!empty($arReplace))
		{
			foreach ($arReplace as $field=>$value)
			{
				$message = str_replace('#'.$field.'#',$value,$message);
			}
		}

		return (!is_null($message))?$message:'['.strtoupper($name).']';
	}

	/**
	 * Функция обертка для getMessage для модулей.
	 * Собирает код текстового сообщения из префикса, названия модуля и кода сообщения
	 *
	 * @api
	 *
	 * @param string $module      Имя модуля
	 * @param string $name      Код локализованной фразы
	 * @param array  $arReplace Массив замен
	 * @param string $prefix    Префикс
	 *
	 * @return mixed
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loc/method_get_module_message
	 */
	public static function getModuleMessage ($module,$name,$arReplace=array(),$prefix='ms_')
	{
		if ($module != 'core')
		{
			if ($arModule = Modules::parseModuleName($module))
			{
				$prefix = strtolower($arModule['BRAND']).'_';
				$module = strtolower($arModule['MODULE']);
			}
		}

		return self::getMessage($prefix.$module.'_'.$name,$arReplace);
	}

	public static function getCompMess ($fullComponentName,$name,$arReplace=array())
	{
		$prefix = str_replace(':','_',$fullComponentName).'_';

		return self::getMessage($prefix.$name,$arReplace);
	}

	public static function getCoreMessage($name,$arReplace=array ())
	{
		return self::getMessage('ms_core_'.$name,$arReplace);
	}

	/**
	 * Возвращает массив, содержащий все локализованные тексты указанного модуля
	 *
	 * @api
	 *
	 * @param string $name   Имя модуля или '' == 'core'
	 * @param string $prefix Префикс (по-умолчанию 'ms_')
	 *
	 * @return array
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loc/method_show_all_messages_module
	 */
	public static function showAllMessagesModule ($name='',$prefix='ms_')
	{
		if ($name=='') $name='core';
		if ($name!='core')
		{
			if ($arModule = Modules::parseModuleName($name))
			{
				$prefix = strtolower($arModule['BRAND']).'_';
				$name = strtolower($arModule['MODULE']);
			}
		}
		$prefix .= $name.'_';
		$prefix = strtoupper($prefix);

		$arMessages = array();
		$arMess = static::$arMessage;
		foreach ($arMess as $field=>$value)
		{
			if (strstr($field,$prefix) !== false)
			{
				$arMessages[$field]=$value;
			}
		}

		return $arMessages;
	}

	/**
	 * Обрабатывает путь к файлу, требующему локализации
	 *
	 * @param string $filename Путь к файлу, требующему локализацию
	 * @param string &$prefix Префикс, будет установлен исходя из имени модуля
	 *
	 * @return bool|mixed
	 */
	private static function prepareLocFile ($filename, &$prefix=null)
	{
		$newFilename = false;
		$lang = strtolower(Application::getInstance()->getSettings()->getSiteLang());
		if (strpos($filename,'modules')!==false)
		{
			if (preg_match('/modules\/([a-z]{1}[a-z0-9]*)[.]{1}([a-z]{1}[a-z0-9_]*)\//',$filename,$m))
			{
				$prefix = strtolower($m[1]).'_'.strtolower($m[2]).'_';
			}
			$returnValue = preg_replace('/modules\/([a-z]{1}[a-z0-9]*[.]{1}[a-z]{1}[a-z0-9_]*)\//', 'modules/$1/loc/'.$lang.'/', $filename);
			if (!is_null($returnValue))
			{
				$newFilename = $returnValue;
			}
		}
		elseif (strpos($filename,'core')!==false)
		{
			$prefix = 'ms_core_';
			$newFilename = str_replace('core','core/loc/'.$lang,$filename);
		}
		elseif (strpos($filename,'components'))
		{
			$returnValue = null;
			if (preg_match('/components\/([a-z0-9]+)\/([a-z0-9_.]+)\/templates\/([a-z0-9_.]{2,})\//',$filename,$m))
			{
				$prefix = strtolower($m[1]).'_'.strtolower($m[2]).'_';
				$returnValue = preg_replace('/components\/([a-z0-9]+)\/([a-z0-9_.]+)\/templates\/([a-z0-9_.]{2,})\//', 'components/$1/$2/templates/$3/loc/'.$lang.'/', $filename);
			}
			elseif (preg_match('/components\/([a-z0-9]+)\/([a-z0-9_.]+)\//',$filename,$m))
			{
				$prefix = strtolower($m[1]).'_'.strtolower($m[2]).'_';
				$returnValue = preg_replace('/components\/([a-z0-9]+)\/([a-z0-9_.]+)\//', 'components/$1/$2/loc/'.$lang.'/', $filename);
			}
			if (!is_null($returnValue))
			{
				$newFilename = $returnValue;
			}
		}

		return $newFilename;
	}

	/**
	 * Возвращает весь массив загруженных фраз
	 *
	 * @return array
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/loc/method_get_ar_mess
	 */
	public static function getArMess()
	{
		return static::$arMessage;
	}
}
<?php
/**
 * Функции обертки основного функционала системы
 *
 * @package Ms\Core
 * @author Mikhail Sergeev
 * @copyright 2018 Mikhail Sergeev
 */

if (!function_exists('IncludeLocFile'))
{
	function IncludeLocFile ($path)
	{
		return \Ms\Core\Lib\Loc::includeLocFile($path);
	}
}

if (!function_exists('GetMessage'))
{
	function GetMessage ($name, $arReplace = array ())
	{
		return \Ms\Core\Lib\Loc::getMessage($name, $arReplace);
	}
}

if (!function_exists('GetModuleMessage'))
{
	function GetModuleMessage ($module, $name, $arReplace = array ())
	{
		return \Ms\Core\Lib\Loc::getModuleMessage($module,$name,$arReplace);
	}
}

if (!function_exists('IssetModule'))
{
	function IssetModule ($nameModule, $requiredVersion = null)
	{
		return \Ms\Core\Lib\Loader::issetModule($nameModule, $requiredVersion);
	}
}

if (!function_exists('IncludeModule'))
{
	function IncludeModule ($nameModule)
	{
		return \Ms\Core\Lib\Loader::includeModule($nameModule);
	}
}

if (!function_exists('Write2DebugLog'))
{
	function Write2DebugLog ($strMessage, $arReplace=array (),&$errorCollection)
	{
		\Ms\Core\Lib\Logs::setDebug($strMessage,$arReplace,$errorCollection);
	}
}

if (!function_exists('Write2InfoLog'))
{
	function Write2InfoLog ($strMessage, $arReplace=array (),&$errorCollection)
	{
		\Ms\Core\Lib\Logs::setInfo($strMessage,$arReplace,$errorCollection);
	}
}

if (!function_exists('Write2NoticeLog'))
{
	function Write2NoticeLog ($strMessage, $arReplace=array (),&$errorCollection)
	{
		\Ms\Core\Lib\Logs::setNotice($strMessage,$arReplace,$errorCollection);
	}
}

if (!function_exists('Write2WarningLog'))
{
	function Write2WarningLog ($strMessage, $arReplace=array (),&$errorCollection)
	{
		\Ms\Core\Lib\Logs::setWarning($strMessage,$arReplace,$errorCollection);
	}
}

if (!function_exists('Write2ErrorLog'))
{
	function Write2ErrorLog ($strMessage, $arReplace=array (),&$errorCollection)
	{
		\Ms\Core\Lib\Logs::setError($strMessage,$arReplace,$errorCollection);
	}
}

if (!function_exists('Write2CriticalLog'))
{
	function Write2CriticalLog ($strMessage, $arReplace=array (),&$errorCollection)
	{
		\Ms\Core\Lib\Logs::setCritical($strMessage,$arReplace,$errorCollection);
	}
}

if (!function_exists('ms_sessid'))
{
	function ms_sessid ()
	{
		return \Ms\Core\Entity\Application::getInstance()->getSession()->getSID();
	}
}


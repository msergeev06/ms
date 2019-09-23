<?php

namespace Ms\Core\Lib;

use Ms\Core\Entity\Application;
use Ms\Core\Entity\User;
use Ms\Core\Tables\UserGroupAccessTable;

class Access
{
	const LEVEL_MODULE_VIEW = "V"; //Работать с модулем

	const LEVEL_MODULE_EDIT = "E"; //Редактировать настройки модуля

	const LEVEL_MODULE_SETUP = "S"; //Устанавливать/удалять/обновлять модуль

	const LEVEL_MODULE_ALL = "A"; //Все действия с модулем

	/*	public static function canViewPersonal ($userID = null)
		{
			$arUserGroups = array ();
			if (self::can($userID,$arUserGroups))
			{
				return true;
			}
			else
			{
				//TODO: Доделать проверку прав групп
			}
		}*/

	/**
	 * Проверяет является ли указанный пользователь системным пользователем
	 *
	 * @param null $userID
	 *
	 * @return bool
	 */
	public static function isSystemUser ($userID=null)
	{
		self::normalizeUserID($userID);
		if ($userID == Users::SYSTEM_USER)
		{
			return TRUE;
		}

		return FALSE;
	}

	public static function can (&$userID=null, &$arUserGroups=null)
	{
		if (defined('NOT_CHECK_PERM') && NOT_CHECK_PERM === true)
		{
			return TRUE;
		}
		self::normalizeUserID($userID);
		if ($userID == Users::SYSTEM_USER || self::isSystemUser($userID))
		{
			return true;
		}
		else
		{
			$arUserGroups = self::getUserGroupsList($userID);
			if (in_array(Users::ADMIN_GROUP,$arUserGroups))
			{
				return true;
			}
		}

		return false;
	}

	public static function canView ($sModuleName, $userID=null, &$arUserGroups=null)
	{
		return static::canLevel ($sModuleName, $userID, $arUserGroups);
	}

	public static function canEdit ($sModuleName, $userID=null, &$arUserGroups=null)
	{
		$arAccessCodes = [
			static::LEVEL_MODULE_EDIT,
			static::LEVEL_MODULE_SETUP,
			static::LEVEL_MODULE_ALL
		];

		return static::canLevel ($sModuleName, $userID, $arUserGroups, $arAccessCodes);
	}

	public static function canSetup ($sModuleName, $userID=null, &$arUserGroups=null)
	{
		$arAccessCodes = [
			static::LEVEL_MODULE_SETUP,
			static::LEVEL_MODULE_ALL
		];

		return static::canLevel ($sModuleName, $userID, $arUserGroups, $arAccessCodes);
	}

	private static function canLevel ($sModuleName, $userID=null, &$arUserGroups=[], $arAccessCodes=null)
	{
		if (is_null($arAccessCodes))
		{
			$arAccessCodes = [
				static::LEVEL_MODULE_VIEW,
				static::LEVEL_MODULE_EDIT,
				static::LEVEL_MODULE_SETUP,
				static::LEVEL_MODULE_ALL
			];
		}

		if (static::can($userID, $arUserGroups))
		{
			return true;
		}

		if (!Modules::checkModuleName($sModuleName))
		{
			return false;
		}

		$arRes = UserGroupAccessTable::getList([
			'select' => ['ID'],
			'filter' => [
				'MODULE_NAME' => $sModuleName,
				'USER_GROUP_ID' => $arUserGroups,
				'ACCESS_CODE' => $arAccessCodes
			]
		]);

		return !!$arRes;
	}

	private static function normalizeUserID (&$userID = null)
	{
		$userID = Tools::normalizeUserID($userID);
	}

	private static function getUserGroupsList ($userID = null)
	{
		self::normalizeUserID($userID);
		$arUserGroups = Users::getGroups($userID);
		if (!$arUserGroups || (is_array($arUserGroups) && count($arUserGroups)==0))
		{
			return false;
		}

		$arReturn = array ();
		foreach ($arUserGroups as $arGroup)
		{
			$arReturn[] = $arGroup['GROUP_ID'];
		}

		return $arReturn;
	}
}
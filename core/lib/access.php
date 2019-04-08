<?php

namespace Ms\Core\Lib;

use Ms\Core\Entity\Application;
use Ms\Core\Entity\User;

class Access
{
	const LEVEL_MODULE_SETUP = "S";

	const LEVEL_MODULE_VIEW = "V";

	const LEVEL_MODULE_EDIT = "E";

	const LEVEL_MODULE_ALL = "A";

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
		if ($userID == Users::SYSTEM_USER)
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

	private static function normalizeUserID (&$userID = null)
	{
		if (is_null($userID))
		{
			$userID = Application::getInstance()->getUser()->getID();
		}
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
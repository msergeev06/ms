<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules;

use Ms\Core\Api\ApiAdapter;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Lib\Tools;
use Ms\Core\Entity\User\UserController;
use Ms\Core\Tables\UserGroupAccessTable;

/**
 * Класс Ms\Core\Entity\Modules\Access
 * Права доступа пользователей к модулям
 */
class Access extends Multiton
{
	const LEVEL_MODULE_VIEW = "V"; //Работать с модулем

	const LEVEL_MODULE_EDIT = "E"; //Редактировать настройки модуля

	const LEVEL_MODULE_SETUP = "S"; //Устанавливать/удалять/обновлять модуль

	const LEVEL_MODULE_ALL = "A"; //Все действия с модулем

	/*	public function canViewPersonal ($userID = null)
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
	public function isSystemUser ($userID=null)
	{
		self::normalizeUserID($userID);
		if ($userID == UserController::SYSTEM_USER)
		{
			return TRUE;
		}

		return FALSE;
	}

	public function can (int &$userID=null, array &$arUserGroups=null)
	{
		if (defined('NOT_CHECK_PERM') && NOT_CHECK_PERM === true)
		{
			return TRUE;
		}

		self::normalizeUserID($userID);
		if ($userID == UserController::SYSTEM_USER || $this->isSystemUser($userID))
		{
			return true;
		}
		else
		{
			$arUserGroups = $this->getUserGroupsList($userID);
			if (in_array(UserController::ADMIN_GROUP, $arUserGroups))
			{
				return true;
			}
		}

		return false;
	}

	public function canView ($sModuleName, $userID=null, &$arUserGroups=null)
	{
		return $this->canLevel ($sModuleName, $userID, $arUserGroups);
	}

	public function canEdit ($sModuleName, $userID=null, &$arUserGroups=null)
	{
		$arAccessCodes = [
			static::LEVEL_MODULE_EDIT,
			static::LEVEL_MODULE_SETUP,
			static::LEVEL_MODULE_ALL
		];

		return $this->canLevel ($sModuleName, $userID, $arUserGroups, $arAccessCodes);
	}

	public function canSetup ($sModuleName, $userID=null, &$arUserGroups=null)
	{
		$arAccessCodes = [
			static::LEVEL_MODULE_SETUP,
			static::LEVEL_MODULE_ALL
		];

		return $this->canLevel ($sModuleName, $userID, $arUserGroups, $arAccessCodes);
	}

	private function getOrm ()
    {
        return ApiAdapter::getInstance()->getDbApi()->getTableOrmByClass(UserGroupAccessTable::class);
    }

	private function canLevel ($sModuleName, $userID=null, &$arUserGroups=[], $arAccessCodes=null)
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

		if ($this->can($userID, $arUserGroups))
		{
			return true;
		}

		if (!Modules::getInstance()->checkModuleName($sModuleName))
		{
			return false;
		}

        try
        {
            $arRes = $this->getOrm()->getList(
                [
                    'select' => ['ID'],
                    'filter' => [
                        'MODULE_NAME'   => $sModuleName,
                        'USER_GROUP_ID' => $arUserGroups,
                        'ACCESS_CODE'   => $arAccessCodes
                    ]
                ]
            );
        }
        catch (SystemException $e)
        {
            return false;
        }

        return !!$arRes;
	}

    /**
     * @param null|int &$userID
     */
    private function normalizeUserID (&$userID = null)
	{
		$userID = Tools::normalizeUserID($userID);
	}

    /**
     * @param null|int $userID
     *
     * @return array|bool
     */
    private function getUserGroupsList ($userID = null)
	{
		$this->normalizeUserID($userID);
		$arUserGroups = UserController::getInstance()->getGroups($userID);
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
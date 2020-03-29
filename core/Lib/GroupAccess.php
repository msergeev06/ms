<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Tables\UserGroupModulesAccessTable;

/**
 * Класс Ms\Core\Lib\GroupAccess
 * Управление доступом для групп пользователей
 */
class GroupAccess
{
	private static $_arModuleGroupAccess = [];

	/**
     * Возвращает существующие доступы для указанного модуля, типа доступа и
     * одной или нескольких групп
     * Полученные данные сохраняет для текущей сессии, чтобы не запрашивать
     * вновь из базы
	 *
	 * @param string    $sModuleName Имя модуля
	 * @param string    $sAccessName Тип доступа
	 * @param int|array $mGroupID    ID или массив с ID групп пользователей
	 *
	 * @return array|bool
	 */
	public static function getAccess ($sModuleName, $sAccessName, $mGroupID)
	{
		$sModuleName = strtolower($sModuleName);
		if (
			!Modules::checkModuleName($sModuleName)
			|| strlen($sAccessName) <= 0
		) {
			return FALSE;
		}

		if (is_array($mGroupID))
		{
			$tmp = [];
			foreach ($mGroupID as $groupID)
			{
				if ((int)$groupID!=0)
				{
					$tmp[] = (int)$groupID;
				}
			}
			if (empty($tmp))
			{
				return FALSE;
			}
			$mGroupID = $tmp;
			unset($tmp);
		}
		else
		{
			if ((int)$mGroupID<=0)
			{
				return FALSE;
			}
			else
			{
				$mGroupID = [$mGroupID];
			}
		}
		$arReturn = [];
		$arGroupSearch = [];

		foreach ($mGroupID as $groupID)
		{
			if (isset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$groupID]))
			{
				$arReturn[$groupID] = self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$groupID];
			}
			else
			{
				$arGroupSearch[] = $groupID;
			}
		}

		if (empty($arGroupSearch))
		{
			return $arReturn;
		}

		$arRes = UserGroupModulesAccessTable::getList([
			'select' => ['GROUP_ID','ACCESS_CODE'],
			'filter' => [
				'MODULE_NAME' => $sModuleName,
				'ACCESS_NAME' => $sAccessName,
				'GROUP_ID'    => $arGroupSearch
			]
		]);
		if (!$arRes)
		{
			return $arReturn;
		}

		foreach ($arRes as $ar_res)
		{
			$arReturn[$ar_res['GROUP_ID']] = $ar_res['ACCESS_CODE'];
			self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$ar_res['GROUP_ID']]
                = $ar_res['ACCESS_CODE'];
		}

		return $arReturn;
	}

	public static function getMultiAccess ($sModuleName, $arAccessName, $mGroupID)
	{
		$sModuleName = strtolower($sModuleName);
		if (
			!Modules::checkModuleName($sModuleName)
		) {
			return FALSE;
		}

		if (is_array($mGroupID))
		{
			$tmp = [];
			foreach ($mGroupID as $groupID)
			{
				if ((int)$groupID!=0)
				{
					$tmp[] = (int)$groupID;
				}
			}
			if (empty($tmp))
			{
				return FALSE;
			}
			$mGroupID = $tmp;
			unset($tmp);
		}
		else
		{
			if ((int)$mGroupID<=0)
			{
				return FALSE;
			}
			else
			{
				$mGroupID = [$mGroupID];
			}
		}
		$arReturn = [];
		$arGroupSearch = [];

		foreach ($mGroupID as $groupID)
		{
			foreach ($arAccessName as $sAccessName)
			{
				if (isset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$groupID]))
				{
					$arReturn[$groupID] = [
					    $sAccessName=>self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$groupID]
                    ];
				}
				elseif (!in_array($groupID,$arGroupSearch))
				{
					$arGroupSearch[] = $groupID;
				}
			}
		}

		if (empty($arGroupSearch))
		{
			return $arReturn;
		}

		$arRes = UserGroupModulesAccessTable::getList([
			'select' => ['GROUP_ID','ACCESS_NAME','ACCESS_CODE'],
			'filter' => [
				'MODULE_NAME' => $sModuleName,
				'ACCESS_NAME' => $arAccessName,
				'GROUP_ID'    => $arGroupSearch
			]
		]);
		if (!$arRes)
		{
			return $arReturn;
		}

		foreach ($arRes as $ar_res)
		{
			$arReturn[$ar_res['GROUP_ID']] = [
			    $ar_res['ACCESS_NAME']=>$ar_res['ACCESS_CODE']
            ];
			self::$_arModuleGroupAccess[$sModuleName][$ar_res['ACCESS_NAME']][$ar_res['GROUP_ID']]
                = $ar_res['ACCESS_CODE'];
		}

		return $arReturn;
	}

	/**
	 * Добавляет коды доступа для указанного модуля, типа доступа, группы
	 * Очищает сохраненные данные для указанной группы
	 *
	 * @param string            $sModuleName Имя модуля
	 * @param string            $sAccessName Код доступа
	 * @param int               $iGroupID    ID группы пользователей
	 * @param null|string|array $mAccessCode Код или массив кодов доступа, либо NULL
	 *
	 * @return bool TRUE, в случае успешного добавления кодов доступа и FALSE в противном случае
	 */
	public static function addAccess (
	    $sModuleName,
        $sAccessName,
        $iGroupID,
        $mAccessCode=null
    ) {
		if (!self::normalizeParams(
		    $sModuleName,
            $sAccessName,
            $iGroupID,
            $mAccessCode)
        ) {
			return FALSE;
		}
		$arRes = UserGroupModulesAccessTable::getOne(array(
			'filter' => array(
				'MODULE_NAME'=>$sModuleName,
				'ACCESS_NAME'=>$sAccessName,
				'GROUP_ID'=>$iGroupID
			)
		));
		if ($arRes)
		{
			$arUpdate = array ();
			if (!empty($arRes['ACCESS_CODE']) && !is_null($mAccessCode))
			{
				$arUpdate['ACCESS_CODE'] = array_merge($arRes['ACCESS_CODE'],$mAccessCode);
				$arUpdate['ACCESS_CODE'] = array_unique($arUpdate['ACCESS_CODE']);
			}
			elseif (!is_null($mAccessCode))
			{
				$arUpdate['ACCESS_CODE'] = $mAccessCode;
			}
			if (!empty($arUpdate))
			{
				$res = UserGroupModulesAccessTable::update($arRes['ID'],$arUpdate);
				if ($res->getResult())
				{
					if (isset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
					{
						unset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
					}
					return TRUE;
				}
			}

			return FALSE;
		}
		else
		{
			$arAdd = array (
				'MODULE_NAME' => $sModuleName,
				'ACCESS_NAME' => $sAccessName,
				'GROUP_ID'    => $iGroupID
			);
			if (!is_null($mAccessCode))
			{
				$arAdd['ACCESS_CODE'] = $mAccessCode;
			}
			$res = UserGroupModulesAccessTable::add($arAdd);
			if ($res->getResult())
			{
				if (isset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
				{
					unset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
				}
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
	}

	/**
	 * Очищает все установленные коды доступа для указанного модуля, типа доступа и группы
	 * Очищает сохраненные данные для указанной группы
	 *
	 * @param string $sModuleName Имя модуля
	 * @param string $sAccessName Тип доступа
	 * @param int    $iGroupID    ID группы пользователей
	 *
	 * @return bool TRUE, если доступы очищены или были пусты, FALSE в противном случае
	 */
	public static function clearAccess ($sModuleName, $sAccessName, $iGroupID)
	{
		if (!self::normalizeParams($sModuleName, $sAccessName, $iGroupID))
		{
			return FALSE;
		}
		$arRes = UserGroupModulesAccessTable::getOne(
			array(
				'select' => ['ID','ACCESS_CODE'],
				'filter' => [
					'MODULE_NAME' => $sModuleName,
					'ACCESS_NAME' => $sAccessName,
					'GROUP_ID'    => $iGroupID
				]
			)
		);
		if (!$arRes || (isset($arRes['ACCESS_CODE']) && empty($arRes['ACCESS_CODE'])))
		{
			if (isset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
			{
				unset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
			}
			return TRUE;
		}
		else
		{
			$res = UserGroupModulesAccessTable::update($arRes['ID'],['ACCESS_CODE'=>NULL]);
			if ($res->getResult())
			{
				if (isset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
				{
					unset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
				}
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
	}

	/**
	 * Удаляет код доступа (или несколько) для указанного модуля, типа доступа и группы пользователей
	 * Очищает сохраненные данные для указанной группы
	 *
	 * @param string            $sModuleName Имя модуля
	 * @param string            $sAccessName Тип доступа
	 * @param string            $iGroupID    ID группы пользователей
	 * @param null|string|array $mAccessCode Удаляемый код (или коды) доступа, либо NULL
	 *
	 * @return bool
	 */
	public static function delAccess ($sModuleName, $sAccessName, $iGroupID, $mAccessCode=null)
	{
		if (!self::normalizeParams($sModuleName, $sAccessName, $iGroupID, $mAccessCode))
		{
			return FALSE;
		}

		$arRes = UserGroupModulesAccessTable::getOne(
			array (
				'select' => ['ID','ACCESS_CODE'],
				'filter' => [
					'MODULE_NAME' => $sModuleName,
					'ACCESS_NAME' => $sAccessName,
					'GROUP_ID'    => $iGroupID
				]
			)
		);
		if (!$arRes || (isset($arRes['ACCESS_CODE']) && empty($arRes['ACCESS_CODE'])))
		{
			if (isset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
			{
				unset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
			}
			return TRUE;
		}
		else
		{
			$arRes['ACCESS_CODE'] = array_diff($arRes['ACCESS_CODE'],$mAccessCode);
			$res = UserGroupModulesAccessTable::update($arRes['ID'],['ACCESS_CODE'=>$arRes['ACCESS_CODE']]);
			if ($res->getResult())
			{
				if (isset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
				{
					unset(self::$_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
				}
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
	}

	/**
	 * Подготавливает параметры для использования, если параметры не подходят, возвращает FALSE
	 *
	 * @param string            &$sModuleName Имя модуля
	 * @param string            &$sAccessName Тип доступа
	 * @param int               &$iGroupID    ID группы пользователей
	 * @param null|string|array &$mAccessCode Код (или коды) доступа, либо NULL
	 *
	 * @return bool
	 */
	private static function normalizeParams (&$sModuleName, &$sAccessName, &$iGroupID, &$mAccessCode=null)
	{
		$sModuleName = strtolower($sModuleName);
		$sAccessName = strtoupper($sAccessName);
		$iGroupID = (int)$iGroupID;
		if (
			!Modules::checkModuleName($sModuleName)
			|| strlen($sAccessName) <= 0
			|| $iGroupID <= 0
		) {
			return FALSE;
		}
		if (!is_null($mAccessCode))
		{
			if (is_array($mAccessCode))
			{
				foreach ($mAccessCode as &$code)
				{
					$code = strtoupper($code);
				}
				unset($code);
			}
			else
			{
				$mAccessCode = array(strtoupper($mAccessCode));
			}
		}

		return TRUE;
	}
}
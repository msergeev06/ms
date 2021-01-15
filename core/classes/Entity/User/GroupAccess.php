<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Entity\User;

use Ms\Core\Api\ApiAdapter;
use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Lib\Modules;

/**
 * Класс Ms\Core\Lib\GroupAccess
 * Управление доступом для групп пользователей
 */
class GroupAccess extends Multiton
{
    private $_arModuleGroupAccess = [];

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
    public function getAccess (string $sModuleName, string $sAccessName, $mGroupID)
    {
        $sModuleName = strtolower($sModuleName);
        if (
            !Modules::checkModuleName($sModuleName)
            || strlen($sAccessName) <= 0
        )
        {
            return false;
        }

        if (is_array($mGroupID))
        {
            $tmp = [];
            foreach ($mGroupID as $groupID)
            {
                if ((int)$groupID != 0)
                {
                    $tmp[] = (int)$groupID;
                }
            }
            if (empty($tmp))
            {
                return false;
            }
            $mGroupID = $tmp;
            unset($tmp);
        }
        else
        {
            if ((int)$mGroupID <= 0)
            {
                return false;
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
            if (isset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$groupID]))
            {
                $arReturn[$groupID] = $this->_arModuleGroupAccess[$sModuleName][$sAccessName][$groupID];
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

        try
        {
            $arRes = $this->getOrm()->getList(
                [
                    'select' => ['GROUP_ID', 'ACCESS_CODE'],
                    'filter' => [
                        'MODULE_NAME' => $sModuleName,
                        'ACCESS_NAME' => $sAccessName,
                        'GROUP_ID'    => $arGroupSearch
                    ]
                ]
            );
        }
        catch (SystemException $e)
        {
            $arRes = false;
        }
        if (!$arRes)
        {
            return $arReturn;
        }

        foreach ($arRes as $ar_res)
        {
            $arReturn[$ar_res['GROUP_ID']] = $ar_res['ACCESS_CODE'];
            $this->_arModuleGroupAccess[$sModuleName][$sAccessName][$ar_res['GROUP_ID']]
                = $ar_res['ACCESS_CODE'];
        }

        return $arReturn;
    }

    /**
     * <Описание>
     *
     * @param string $sModuleName  Имя модуля
     * @param array  $arAccessName Массив со списком кодов доступов
     * @param mixed  $mGroupID     ID группы, либо массив ID групп
     *
     * @return array|bool
     */
    public function getMultiAccess (string $sModuleName, array $arAccessName, $mGroupID)
    {
        $sModuleName = strtolower($sModuleName);
        if (!Modules::checkModuleName($sModuleName))
        {
            return false;
        }

        if (!is_array($mGroupID))
        {
            $mGroupID = [$mGroupID];
        }

        $tmp = [];
        foreach ($mGroupID as $groupID)
        {
            if ((int)$groupID != 0)
            {
                $tmp[] = (int)$groupID;
            }
        }
        if (empty($tmp))
        {
            return false;
        }
        $mGroupID = $tmp;
        unset($tmp);
        $arReturn = [];
        $arGroupSearch = [];

        foreach ($mGroupID as $groupID)
        {
            foreach ($arAccessName as $sAccessName)
            {
                if (isset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$groupID]))
                {
                    $arReturn[$groupID] = [
                        $sAccessName => $this->_arModuleGroupAccess[$sModuleName][$sAccessName][$groupID]
                    ];
                }
                elseif (!in_array($groupID, $arGroupSearch))
                {
                    $arGroupSearch[] = $groupID;
                }
            }
        }

        if (empty($arGroupSearch))
        {
            return $arReturn;
        }

        try
        {
            $arRes = $this->getOrm()->getList(
                [
                    'select' => ['GROUP_ID', 'ACCESS_NAME', 'ACCESS_CODE'],
                    'filter' => [
                        'MODULE_NAME' => $sModuleName,
                        'ACCESS_NAME' => $arAccessName,
                        'GROUP_ID'    => $arGroupSearch
                    ]
                ]
            );
        }
        catch (SystemException $e)
        {
            $arRes = false;
        }
        if (!$arRes)
        {
            return $arReturn;
        }

        foreach ($arRes as $ar_res)
        {
            $arReturn[$ar_res['GROUP_ID']] = [
                $ar_res['ACCESS_NAME'] => $ar_res['ACCESS_CODE']
            ];
            $this->_arModuleGroupAccess[$sModuleName][$ar_res['ACCESS_NAME']][$ar_res['GROUP_ID']]
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
    public function addAccess (
        string $sModuleName,
        string $sAccessName,
        int $iGroupID,
        $mAccessCode = null
    ) {
        if (!self::normalizeParams($sModuleName,$sAccessName,$iGroupID,$mAccessCode))
        {
            return false;
        }
        try
        {
            $arRes = $this->getOrm()->getOne(
                [
                    'filter' => [
                        'MODULE_NAME' => $sModuleName,
                        'ACCESS_NAME' => $sAccessName,
                        'GROUP_ID'    => $iGroupID
                    ]
                ]
            );
        }
        catch (SystemException $e)
        {
            $arRes = false;
        }
        if ($arRes)
        {
            $arUpdate = [];
            if (!empty($arRes['ACCESS_CODE']) && !is_null($mAccessCode))
            {
                $arUpdate['ACCESS_CODE'] = array_merge($arRes['ACCESS_CODE'], $mAccessCode);
                $arUpdate['ACCESS_CODE'] = array_unique($arUpdate['ACCESS_CODE']);
            }
            elseif (!is_null($mAccessCode))
            {
                $arUpdate['ACCESS_CODE'] = $mAccessCode;
            }
            if (!empty($arUpdate))
            {
                try
                {
                    $res = $this->getOrm()->update($arRes['ID'], $arUpdate);
                }
                catch (SystemException $e)
                {
                    $res = new DBResult();
                }
                if ($res->isSuccess())
                {
                    if (isset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
                    {
                        unset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
                    }

                    return true;
                }
            }

            return false;
        }
        else
        {
            $arAdd = [
                'MODULE_NAME' => $sModuleName,
                'ACCESS_NAME' => $sAccessName,
                'GROUP_ID'    => $iGroupID
            ];
            if (!is_null($mAccessCode))
            {
                $arAdd['ACCESS_CODE'] = $mAccessCode;
            }
            try
            {
                $res = $this->getOrm()->insert($arAdd);
            }
            catch (SystemException $e)
            {
                $res = new DBResult();
            }
            if ($res->isSuccess())
            {
                if (isset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
                {
                    unset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
                }

                return true;
            }
            else
            {
                return false;
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
    public function clearAccess (string $sModuleName, string $sAccessName, int $iGroupID)
    {
        if (!self::normalizeParams($sModuleName, $sAccessName, $iGroupID))
        {
            return false;
        }
        try
        {
            $arRes = $this->getOrm()->getOne(
                [
                    'select' => ['ID', 'ACCESS_CODE'],
                    'filter' => [
                        'MODULE_NAME' => $sModuleName,
                        'ACCESS_NAME' => $sAccessName,
                        'GROUP_ID'    => $iGroupID
                    ]
                ]
            );
        }
        catch (SystemException $e)
        {
            $arRes = false;
        }
        if (!$arRes || (isset($arRes['ACCESS_CODE']) && empty($arRes['ACCESS_CODE'])))
        {
            if (isset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
            {
                unset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
            }

            return true;
        }
        else
        {
            try
            {
                $res = $this->getOrm()->update($arRes['ID'], ['ACCESS_CODE' => null]);
            }
            catch (SystemException $e)
            {
                $res = new DBResult();
            }
            if ($res->isSuccess())
            {
                if (isset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
                {
                    unset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
                }

                return true;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Удаляет код доступа (или несколько) для указанного модуля, типа доступа и группы пользователей
     * Очищает сохраненные данные для указанной группы
     *
     * @param string            $sModuleName Имя модуля
     * @param string            $sAccessName Тип доступа
     * @param int               $iGroupID    ID группы пользователей
     * @param null|string|array $mAccessCode Удаляемый код (или коды) доступа, либо NULL
     *
     * @return bool
     */
    public function delAccess (string $sModuleName, string $sAccessName, int $iGroupID, $mAccessCode = null)
    {
        if (!self::normalizeParams($sModuleName, $sAccessName, $iGroupID, $mAccessCode))
        {
            return false;
        }

        try
        {
            $arRes = $this->getOrm()->getOne(
                [
                    'select' => ['ID', 'ACCESS_CODE'],
                    'filter' => [
                        'MODULE_NAME' => $sModuleName,
                        'ACCESS_NAME' => $sAccessName,
                        'GROUP_ID'    => $iGroupID
                    ]
                ]
            );
        }
        catch (SystemException $e)
        {
            $arRes = false;
        }
        if (!$arRes || (isset($arRes['ACCESS_CODE']) && empty($arRes['ACCESS_CODE'])))
        {
            if (isset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
            {
                unset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
            }

            return true;
        }
        else
        {
            $arRes['ACCESS_CODE'] = array_diff($arRes['ACCESS_CODE'], $mAccessCode);
            try
            {
                $res = $this->getOrm()->update($arRes['ID'], ['ACCESS_CODE' => $arRes['ACCESS_CODE']]);
            }
            catch (SystemException $e)
            {
                $res = new DBResult();
            }
            if ($res->isSuccess())
            {
                if (isset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]))
                {
                    unset($this->_arModuleGroupAccess[$sModuleName][$sAccessName][$iGroupID]);
                }

                return true;
            }
            else
            {
                return false;
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
    private function normalizeParams (string &$sModuleName, string &$sAccessName, int &$iGroupID, &$mAccessCode = null)
    {
        $sModuleName = strtolower($sModuleName);
        $sAccessName = strtoupper($sAccessName);
        $iGroupID = (int)$iGroupID;
        if (
            !Modules::checkModuleName($sModuleName)
            || strlen($sAccessName) <= 0
            || $iGroupID <= 0
        )
        {
            return false;
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
                $mAccessCode = [strtoupper($mAccessCode)];
            }
        }

        return true;
    }

    /**
     * @return ORMController
     */
    private static function getOrm ()
    {
        return ApiAdapter::getInstance()
                         ->getDbApi()
                         ->getTableOrmByClass(\Ms\Core\Tables\UserGroupModulesAccessTable::class);
    }
}
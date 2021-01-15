<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\User;

use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\Db\Query;
use Ms\Core\Entity\System\Cookie;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Tables;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Modules\Modules;

/**
 * Класс Ms\Core\Lib\UserController
 * Обработка данных пользователей
 */
class UserController extends Multiton
{
    /**
     * ID системного пользователя
     */
    const SYSTEM_USER = 0;

    /**
     * ID пользователя admin
     */
    const ADMIN_USER = 1;

    /**
     * ID пользователя guest
     */
    const GUEST_USER = 2;

    /**
     * ID группы Администраторы
     */
    const ADMIN_GROUP = 1;

    /**
     * ID группы Все пользователи
     */
    const ALL_GROUP = 2;

    /**
     * Время в секундах, на которое сохраняется сессия пользователя
     */
    const REMEMBER_TIME = 31536000; //365 дней

    /**
     * Присваивает всем группам пользователей все перечисленные права для модуля
     *
     * @param string       $sModuleName Имя модуля
     * @param array|int    $mGroup      Группа или массив групп пользователей
     * @param array|string $mAccess     Доступ или массив доступов, назначаемых всем группам
     *
     * @return bool
     */
    public function addAccess ($sModuleName, $mGroup = [], $mAccess = [])
    {
        if (!Modules::getInstance()->checkModuleName($sModuleName))
        {
            return false;
        }
        $arAdd = [];

        //Приводим групу и код доступа к массивам, если ими они не являются
        if (!is_array($mGroup))
        {
            $mGroup = [$mGroup];
        }
        if (!is_array($mAccess))
        {
            $mAccess = [$mAccess];
        }

        //Составляем множество возможных доступов из полученных параметров
        foreach ($mGroup as $groupID)
        {
            foreach ($mAccess as $accessCode)
            {
                $arAdd[] = [
                    'MODULE_NAME'   => $sModuleName,
                    'USER_GROUP_ID' => $groupID,
                    'ACCESS_CODE'   => $accessCode
                ];
            }
        }

        //Получаем существующие записи с правами для модуля и групп
        try
        {
            $arRes = $this->getOrmUserGroupAccessTable()->getList(
                [
                    'select' => ['USER_GROUP_ID', 'ACCESS_CODE'],
                    'filter' => [
                        'MODULE_NAME'   => $sModuleName,
                        'USER_GROUP_ID' => $mGroup
                    ]
                ]
            )
            ;
        }
        catch (SystemException $e)
        {
            return false;
        }
        //Удаляем из списка добавления записи, существующие в БД
        if ($arRes && !empty($arRes))
        {
            for ($i = 0; $i < count($arAdd); $i++)
            {
                foreach ($arRes as $ar_res)
                {
                    if (
                        $arAdd[$i]['USER_GROUP_ID'] == $ar_res['USER_GROUP_ID']
                        && $arAdd[$i]['ACCESS_CODE'] == $ar_res['ACCESS_CODE']
                    )
                    {
                        unset($arAdd[$i]);
                        break;
                    }
                }
            }
        }

        //Если есть что добавлять, добавляем
        if (!empty($arAdd))
        {
            try
            {
                $this->getOrmUserGroupAccessTable()->insert($arAdd);
            }
            catch (SystemException $e)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Проверяет наличие функций password_hash и password_verify
     *
     * @return bool
     */
    public function isPasswordHash ()
    {
        return (function_exists('password_hash') && function_exists('password_verify'));
    }

    /**
     * Создает HASH пароля
     *
     * @param string $login
     * @param string $pass
     *
     * @return bool|string
     */
    public function createMd5Pass (string $login, string $pass)
    {
        if ($this->isPasswordHash())
        {
            $passHash = password_hash($pass, PASSWORD_BCRYPT);
        }
        else
        {
            $str = 'msergeev|' . $login . '|' . $pass;
            $passHash = md5(md5(trim($str)));
        }

        return $passHash;
    }

    /**
     * Создает нового пользователя
     *
     * @param       $arData
     * @param array $err
     *
     * @return bool
     */
    public function createNewUser ($arData, &$err = [])
    {
        //TODO:Добавить все проверки
        if (!isset($arData['LOGIN']))
        {
            $err['NOT_ISSET_LOGIN'] = 'Логин не указан';

            return false;
        }

        if (!preg_match("/^[a-zA-Z0-9]+$/", $arData['LOGIN']))
        {
            $err['LOGIN_LETTER'] = "Логин может состоять только из букв английского алфавита и цифр";

            return false;
        }

        if (strlen($arData['LOGIN']) < 3 || strlen($arData['LOGIN']) > 255)
        {
            $err['LOGIN_LENGTH'] = "Логин должен быть не меньше 3-х символов и не больше 255";

            return false;
        }

        try
        {
            $arRes = self::getOrmUsersTable()->getOne(
                [
                    'select' => ['ID'],
                    'filter' => [
                        'LOGIN' => $arData['LOGIN']
                    ]
                ]
            )
            ;
        }
        catch (SystemException $e)
        {
            $err['DB_ERROR'] = "Ошибка запроса БД";

            return false;
        }
        if ($arRes)
        {
            $err['LOGIN_ISSET_DB'] = "Пользователь с таким логином уже существует.";

            return false;
        }


        $arAdd = [
            'LOGIN'    => $arData['LOGIN'],
            'PASSWORD' => $this->createMd5Pass($arData['LOGIN'], $arData['PASSWORD']),
            'EMAIL'    => $arData['EMAIL']
        ];

        if (isset($arData['MOBILE']))
        {
            $arAdd['MOBILE'] = $arData['MOBILE'];
        }
        if (isset($arData['NAME']))
        {
            $arAdd['NAME'] = $arData['NAME'];
        }
        if (isset($arData['FIO_F']))
        {
            $arAdd['FIO_F'] = $arData['FIO_F'];
        }
        if (isset($arData['FIO_I']))
        {
            $arAdd['FIO_I'] = $arData['FIO_I'];
        }
        if (isset($arData['FIO_O']))
        {
            $arAdd['FIO_O'] = $arData['FIO_O'];
        }

        try
        {
            $res = self::getOrmUsersTable()->insert($arAdd);
            if ($res->isSuccess())
            {
                $res->getInsertId();
            }

            return false;
        }
        catch (SystemException $e)
        {
            return false;
        }
    }

    /**
     * Удаляет указанные доступы для групп пользователей, если они существуют
     *
     * @param string       $sModuleName Имя модуля
     * @param array|int    $mGroups     Группа или массив групп пользователей
     * @param array|string $mAccess     Код или массив кодов доступа
     *
     * @return bool
     */
    public function deleteAccess ($sModuleName, $mGroups = [], $mAccess = [])
    {
        //Проверяем правильность имени модуля
        if (!Modules::getInstance()->checkModuleName($sModuleName))
        {
            return false;
        }
        $arDelete = [];

        //Правращаем группы и коды доступа в массивы, если они ими не являются
        if (!is_array($mGroups))
        {
            $mGroups = [$mGroups];
        }
        if (!is_array($mAccess))
        {
            $mAccess = [$mAccess];
        }

        //Получаем существующие записи о доступах
        try
        {
            $arRes = $this->getOrmUserGroupAccessTable()->getList(
                [
                    'filter' => [
                        'MODULE_NAME'   => $sModuleName,
                        'USER_GROUP_ID' => $mGroups
                    ]
                ]
            )
            ;
        }
        catch (SystemException $e)
        {
            return false;
        }
        //Составляем массив ID существующих записей, которые требуется удалить
        if ($arRes && !empty($arRes))
        {
            foreach ($mGroups as $groupID)
            {
                foreach ($mAccess as $accessCode)
                {
                    foreach ($arRes as $ar_res)
                    {
                        if (
                            $ar_res['USER_GROUP_ID'] == $groupID
                            && $ar_res['ACCESS_CODE'] == $accessCode
                        )
                        {
                            $arDelete[] = $ar_res['ID'];
                            break;
                        }
                    }
                }
            }
        }
        if (!empty($arDelete))
        {
            foreach ($arDelete as $deleteID)
            {
                try
                {
                    $this->getOrmUserGroupAccessTable()->delete($deleteID);
                }
                catch (SystemException $e)
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Возвращает параметры авторизованного пользователя
     *
     * @param array $arParams
     *
     * @return array
     */
    public function getAuthUserParams ($arParams = [])
    {
        $USER = Application::getInstance()->getUser();

        return self::getUserParams($USER->getID(), $arParams);
    }

    /**
     * Возвращает аватар указанного или текущего
     *
     * @param null|int $userID ID пользователя
     *
     * @return mixed|null
     */
    public function getAvatar ($userID = null)
    {
        //TODO: Доделать получение аватара пользователя
        $arRes = $this->getFields($userID, ['AVATAR']);
        if ($arRes && isset($arRes['AVATAR']))
        {
            return $arRes['AVATAR'];
        }

        return null;
    }

    /**
     * Возвращает указанные поля указанного или текущего пользователя
     *
     * @param null|int $userID   ID пользователя
     * @param array    $arFields Массив необходимых полей
     *
     * @return array|false
     */
    public function getFields ($userID = null, $arFields = [])
    {
        if (is_null($userID))
        {
            $USER = Application::getInstance()->getUser();
            $userID = $USER->getID();
        }

        try
        {
            $arRes = self::getOrmUsersTable()->getById(
                $userID,
                $arFields
            )
            ;
        }
        catch (\Exception $e)
        {
            return false;
        }

        return $arRes;
    }

    /**
     * Возвращает массив групп, в которых состоит указанный или текущий пользователь.
     *
     * @param null|int $userID ID пользователя
     *
     * @return array|bool Массив групп, либо false
     */
    public function getGroups ($userID = null)
    {
        if (is_null($userID))
        {
            $USER = Application::getInstance()->getUser();
            $userID = $USER->getID();
        }

        try
        {
            $now = new Date();
            $arRes = self::getOrmUserToGroupTable()->getList(
                [
                    'select' => [
                        'GROUP_ID',
                        'GROUP_ID.ACTIVE' => 'GROUP_ACTIVE',
                        'GROUP_ID.NAME'   => 'GROUP_NAME',
                        'GROUP_ID.CODE'   => 'GROUP_CODE'
                    ],
                    'filter' => [
                        'USER_ID' => $userID,
                        [
                            'LOGIC'         => 'OR',
                            'ACTIVE_FROM'   => null,
                            '>=ACTIVE_FROM' => $now
                        ],
                        [
                            'LOGIC'       => 'OR',
                            'ACTIVE_TO'   => null,
                            '<=ACTIVE_TO' => $now
                        ]
                    ]
                ]
            )
            ;
        }
        catch (SystemException $e)
        {
            return false;
        }
        if ($arRes && !empty($arRes))
        {
            foreach ($arRes as $k => $v)
            {
                if (!$v['GROUP_ACTIVE'])
                {
                    unset($arRes[$k]);
                }
            }
        }

        return ($arRes && !empty($arRes)) ? $arRes : false;
    }

    /**
     * Возвращает логин указанного или текущего пользователя, либо NULL
     *
     * @param null|int $userID ID пользователя
     *
     * @return string|null
     */
    public function getLogin ($userID = null)
    {
        $arRes = $this->getFields($userID, ['LOGIN']);
        if ($arRes && isset($arRes['LOGIN']))
        {
            return $arRes['LOGIN'];
        }

        return null;
    }

    /**
     * Возвращает псевдоним указанного или текущего пользователя
     *
     * @param null|int $userID ID пользователя
     *
     * @return mixed|null
     */
    public function getName ($userID = null)
    {
        $arRes = $this->getFields($userID, ['NAME']);
        if ($arRes && isset($arRes['NAME']))
        {
            return $arRes['NAME'];
        }

        return null;
    }

    /**
     * Возвращает значение cookie пользователя
     *
     * @param string $cookieName Имя cookie
     * @param string $userID     ID пользователя
     *
     * @return null|string
     */
    public function getUserCookie ($cookieName, $userID = null)
    {
        $cookie = Application::getInstance()->getCookieController();
        if (is_null($userID))
        {
            $userID = Application::getInstance()->getUser()->getID();
        }
        $cookieName = strtolower($cookieName);
        $cookieName = str_replace($cookie->getPrefix(), '', $cookieName);

        if ($cookie->isset($cookieName . '_user_' . $userID))
        {
            return $cookie->getCookie($cookieName . '_user_' . $userID);
        }
        else
        {
            return null;
        }
    }

    /**
     * Возвращает значение параметра пользователя, если оно задано, либо возвращает значение по-умолчанию, если оно
     * задано
     *
     * @param string $sOptionName   Имя необходимого параметра
     * @param int    $iUserID       ID пользователя, если null - текущий пользователь
     * @param mixed  $mDefaultValue Значение по-умолчанию, возвращается, если другое значение не было найдено
     *
     * @return null|mixed
     */
    public function getUserOption ($sOptionName, $iUserID = null, $mDefaultValue = null)
    {
        if (is_null($iUserID))
        {
            $iUserID = Application::getInstance()->getUser()->getID();
        }
        try
        {
            $arRes = self::getOrmUserOptionsTable()->getOne(
                [
                    'select' => ['VALUE'],
                    'filter' => ['USER_ID' => $iUserID, 'NAME' => strtoupper($sOptionName)]
                ]
            )
            ;
        }
        catch (SystemException $e)
        {
            return null;
        }
        if ($arRes && isset($arRes['VALUE']))
        {
            return $arRes['VALUE'];
        }
        elseif (!$arRes && !is_null($mDefaultValue))
        {
            return $mDefaultValue;
        }
        else
        {
            return null;
        }
    }

    public function getUserParams ($userID, $arParams = [])
    {
        $arSelect = [];
        $arProperties = [];
        $arProps = [];
        $arReturn = [];
        $userID = intval($userID);
        if (!empty($arParams))
        {
            $usersTableMap = self::getOrmUsersTable()->getMap();
            foreach ($arParams as $parameter)
            {
                $parameter = strtoupper($parameter);
                if ($parameter == 'ID')
                {
                    continue;
                }
                if (preg_match('/PROPERTY_(.*)/', $parameter, $match))
                {
                    if (isset($match[1]))
                    {
                        $arProperties[] = $match[1];
                    }
                }
                elseif ($usersTableMap->isExists($parameter))
                {
                    $arSelect[] = $parameter;
                }
            }
        }

        $arList = [
            'filter' => [
                'ID' => $userID
            ]
        ];
        if (!empty($arSelect))
        {
            $arList['select'] = $arSelect;
        }

        //Получаем данные из таблицы пользователей
        try
        {
            $arRes = self::getOrmUsersTable()->getOne($arList);
        }
        catch (SystemException $e)
        {
            $arRes = false;
        }
        if ($arRes)
        {
            foreach ($arRes as $key => $value)
            {
                $arReturn[$key] = $value;
            }
        }

        if (!empty($arProperties))
        {
            $sqlHelper = new SqlHelper(self::getOrmUsersPropertiesTable()->getTableName());
            $sql = "SELECT\n\t"
                   . $sqlHelper->wrapFieldQuotes('ID') . ",\n\t"
                   . $sqlHelper->wrapFieldQuotes('PROPERTY_NAME') . ",\n\t"
                   . $sqlHelper->wrapFieldQuotes('PROPERTY_VALUE') . "\nFROM\n\t"
                   . $sqlHelper->wrapTableQuotes() . "\nWHERE\n\t"
                   . $sqlHelper->wrapFieldQuotes('USER_ID') . " = " . $userID . " AND\n\t"
                   . $sqlHelper->wrapFieldQuotes('PROPERTY_NAME') . " IN (";
            $bFirst = true;
            foreach ($arProperties as $prop)
            {
                if (!$bFirst)
                {
                    $sql .= ', ';
                }
                else
                {
                    $bFirst = false;
                }
                $sql .= "'$prop'";
            }
            $sql .= ")";
            $query = new Query\QueryBase($sql);
            try
            {
                $res = $query->exec();
            }
            catch (SqlQueryException $e)
            {
                $res = new DBResult();
            }
            if ($res->isSuccess())
            {
                while ($ar_res = $res->fetch())
                {
                    $name = $ar_res['PROPERTY_NAME'];
                    $arProps['PROPERTY']['PROPERTY_' . $name . '_ID'] = $ar_res['ID'];
                    $arProps['PROPERTY']['PROPERTY_' . $name . '_VALUE'] = $ar_res['PROPERTY_VALUE'];
                }
            }
        }
        if (!empty($arProps))
        {
            $arReturn = array_merge($arReturn, $arProps);
        }

        return $arReturn;
    }

    /**
     * Возвращает TRUE, если указанный (или текущий) пользователь является администратором, FALSE в противном случае
     *
     * @param null|int $userID ID пользователя
     *
     * @return bool
     */
    public function isAdmin ($userID = null)
    {
        if (is_null($userID))
        {
            $userID = Application::getInstance()->getUser()->getID();
        }
        try
        {
            $now = new Date();
        }
        catch (SystemException $e)
        {
            return false;
        }

        if ($userID == self::ADMIN_USER)
        {
            return true;
        }
        else
        {
            try
            {
                $arRes = self::getOrmUserToGroupTable()->getOne(
                    [
                        'select' => 'ID',
                        'filter' => [
                            'USER_ID'  => $userID,
                            'GROUP_ID' => self::ADMIN_GROUP,
                            [
                                'LOGIC'         => 'OR',
                                'ACTIVE_FROM'   => null,
                                '>=ACTIVE_FROM' => $now
                            ],
                            [
                                'LOGIC'       => 'OR',
                                'ACTIVE_TO'   => null,
                                '<=ACTIVE_TO' => $now
                            ]
                        ]
                    ]
                )
                ;
            }
            catch (SystemException $e)
            {
                return false;
            }

            return !!($arRes);
        }
    }

    /**
     * Возвращает true либо false на основе принадлежности пользователя к группам
     * Если используется логика 'or', вернет true, если пользователь состоит хотя бы в одной из групп
     * Если используется логика 'and', вернет true только если пользователь состоит во всех перечисленных группах
     * Если используется поле ID - ожидается массив ID групп
     * Если используется поле CODE - ожидается массив кодов групп
     *
     * @param null|int $userID   ID пользователя, если null|false - текущий
     * @param array    $arGroups Массив групп для проверки, может содержать ID групп или их коды
     *                           в зависимости от используемого типа поля field
     * @param string   $logic    Логика поиска 'or' или 'and'
     * @param string   $field    Поле ID или CODE
     *
     * @return bool
     */
    public function isInGroups (int $userID = null, $arGroups = [], $logic = 'or', $field = 'ID')
    {
        if (!$userID || is_null($userID))
        {
            $userID = Application::getInstance()->getUser()->getID();
        }

        if (strtolower($logic) != 'or' && strtolower($logic) != 'and')
        {
            $logic = 'or';
        }
        else
        {
            $logic = strtolower($logic);
        }
        if (strtoupper($field) != 'ID' && strtoupper($field) != 'CODE')
        {
            $field = 'ID';
        }
        else
        {
            $field = strtoupper($field);
        }
        $userGroups = $this->getGroups($userID);
        if (!$userGroups || empty($userGroups))
        {
            return false;
        }
        $isset = null;
        foreach ($userGroups as $ar_group)
        {
            if (in_array($ar_group['GROUP_' . $field], $arGroups))
            {
                if (is_null($isset) || $logic == 'or')
                {
                    $isset = true;
                }
            }
            else
            {
                if (is_null($isset) || $logic == 'and')
                {
                    $isset = false;
                }
            }
        }
        if (is_null($isset))
        {
            $isset = false;
        }

        return $isset;
    }

    public function isOnline (int $userID = null)
    {
        //TODO: Доделать проверку активности пользователя
        if (is_null($userID))
        {
            $USER = Application::getInstance()->getUser();

            return $USER->isAuthorize();
        }

        return false;
    }

    /**
     * Проверяет существование пользователя с заданным ID
     *
     * @param int $iUserID ID пользователя
     *
     * @return bool Если пользователь существует, возвращает true, иначе false
     */
    public function isset ($iUserID)
    {
        if ((int)$iUserID <= 0)
        {
            return false;
        }

        try
        {
            return !!self::getOrmUsersTable()->getById($iUserID, ['ID']);
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    /**
     * Возвращает true, есди указанный или текущий пользователь имеет указанные права доступа
     *
     * @param string|array $mAccess    Код или коды доступа
     * @param string       $moduleName Имя модуля
     * @param int|null     $userID     ID пользователя
     *
     * @return bool
     */
    public function issetAccess ($mAccess, $moduleName = 'core', $userID = null)
    {
        if (is_null($userID))
        {
            $userID = Application::getInstance()->getUser()->getID();
        }

        if ($this->isAdmin($userID))
        {
            return true;
        }

        $arFilter = [
            'ACCESS_CODE' => $mAccess,
            'MODULE_NAME' => $moduleName
        ];
        try
        {
            $arRes = $this->getOrmUserGroupAccessTable()->getList(
                [
                    'select' => ['USER_GROUP_ID'],
                    'filter' => $arFilter
                ]
            )
            ;
        }
        catch (SystemException $e)
        {
            return false;
        }
        $arGroups = [];
        if ($arRes && !empty($arRes))
        {
            foreach ($arRes as $arAccess)
            {
                if (!in_array($arAccess['USER_GROUP_ID'], $arGroups))
                {
                    $arGroups[] = $arAccess['USER_GROUP_ID'];
                }
            }
        }

        if (!empty($arGroups))
        {
            return $this->isInGroups($userID, $arGroups);
        }

        return false;
    }

    /**
     * Возвращает true, если указанный cookie пользователя существует, false в противном случае
     *
     * @param string $cookieName Имя cookie
     * @param int    $userID     ID пользователя
     *
     * @return bool
     */
    public function issetUserCookie ($cookieName, $userID = null)
    {
        $cookie = Application::getInstance()->getCookieController();
        if (is_null($userID))
        {
            $userID = Application::getInstance()->getUser()->getID();
        }
        $cookieName = strtolower($cookieName);
        $cookieName = str_replace($cookie->getPrefix(), '', $cookieName);

        return $cookie->isset($cookieName . '_user_' . $userID);
    }

    /**
     * Пытается авторизовать пользователя по Логину и Паролю
     *
     * @param      $login
     * @param      $pass
     * @param bool $remember
     *
     * @return bool
     */
    public function logIn ($login, $pass, $remember = false)
    {
        try
        {
            $arRes = self::getOrmUsersTable()->getOne(
                [
                    'select' => ['ID', 'PASSWORD', 'HASH'],
                    'filter' => [
                        'LOGIN'  => $login,
                        'ACTIVE' => true
                    ]
                ]
            )
            ;
            // msDebugNoAdmin($arRes);
        }
        catch (SystemException $e)
        {
            return false;
        }
        $authorizer = Application::getInstance()->getAuthorizer();

        if ($arRes)
        {
            if ($this->isHashVerify())
            {
                // msDebugNoAdmin($this->isHashVerify());
                if (password_verify($pass, $arRes['PASSWORD']))
                {
                    // msDebugNoAdmin('pass - pass');
                    return $authorizer->logIn($arRes['ID'], $remember);
                }
                elseif (password_verify($pass, $arRes['HASH']))
                {
                    // msDebugNoAdmin('pass - hash');
                    return $authorizer->logIn($arRes['ID'], $remember);
                }
                elseif ($arRes['PASSWORD'] == $this->createMd5Pass($login, $pass))
                {
                    // msDebugNoAdmin('pass - md5');
                    return $authorizer->logIn($arRes['ID'], $remember);
                }
            }
            elseif ($arRes['PASSWORD'] == $this->createMd5Pass($login, $pass))
            {
                // msDebugNoAdmin('pass - md5');
                return $authorizer->logIn($arRes['ID'], $remember);
            }
        }

        return false;
    }

    /**
     * Возвращает TRUE, если существуют функции password_hash и password_verify
     *
     * @return bool
     */
    public function isHashVerify ()
    {
        return (function_exists('password_hash') && function_exists('password_verify'));
    }

    public function logOut ()
    {
        $auth = Application::getInstance()->getAuthorizer();

        $auth->logOut();
    }

    public function setAccess ($sModuleName, array $arAccess)
    {
        if (!is_array($arAccess) || empty($arAccess) || strlen($sModuleName) <= 0)
        {
            return false;
        }

        foreach ($arAccess as $iUserGroup => $ar_access)
        {
            if (!empty($ar_access))
            {
                $arAdd = [];
                $arDelete = [];
                foreach ($ar_access as $code => $action)
                {
                    if ($action)
                    {
                        $arAdd[] = $code;
                    }
                    else
                    {
                        $arDelete[] = $code;
                    }
                }
                if (!empty($arAdd))
                {
                    $this->addAccess($sModuleName, $iUserGroup, $arAdd);
                }
                if (!empty($arDelete))
                {
                    $this->deleteAccess($sModuleName, $iUserGroup, $arDelete);
                }
            }
        }

        return true;
    }

    /**
     * Сохраняет cookie пользователя
     *
     * @param string $cookieName Имя cookie
     * @param string $value      Значение cookie
     * @param int    $userID     ID пользователя
     *
     * @return bool
     */
    public function setUserCookie ($cookieName, $value, $userID = null)
    {
        $cookie = Application::getInstance()->getCookieController();
        if (is_null($userID))
        {
            $userID = Application::getInstance()->getUser()->getID();
        }
        $cookieName = strtolower($cookieName);

        $cookieName = str_replace($cookie->getPrefix(), '', $cookieName);

        return $cookie->setCookie(
            (new Cookie($cookieName . '_user_' . $userID, $value))
                ->setExpires((time() + self::REMEMBER_TIME))
                ->setPath('/')
        );
    }

    /**
     * Добавляет или обновляет значение параметра пользователя, возвращая ID записи, либо false
     *
     * @param string $sOptionName Имя необходимого параметра (приводится к верхнему регистру)
     * @param mixed  $mValue      Новое значение параметра
     * @param int    $iUserID     ID пользователя, если null - текущий пользователь
     *
     * @return bool|int
     */
    public function setUserOption ($sOptionName, $mValue, $iUserID = null)
    {
        if (is_null($iUserID))
        {
            $iUserID = Application::getInstance()->getUser()->getID();
        }
        try
        {
            $arRes = self::getOrmUserOptionsTable()->getOne(
                [
                    'filter' => ['USER_ID' => (int)$iUserID, 'NAME' => strtoupper($sOptionName)]
                ]
            )
            ;
        }
        catch (SystemException $e)
        {
            return false;
        }
        if ($arRes && $arRes['VALUE'] != $mValue)
        {
            try
            {
                $resUpdate = self::getOrmUserOptionsTable()->update(
                    $arRes['ID'],
                    ['VALUE' => $mValue]
                )
                ;
            }
            catch (SystemException $e)
            {
                $resUpdate = new DBResult();
            }
            if ($resUpdate->isSuccess())
            {
                return $arRes['ID'];
            }
            else
            {
                return false;
            }
        }
        elseif ($arRes && $arRes['VALUE'] == $mValue)
        {
            return $arRes['ID'];
        }
        elseif (!$arRes)
        {
            try
            {
                $resAdd = self::getOrmUserOptionsTable()->insert(
                    [
                        'USER_ID' => (int)$iUserID,
                        'NAME'    => strtoupper($sOptionName),
                        'VALUE'   => $mValue
                    ]
                )
                ;
            }
            catch (SystemException $e)
            {
                $resAdd = new DBResult();
            }
            if ($resAdd->isSuccess())
            {
                return $resAdd->getInsertId();
            }
            else
            {
                return false;
            }
        }

        return false;
    }

    /**
     * Устанавливает параметры пользователя
     *
     * @param       $userID
     * @param array $arParams
     *
     */
    public function setUserParams ($userID, array $arParams)
    {
        $userID = intval($userID);
        if (isset($arParams) && !empty($arParams) && $userID > 0)
        {
            $usersTableMap = self::getOrmUsersTable()->getMap();
            $arUpdate = [];
            $arUpdateProp = [];
            foreach ($arParams as $key => $value)
            {
                if ($key == 'ID')
                {
                    continue;
                }
                if (preg_match('/PROPERTY_(.*)/', $key, $match))
                {
                    if (isset($match[1]))
                    {
                        $arUpdateProp[$match[1]] = $value;
                    }
                }
                elseif ($usersTableMap->isExists($key))
                {
                    $arUpdate[$key] = $value;
                }
            }

            if (!empty($arUpdate))
            {
                try
                {
                    self::getOrmUsersTable()->update($userID, $arUpdate);
                }
                catch (SystemException $e)
                {
                }
            }

            if (!empty($arUpdateProp))
            {
                foreach ($arUpdateProp as $key => $value)
                {
                    try
                    {
                        $arRes = self::getOrmUsersPropertiesTable()->getOne(
                            [
                                'select' => ['ID'],
                                'filter' => [
                                    'USER_ID'       => $userID,
                                    'PROPERTY_NAME' => $key
                                ]
                            ]
                        )
                        ;
                    }
                    catch (SystemException $e)
                    {
                        $arRes = false;
                    }

                    if ($arRes)
                    {
                        try
                        {
                            self::getOrmUsersPropertiesTable()->update(
                                $arRes['ID'],
                                ['PROPERTY_VALUE' => $value]
                            )
                            ;
                        }
                        catch (SystemException $e)
                        {
                        }
                    }
                }
            }
        }
    }

    public function setUserProperty ($userID, array $arProperty)
    {
        $userID = intval($userID);
        foreach ($arProperty as $key => $value)
        {
            if (preg_match('/PROPERTY_(.*)/', $key, $match))
            {
                if (isset($match[1]))
                {
                    try
                    {
                        $arRes = self::getOrmUsersPropertiesTable()->getOne(
                            [
                                'select' => ['ID'],
                                'filter' => [
                                    'USER_ID'       => $userID,
                                    'PROPERTY_NAME' => $match[1]
                                ]
                            ]
                        )
                        ;
                        if ($arRes)
                        {
                            self::getOrmUsersPropertiesTable()->update(
                                $arRes['ID'],
                                ['PROPERTY_VALUE' => $value]
                            )
                            ;
                        }
                        else
                        {
                            self::getOrmUsersPropertiesTable()->insert(
                                [
                                    "USER_ID"        => $userID,
                                    'PROPERTY_NAME'  => $match[1],
                                    'PROPERTY_VALUE' => $value
                                ]
                            )
                            ;
                        }
                    }
                    catch (SystemException $e)
                    {
                    }
                }
            }
        }
    }

    private function getOrm (TableAbstract $table)
    {
        return ORMController::getInstance($table);
    }

    private function getOrmUserGroupAccessTable ()
    {
        return self::getOrm(new Tables\UserGroupAccessTable());
    }

    private function getOrmUserOptionsTable ()
    {
        return self::getOrm(new Tables\UserOptionsTable());
    }

    private function getOrmUserToGroupTable ()
    {
        return self::getOrm(new Tables\UserToGroupTable());
    }

    private function getOrmUsersPropertiesTable ()
    {
        return self::getOrm(new Tables\UsersPropertiesTable());
    }

    //TODO: Подумать над удалением доступов при удалении групп пользователей

    private function getOrmUsersTable ()
    {
        return self::getOrm(new Tables\UsersTable());
    }
}
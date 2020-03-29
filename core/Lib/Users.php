<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\Db\Query;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Tables;
use Ms\Core\Entity\Db\SqlHelper;

/**
 * Класс Ms\Core\Lib\Users
 * Обработка данных пользователей
 */
class Users
{
	/**
	 * ID системного пользователя
	 * @since 0.3.0
	 */
	const SYSTEM_USER = 0;

	/**
	 * ID пользователя admin
	 * @since 0.2.0
	 */
	const ADMIN_USER = 1;

	/**
	 * ID пользователя guest
	 * @since 0.2.0
	 */
	const GUEST_USER = 2;

	/**
	 * ID группы Администраторы
	 * @since 0.2.0
	 */
	const ADMIN_GROUP = 1;

	/**
	 * ID группы Все пользователи
	 * @since 0.3.0
	 */
	const ALL_GROUP = 2;

	/**
	 * Время в секундах, на которое сохраняется сессия пользователя
	 * @since 0.2.0
	 */
	const REMEMBER_TIME = 31536000; //365 дней

	//<editor-fold defaultstate="collapse" desc=">>>Получение данных о пользователях<<<">
	/**
	 * Возвращает TRUE, если указанный (или текущий) пользователь является администратором, FALSE в противном случае
	 *
	 * @param null|int $userID ID пользователя
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public static function isAdmin ($userID=null)
	{
		if (is_null($userID))
		{
			$USER = Application::getInstance()->getUser();
			$userID = $USER->getID();
		}
		$now = new Date();

		if ($userID == self::ADMIN_USER)
		{
			return true;
		}
		else
		{
			$arRes = Tables\UserToGroupTable::getOne(
				array (
					'select' => 'ID',
					'filter' => array (
						'USER_ID' => $userID,
						'GROUP_ID' => self::ADMIN_GROUP,
						array (
							'LOGIC'=>'OR',
							'ACTIVE_FROM'=>NULL,
							'>=ACTIVE_FROM' => $now
						),
						array (
							'LOGIC' => 'OR',
							'ACTIVE_TO' => NULL,
							'<=ACTIVE_TO' => $now
						)
					)
				)
			);
			return !!($arRes);
		}
	}

	/**
	 * Возвращает массив групп, в которых состоит указанный или текущий пользователь.
	 *
	 * @param null|int $userID ID пользователя
	 *
	 * @return array|bool Массив групп, либо false
	 * @since 0.2.0
	 */
	public static function getGroups ($userID=null)
	{
		if (is_null($userID))
		{
			$USER = Application::getInstance()->getUser();
			$userID = $USER->getID();
		}

		$now = new Date();
		$arRes = Tables\UserToGroupTable::getList(
			array (
				'select' => array(
					'GROUP_ID',
					'GROUP_ID.ACTIVE' => 'GROUP_ACTIVE',
					'GROUP_ID.NAME' => 'GROUP_NAME',
					'GROUP_ID.CODE' => 'GROUP_CODE'
				),
				'filter' => array (
					'USER_ID' => $userID,
					array (
						'LOGIC' => 'OR',
						'ACTIVE_FROM' => NULL,
						'>=ACTIVE_FROM' => $now
					),
					array (
						'LOGIC' => 'OR',
						'ACTIVE_TO' => NULL,
						'<=ACTIVE_TO' => $now
					)
				)
			)
		);
		if ($arRes && !empty($arRes))
		{
			foreach ($arRes as $k=>$v)
			{
				if (!$v['GROUP_ACTIVE'])
				{
					unset($arRes[$k]);
				}
			}
		}

		return ($arRes && !empty($arRes))?$arRes:false;
	}

	/**
	 * Возвращает true либо false на основе принадлежности пользователя к группам
	 * Если используется логика 'or', вернет true, если пользователь состоит хотя бы в одной из групп
	 * Если используется логика 'and', вернет true только если пользователь состоит во всех перечисленных группах
	 * Если используется поле ID - ожидается массив ID групп
	 * Если используется поле CODE - ожидается массив кодов групп
	 *
	 * @param null|int $userID ID пользователя, если null|false - текущий
	 * @param array $arGroups Массив групп для проверки, может содержать ID групп или их коды
	 *                        в зависимости от используемого типа поля field
	 * @param string $logic   Логика поиска 'or' или 'and'
	 * @param string $field   Поле ID или CODE
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public static function isInGroups ($userID=null, $arGroups = array (), $logic = 'or', $field = 'ID')
	{
		if (!$userID || is_null($userID))
		{
			$USER = Application::getInstance()->getUser();
			$userID = $USER->getID();
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
		$userGroups = static::getGroups($userID);
		if (!$userGroups || empty($userGroups))
		{
			return false;
		}
		$isset = null;
		foreach ($userGroups as $ar_group)
		{
			if (in_array($ar_group['GROUP_'.$field],$arGroups))
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

	/**
	 * Возвращает логин указанного или текущего пользователя, либо NULL
	 *
	 * @param null|int $userID ID пользователя
	 *
	 * @return string|null
	 * @since 0.2.0
	 */
	public static function getLogin ($userID=null)
	{
		$arRes = static::getFields($userID, ['LOGIN']);
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
	 * @since 0.2.0
	 */
	public static function getName ($userID=null)
	{
		$arRes = static::getFields($userID, ['NAME']);
		if ($arRes && isset($arRes['NAME']))
		{
			return $arRes['NAME'];
		}

		return null;
	}

	/**
	 * Возвращает аватар указанного или текущего
	 *
	 * @param null|int $userID ID пользователя
	 *
	 * @return mixed|null
	 * @since 0.2.0
	 */
	public static function getAvatar ($userID=null)
	{
		//TODO: Доделать получение аватара пользователя
		$arRes = static::getFields($userID, ['AVATAR']);
		if ($arRes && isset($arRes['AVATAR']))
		{
			return $arRes['AVATAR'];
		}

		return null;
	}

	/**
	 * Возвращает указанные поля указанного или текущего пользователя
	 *
	 * @param null|int  $userID ID пользователя
	 * @param array $arFields Массив необходимых полей
	 *
	 * @return array
	 * @since 0.2.0
	 */
	public static function getFields ($userID=null, $arFields=array())
	{
		if (is_null($userID))
		{
			$USER = Application::getInstance()->getUser();
			$userID = $USER->getID();
		}

		$arRes = Tables\UsersTable::getById(
			$userID,
			$arFields
		);

		return $arRes;
	}

	public static function getAuthUserParams ($arParams = array())
	{
		$USER = Application::getInstance()->getUser();

		return self::getUserParams($USER->getID(),$arParams);
	}

	public static function getUserParams ($userID, $arParams = array())
	{
		$arSelect = array();
		$arProperties = array();
		$arProps = array();
		$arReturn = array();
		$userID = intval($userID);
		if (!empty($arParams))
		{
			$arMapArray = Tables\UsersTable::getMapArray();
			foreach ($arParams as $parameter)
			{
				$parameter = strtoupper($parameter);
				if ($parameter == 'ID')
				{
					continue;
				}
				if (preg_match('/PROPERTY_(.*)/',$parameter,$match))
				{
					if (isset($match[1]))
					{
						$arProperties[] = $match[1];
					}
				}
				elseif (isset($arMapArray[$parameter]))
				{
					$arSelect[] = $parameter;
				}
			}
		}

		$arList = array(
			'filter' => array(
				'ID' => $userID
			)
		);
		if (!empty($arSelect))
		{
			$arList['select'] = $arSelect;
		}

		//Получаем данные из таблицы пользователей
		$arRes = Tables\UsersTable::getOne($arList);
		if ($arRes)
		{
			foreach ($arRes as $key=>$value)
			{
				$arReturn[$key] = $value;
			}

		}

		if (!empty($arProperties))
		{
			$sqlHelper = new SqlHelper(Tables\UsersPropertiesTable::getTableName());
			$sql = "SELECT\n\t"
				.$sqlHelper->wrapFieldQuotes('ID').",\n\t"
				.$sqlHelper->wrapFieldQuotes('PROPERTY_NAME').",\n\t"
				.$sqlHelper->wrapFieldQuotes('PROPERTY_VALUE')."\nFROM\n\t"
				.$sqlHelper->wrapTableQuotes()."\nWHERE\n\t"
				.$sqlHelper->wrapFieldQuotes('USER_ID')." = ".$userID." AND\n\t"
				.$sqlHelper->wrapFieldQuotes('PROPERTY_NAME')." IN (";
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
			$res = $query->exec();
			if ($res->getResult())
			{

				while($ar_res = $res->fetch())
				{
					$name = $ar_res['PROPERTY_NAME'];
					$arProps['PROPERTY']['PROPERTY_'.$name.'_ID'] = $ar_res['ID'];
					$arProps['PROPERTY']['PROPERTY_'.$name.'_VALUE'] = $ar_res['PROPERTY_VALUE'];
				}
			}
		}
		if (!empty($arProps))
		{
			$arReturn = array_merge($arReturn,$arProps);
		}

		return $arReturn;
	}

	public static function setUserParams ($userID, array $arParams)
	{
		$userID = intval($userID);
		if (isset($arParams) && !empty($arParams) && $userID > 0)
		{
			$arMapArray = Tables\UsersTable::getMapArray();
			$arUpdate = array();
			$arUpdateProp = array();
			foreach ($arParams as $key=>$value)
			{
				if ($key == 'ID')
				{
					continue;
				}
				if (preg_match('/PROPERTY_(.*)/',$key,$match))
				{
					if (isset($match[1]))
					{
						$arUpdateProp[$match[1]] = $value;
					}
				}
				elseif (isset($arMapArray[$key]))
				{
					$arUpdate[$key] = $value;
				}
			}

			if (!empty($arUpdate))
			{
				Tables\UsersTable::update($userID,$arUpdate);
			}

			if (!empty($arUpdateProp))
			{
				foreach ($arUpdateProp as $key=>$value)
				{
					$arRes = Tables\UsersPropertiesTable::getOne(
						array(
							'select' => array('ID'),
							'filter' => array(
								'USER_ID' => $userID,
								'PROPERTY_NAME' => $key
							)
						)
					);

					if ($arRes)
					{
						Tables\UsersPropertiesTable::update(
							$arRes['ID'],
							array('PROPERTY_VALUE' => $value)
						);
					}
				}
			}
		}
	}

	public static function setUserProperty ($userID, array $arProperty)
	{
		$userID = intval($userID);
		foreach ($arProperty as $key=>$value)
		{
			if (preg_match('/PROPERTY_(.*)/',$key,$match))
			{
				if (isset($match[1]))
				{
					$arRes = Tables\UsersPropertiesTable::getOne(
						array(
							'select' => array('ID'),
							'filter' => array(
								'USER_ID' => $userID,
								'PROPERTY_NAME' => $match[1]
							)
						)
					);

					if ($arRes)
					{
						Tables\UsersPropertiesTable::update(
							$arRes['ID'],
							array('PROPERTY_VALUE' => $value)
						);
					}
					else
					{
						Tables\UsersPropertiesTable::add(
							array(
								"USER_ID" => $userID,
								'PROPERTY_NAME' => $match[1],
								'PROPERTY_VALUE' => $value
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Возвращает значение параметра пользователя, если оно задано, либо возвращает значение по-умолчанию, если оно задано
	 *
	 * @param string $sOptionName   Имя необходимого параметра
	 * @param int    $iUserID       ID пользователя, если null - текущий пользователь
	 * @param mixed  $mDefaultValue Значение по-умолчанию, возвращается, если другое значение не было найдено
	 *
	 * @return null|mixed
	 */
	public static function getUserOption($sOptionName, $iUserID=null, $mDefaultValue=null)
	{
		if (is_null($iUserID))
		{
			$iUserID = Application::getInstance()->getUser()->getID();
		}
		$arRes = Tables\UserOptionsTable::getOne(
			array (
				'select' => array ('VALUE'),
				'filter' => array('USER_ID'=>$iUserID,'NAME'=>strtoupper($sOptionName))
			)
		);
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

	/**
	 * Добавляет или обновляет значение параметра пользователя, возвращая ID записи, либо false
	 *
	 * @param string $sOptionName Имя необходимого параметра (приводится к верхнему регистру)
	 * @param mixed  $mValue      Новое значение параметра
	 * @param int    $iUserID     ID пользователя, если null - текущий пользователь
	 *
	 * @return bool|int
	 */
	public static function setUserOption ($sOptionName, $mValue, $iUserID=null)
	{
		if (is_null($iUserID))
		{
			$iUserID = Application::getInstance()->getUser()->getID();
		}
		$arRes = Tables\UserOptionsTable::getOne(array(
			'filter' => array ('USER_ID'=>(int)$iUserID,'NAME'=>strtoupper($sOptionName))
		));
		if ($arRes && $arRes['VALUE']!=$mValue)
		{
			$resUpdate = Tables\UserOptionsTable::update(
				$arRes['ID'],
				array('VALUE'=>$mValue)
			);
			if ($resUpdate->getResult())
			{
				return $arRes['ID'];
			}
			else
			{
				return false;
			}
		}
		elseif ($arRes && $arRes['VALUE']==$mValue)
		{
			return $arRes['ID'];
		}
		elseif (!$arRes)
		{
			$resAdd = Tables\UserOptionsTable::add(array (
				'USER_ID' => (int)$iUserID,
				'NAME' => strtoupper($sOptionName),
				'VALUE' => $mValue
			));
			if ($resAdd->getResult())
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

	public static function createMd5Pass ($login, $pass)
	{
		if (function_exists('password_hash') && function_exists('password_verify'))
		{
			$passHash = password_hash($pass,PASSWORD_BCRYPT);
		}
		else
		{
			$str = 'msergeev|'.$login.'|'.$pass;
			$passHash = md5(md5(trim($str)));
		}

		return $passHash;
	}
	//</editor-fold>

	public function isOnline ($userID=null)
	{
		//TODO: Доделать проверку активности пользователя
		if (is_null($userID))
		{
			$USER = Application::getInstance()->getUser();
			return $USER->isAuthorise();
		}

		return false;
	}

	//<editor-fold defaultstate="collapse" desc="Методы управления куками">
	/**
	 * Сохраняет cookie пользователя
	 *
	 * @param string $cookieName    Имя cookie
	 * @param string $value         Значение cookie
	 * @param int    $userID        ID пользователя
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public static function setUserCookie ($cookieName, $value, $userID=null)
	{
		$r = Application::getInstance()->getContext()->getRequest();
		if (is_null($userID))
		{
			$USER = Application::getInstance()->getUser();
			$userID = $USER->getID();
		}
		$cookieName = strtolower($cookieName);

		$cookieName = str_replace('ms_','',$cookieName);

		return $r->setCookie($cookieName.'_user_'.$userID,$value,(time()+static::REMEMBER_TIME),'/');
	}

	/**
	 * Возвращает значение cookie пользователя
	 *
	 * @param string $cookieName    Имя cookie
	 * @param string $userID        ID пользователя
	 *
	 * @return null|string
	 * @since 0.2.0
	 */
	public static function getUserCookie ($cookieName, $userID=null)
	{
		$r = Application::getInstance()->getContext()->getRequest();
		if (is_null($userID))
		{
			$USER = Application::getInstance()->getUser();
			$userID = $USER->getID();
		}
		$cookieName = strtolower($cookieName);
		$cookieName = str_replace('ms_','',$cookieName);

		if (!is_null($r->getCookie($cookieName.'_user_'.$userID)))
		{
			return $r->getCookie($cookieName.'_user_'.$userID);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Возвращает true, если указанный cookie пользователя существует, false в противном случае
	 *
	 * @param string $cookieName Имя cookie
	 * @param int    $userID     ID пользователя
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public function issetUserCookie ($cookieName, $userID=null)
	{
		$r = Application::getInstance()->getContext()->getRequest();
		if (is_null($userID))
		{
			$USER = Application::getInstance()->getUser();
			$userID = $USER->getID();
		}
		$cookieName = strtolower($cookieName);
		$cookieName = str_replace('ms_','',$cookieName);

		if (!is_null($r->getCookie($cookieName.'_user_'.$userID)))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	//</editor-fold>

	public static function createNewUser ($arData, &$err=array())
	{
		//TODO:Добавить все проверки
		if (!isset($arData['LOGIN']))
		{
			$err['NOT_ISSET_LOGIN'] = 'Логин не указан';
			return false;
		}

		if(!preg_match("/^[a-zA-Z0-9]+$/",$arData['LOGIN']))
		{
			$err['LOGIN_LETTER'] = "Логин может состоять только из букв английского алфавита и цифр";
			return false;
		}

		if(strlen($arData['LOGIN']) < 3 || strlen($arData['LOGIN']) > 255)
		{
			$err['LOGIN_LENGTH'] = "Логин должен быть не меньше 3-х символов и не больше 255";
			return false;
		}

		$arRes = Tables\UsersTable::getOne(
			array(
				'select' => array('ID'),
				'filter' => array(
					'LOGIN' => $arData['LOGIN']
				)
			)
		);
		if ($arRes)
		{
			$err['LOGIN_ISSET_DB'] = "Пользователь с таким логином уже существует.";
			return false;
		}


		$arAdd = array(
			'LOGIN' => $arData['LOGIN'],
			'PASSWORD' => static::createMd5Pass($arData['LOGIN'],$arData['PASSWORD']),
			'EMAIL' => $arData['EMAIL']
		);

		if (isset($arData['MOBILE']))   $arAdd['MOBILE']    = $arData['MOBILE'];
		if (isset($arData['NAME']))     $arAdd['NAME']      = $arData['NAME'];
		if (isset($arData['FIO_F']))    $arAdd['FIO_F']     = $arData['FIO_F'];
		if (isset($arData['FIO_I']))    $arAdd['FIO_I']     = $arData['FIO_I'];
		if (isset($arData['FIO_O']))    $arAdd['FIO_O']     = $arData['FIO_O'];

		return Tables\UsersTable::add($arAdd)->getInsertId();
	}

	//<editor-fold defaultstate="collapse" desc="Методы авторизации пользователей">
	public static function logIn ($login, $pass, $remember=false)
	{
		$arRes = Tables\UsersTable::getOne(
			array(
				'select' => array('ID','PASSWORD'),
				'filter' => array(
					'LOGIN' => $login,
					'ACTIVE' => true
				)
			)
		);
		$USER = Application::getInstance()->getUser();

		if ($arRes)
		{
			if (function_exists('password_hash') && function_exists('password_verify'))
			{
				if (password_verify($pass,$arRes['PASSWORD']))
				{
					$USER->logIn($arRes['ID'],$remember);
					return true;
				}
			}
			elseif ($arRes['PASSWORD'] == static::createMd5Pass($login,$pass))
			{
				$USER->logIn($arRes['ID'],$remember);
				return true;
			}

		}

		return false;
	}

	public static function logOut ()
	{
		$USER = &Application::getInstance()->getUser();
		$USER->logOut();
	}
	//</editor-fold>

	//<editor-fold defaultstate="collapse" desc="Доступы">
	/**
	 * Возвращает true, есди указанный или текущий пользователь имеет указанные права доступа
	 *
	 * @param string|array $mAccess Код или коды доступа
	 * @param string $moduleName Имя модуля
	 * @param null   $userID ID пользователя
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public static function issetAccess ($mAccess, $moduleName='core', $userID=null)
	{
		if (is_null($userID))
		{
			$userID = Application::getInstance()->getUser()->getID();
		}

		if (static::isAdmin($userID))
		{
			return true;
		}

		$arFilter = array (
			'ACCESS_CODE' => $mAccess,
			'MODULE_NAME' => $moduleName
		);
		$arRes = Tables\UserGroupAccessTable::getList(array (
			'select' => ['USER_GROUP_ID'],
			'filter' => $arFilter
		));
		$arGroups = array ();
		if ($arRes && !empty($arRes))
		{
			foreach ($arRes as $arAccess)
			{
				if (!in_array($arAccess['USER_GROUP_ID'],$arGroups))
				{
					$arGroups[] =  $arAccess['USER_GROUP_ID'];
				}
			}
		}

		if (!empty($arGroups))
		{
			return static::isInGroups($userID, $arGroups);
		}

		return false;
	}

	/**
	 * Присваивает всем группам пользователей все перечисленные права для модуля
	 *
	 * @param string       $sModuleName Имя модуля
	 * @param array|int    $mGroup      Группа или массив групп пользователей
	 * @param array|string $mAccess     Доступ или массив доступов, назначаемых всем группам
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public static function addAccess ($sModuleName, $mGroup=array(), $mAccess=array ())
	{
		if (!Modules::checkModuleName($sModuleName))
		{
			return false;
		}
		$arAdd = array ();

		//Приводим групу и код доступа к массивам, если ими они не являются
		if (!is_array($mGroup))
		{
			$mGroup = array ($mGroup);
		}
		if (!is_array($mAccess))
		{
			$mAccess = array ($mAccess);
		}

		//Составляем множество возможных доступов из полученных параметров
		foreach ($mGroup as $groupID)
		{
			foreach ($mAccess as $accessCode)
			{
				$arAdd[] = array (
					'MODULE_NAME' => $sModuleName,
					'USER_GROUP_ID' => $groupID,
					'ACCESS_CODE' => $accessCode
				);
			}
		}

		//Получаем существующие записи с правами для модуля и групп
		$arRes = Tables\UserGroupAccessTable::getList(array (
			'select' => array ('USER_GROUP_ID','ACCESS_CODE'),
			'filter' => array (
				'MODULE_NAME'=>$sModuleName,
				'USER_GROUP_ID' => $mGroup
			)
		));
		//Удаляем из списка добавления записи, существующие в БД
		if ($arRes && !empty($arRes))
		{
			for ($i=0;$i<count($arAdd);$i++)
			{
				foreach ($arRes as $ar_res)
				{
					if (
						$arAdd[$i]['USER_GROUP_ID'] == $ar_res['USER_GROUP_ID']
						&& $arAdd[$i]['ACCESS_CODE'] == $ar_res['ACCESS_CODE']
					) {
						unset($arAdd[$i]);
						break;
					}
				}
			}
		}

		//Если есть что добавлять, добавляем
		if (!empty($arAdd))
		{
			Tables\UserGroupAccessTable::add($arAdd);
		}

		return true;
	}

	/**
	 * Удаляет указанные доступы для групп пользователей, если они существуют
	 *
	 * @param string       $sModuleName Имя модуля
	 * @param array|int    $mGroups     Группа или массив групп пользователей
	 * @param array|string $mAccess     Код или массив кодов доступа
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public static function deleteAccess ($sModuleName, $mGroups=array (), $mAccess=array ())
	{
		//Проверяем правильность имени модуля
		if (!Modules::checkModuleName($sModuleName))
		{
			return false;
		}
		$arDelete = array ();

		//Правращаем группы и коды доступа в массивы, если они ими не являются
		if (!is_array($mGroups))
		{
			$mGroups = array($mGroups);
		}
		if (!is_array($mAccess))
		{
			$mAccess = array ($mAccess);
		}

		//Получаем существующие записи о доступах
		$arRes = Tables\UserGroupAccessTable::getList(array (
			'filter' => array(
				'MODULE_NAME' => $sModuleName,
				'USER_GROUP_ID' => $mGroups
			)
		));
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
						) {
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
				Tables\UserGroupAccessTable::delete($deleteID,true);
			}
		}

		return true;
	}

	public static function setAccess ($sModuleName, array $arAccess)
	{
		if (!is_array($arAccess)||empty($arAccess)||strlen($sModuleName)<=0)
		{
			return false;
		}

		foreach ($arAccess as $iUserGroup=>$ar_access)
		{
			if (!empty($ar_access))
			{
				$arAdd = [];
				$arDelete = [];
				foreach ($ar_access as $code=>$action)
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
					static::addAccess($sModuleName,$iUserGroup,$arAdd);
				}
				if (!empty($arDelete))
				{
					static::deleteAccess($sModuleName,$iUserGroup,$arDelete);
				}
			}
		}
	}
	//TODO: Подумать над удалением доступов при удалении групп пользователей
	//</editor-fold>

	/**
	 * Проверяет существование пользователя с заданным ID
	 *
	 * @param int $iUserID ID пользователя
	 *
	 * @return bool Если пользователь существует, возвращает true, иначе false
	 */
	public static function isset($iUserID)
	{
		if ((int)$iUserID <= 0)
		{
			return false;
		}
		return !!Tables\UsersTable::getById($iUserID);
	}
}
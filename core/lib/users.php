<?php
/**
 * MSergeev\Core\Lib\Users
 * Обработка данных пользователей
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @since 0.1.0
 */

namespace MSergeev\Core\Lib;

use MSergeev\Core\Entity\Application;
use MSergeev\Core\Entity\Db\Query;
use MSergeev\Core\Tables;
use MSergeev\Core\Entity\Db\SqlHelper;

class Users
{
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

		if ($arRes)
		{
			if ($arRes['PASSWORD'] == static::createMd5Pass($login,$pass))
			{
				$USER = &Application::getInstance()->getUser();
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

	protected static function createMd5Pass ($login, $pass)
	{
		$str = 'msergeev|'.$login.'|'.$pass;
		return md5(md5(trim($str)));
	}
}
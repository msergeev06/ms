<?php
/**
 * Объект пользователя
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity;

use Ms\Core\Entity\Type\Date;
use Ms\Core\Lib\Users;
use Ms\Core\Tables\UsersTable;
use Ms\Core\Tables\UserToGroupTable;

class User
{
	/**
	 * ID текущего пользователя
	 * @var int ID пользователя
	 * @access protected
	 */
	protected $ID = null;

	/**
	 * Основные параметры пользователя
	 * @var array
	 */
	protected $arUserData = array ();

	/**
	 * Флаг, говорящий администратор ли текущий пользователь
	 * @var bool
	 * @access protected
	 */
	protected $isAdmin = null;

	/**
	 * Флаг, говорящий гость ли текущий пользователь
	 * @var bool
	 * @access protected
	 */
	protected $isGuest = null;

	/**
	 * Флаг, говорящий что текущий пользователь является системным
	 * @var bool
	 */
	protected $isSysUser = false;
	/**
	 * Проверочная строка при авторизации
	 * @var string
	 * @access protected
	 */
	protected $hash = null;

	/**
	 * Массив дополнительных параметров
	 * @var array
	 * @access protected
	 */
	protected $arParams = array();

	private static $object = null;


	//<editor-fold defaultstate="collapse" desc="Создание и получение объекта пользователя">
	/**
	 * Возвращает единственный объект пользователя
	 * Singleton
	 *
	 * @return User
	 */
	public static function getObject ()
	{
		if (is_null(static::$object))
		{
			static::$object = new static();
		}

		return static::$object;
	}

	/**
	 * Создает объект пользователя
	 */
	private function __construct ()
	{
		if (defined('RUN_ON_SYSTEM_USER') && RUN_ON_SYSTEM_USER === true)
		{
			$this->logInSysUser();
			return $this;
		}

		$r = Application::getInstance()->getContext()->getRequest();
		if (!is_null($r->getCookie('user_id')) && !is_null($r->getCookie('hash')))
		{
			$userID = intval($r->getCookie('user_id'));
			$hash = $r->getCookie('hash');
			$arRes = UsersTable::getList(
				array(
					'select' => array('ID'),
					'filter' => array(
						'ID' => $userID,
						'HASH' => $hash
					)
				),true
			);
			if ($arRes)
			{
				$this->ID = $userID;
				$this->hash = $hash;
				$rememberMe = false;
				if (!is_null($r->getCookie('remember')) && intval($r->getCookie('remember'))>0)
				{
					$rememberMe = true;
				}
				if ($this->ID == $this->getConst('ADMIN_USER'))
				{
					$this->logInAdmin($rememberMe);
				}
				elseif ($this->ID == $this->getConst('GUEST_USER'))
				{
					$this->logInGuest();
				}
				else
				{
					$this->logInOther($rememberMe);
				}
			}
			else
			{
				$this->logInGuest();
			}
		}
		else
		{
			$this->logInGuest();
		}
	}
	//</editor-fold>


	//<editor-fold defaultstate="collapse" desc="Методы получения данных о пользователе">
	/**
	 * Возвращает ID текущего пользователя
	 *
	 * @api
	 *
	 * @access public
	 * @return int ID текущего пользователя
	 */
	public function getID ()
	{
		return (int)$this->ID;
	}

	/**
	 * Администратор ли текущий пользователь
	 * true - администратор
	 * false - не администратор
	 *
	 * @api
	 *
	 * @access public
	 * @return bool
	 */
	public function isAdmin ()
	{
		if (is_null($this->isAdmin))
		{
			$this->isAdmin = Users::isAdmin($this->ID);
		}

		return $this->isAdmin;
	}

	/**
	 * Гость ли текущий пользователь
	 * true - гость
	 * false - не гость
	 *
	 * @api
	 *
	 * @return bool
	 */
	public function isGuest()
	{
		return $this->isGuest;
	}

	/**
	 * Явялется ли текущий пользователь системным
	 *
	 * @return bool
	 */
	public function isSysUser()
	{
		return $this->isSysUser;
	}

	/**
	 * Авторизован ли текущий пользователь
	 * true - авторизован
	 * false - требуется авторизация
	 *
	 * @return bool
	 */
	public function isAuthorise ()
	{
		$r = Application::getInstance()->getContext()->getRequest();

		return (!is_null($r->getCookie('user_id')) && !is_null($r->getCookie('hash'))
			&& $r->getCookie('user_id') == $this->ID
			&& $r->getCookie('hash') == $this->hash
		);
	}

	/**
	 * Вовзращает значение констант
	 *
	 * @param string $name Код константы
	 *
	 * @return int|null
	 */
	public function getConst ($name)
	{
		switch (strtoupper($name))
		{
			case 'ADMIN_USER':
				return Users::ADMIN_USER;
			case 'ADMIN_GROUP':
				return Users::ADMIN_GROUP;
			case 'GUEST_USER':
				return Users::GUEST_USER;
			case 'REMEMBER_TIME':
				return Users::REMEMBER_TIME;
			default:
				return NULL;
		}
	}

	/**
	 * Возвращает указанный параметр пользователя, либо false
	 *
	 * @param string $strParamName Имя параметра
	 *
	 * @return bool
	 */
	public function getParam ($strParamName)
	{
		if (isset($this->arParams[$strParamName]))
		{
			return $this->arParams[$strParamName];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Устанавливает значение параметра пользователя
	 *
	 * @param string $strParamName Имя параметра
	 * @param mixed $value Значение параметра
	 */
	public function setParam ($strParamName, $value)
	{
		$strParamName = strtoupper($strParamName);
		$this->arParams[$strParamName] = $value;
	}

	/**
	 * Возвращает массив групп, в которых состоит пользователь.
	 *
	 * @return array|bool Массив групп, либо false
	 */
	public function getGroups ()
	{
		return Users::getGroups($this->ID);
	}

	/**
	 * Возвращает true либо false на основе принадлежности пользователя к группам
	 * Если используется логика 'or', вернет true, если пользователь состоит хотя бы в одной из групп
	 * Если используется логика 'and', вернет true только если пользователь состоит во всех перечисленных группах
	 * Если используется поле ID - ожидается массив ID групп
	 * Если используется поле CODE - ожидается массив кодов групп
	 *
	 * @param array $arGroups Массив групп для проверки, может содержать ID групп или их коды
	 *                        в зависимости от используемого типа поля field
	 * @param string $logic   Логика поиска 'or' или 'and'
	 * @param string $field   Поле ID или CODE
	 *
	 * @return bool
	 */
	public function isInGroups ($arGroups = array (), $logic = 'or', $field = 'ID')
	{
		return Users::isInGroups($this->ID, $arGroups, $logic, $field);
	}

	/**
	 * Возвращает логин пользователя
	 *
	 * @return mixed
	 */
	public function getLogin ()
	{
		if (!isset($this->arUserData['LOGIN']))
		{
			$this->getUserData();
		}

		return $this->arUserData['LOGIN'];
	}

	/**
	 * Возвращает имя пользователя
	 *
	 * @return string
	 */
	public function getName ()
	{
		if (!isset($this->arUserData['NAME']))
		{
			$this->getUserData();
		}

		if (isset($this->arUserData['NAME']))
		{
			return $this->arUserData['NAME'];
		}
		else
		{
			return 'null';
		}
	}

	public function getAvatar()
	{
		//TODO: Доделать аватары пользователей
		if (!isset($this->arUserData['AVATAR']))
		{
			$this->getUserData();
		}

		if (isset($this->arUserData['AVATAR']))
		{
			return $this->arUserData['AVATAR'];
		}
		else
		{
			return null;
		}
	}

	public function isOnline ()
	{
		//TODO: Доделать проверку активности пользователя
		return $this->isAuthorise();
	}

	/**
	 * Возвращает значение уазанного параметра пользователя
	 *
	 * @param string $sOptionName   Имя нужного параметра
	 * @param mixed  $mDefaultValue Значение по-умолчанию
	 *
	 * @uses Users::getUserOption()
	 *
	 * @return mixed|null
	 */
	public function getOption ($sOptionName, $mDefaultValue)
	{
		return Users::getUserOption($sOptionName,$this->getID(),$mDefaultValue);
	}

	/**
	 * Устанавливает новое значение параметра пользователя
	 *
	 * @param string $sOptionName Имя параметра
	 * @param mixed  $mValue      Новое значение параметра
	 *
	 * @uses Users::setUserOption()
	 *
	 * @return bool|int
	 */
	public function setOption ($sOptionName, $mValue)
	{
		return Users::setUserOption($sOptionName,$mValue,$this->getID());
	}

	/**
	 * Получает основные параметры пользователя
	 *
	 * @since 0.2.0
	 */
	protected function getUserData ()
	{
		if (empty($this->arUserData))
		{
			$arRes = UsersTable::getById(
				$this->getID(),
				array (
					'ACTIVE',
					'LOGIN',
					'EMAIL',
					'MOBILE',
					'NAME',
					'FIO_F',
					'FIO_I',
					'FIO_O',
					'AVATAR'
				)
			);
			if ($arRes)
			{
				$this->arUserData = $arRes;
			}
		}
	}
	//</editor-fold>


	//<editor-fold defaultstate="collapse" desc="Методы авторизации пользователя">
	/**
	 * Авторизует указанного пользователя
	 *
	 * @param int  $userID      ID пользователя
	 * @param bool $rememberMe  Флаг, запомнить авторизацию
	 */
	public function logIn ($userID, $rememberMe=false)
	{
		$this->ID = intval($userID);
		$this->hash = $this->generateRandomString();
		UsersTable::update(intval($userID),array("HASH"=>$this->hash));
		if ($this->ID == $this->getConst('ADMIN_USER') || $this->isAdmin())
		{
			$this->logInAdmin($rememberMe);
		}
		elseif ($this->ID == $this->getConst('GUEST_USER'))
		{   //Авторизация гостя не должна проходить так, но на всякий случай
			$this->logInGuest();
		}
		else
		{
			$this->logInOther($rememberMe);
		}
		$this->arUserData = array();
		$this->arParams = array ();
	}

	/**
	 * Разавторизовать пользователя. Автоматически авторизует гостя
	 */
	public function logOut ()
	{
		$r = Application::getInstance()->getContext()->getRequest();
		if (!is_null($r->getCookie('user_id')))
		{
			UsersTable::update(intval($r->getCookie('user_id')),array("HASH"=>NULL));
		}
		$this->arParams = array();
		$this->logInGuest();
	}

	/**
	 * Генерирует случайную строку для хеша
	 *
	 * @param string $prefix [optional] Префикс
	 *
	 * @return string
	 */
	public function generateRandomString ($prefix=null)
	{
		if (is_null($prefix))
		{
			$prefix = rand();
		}

		if (function_exists('password_hash'))
		{
			$random = password_hash($prefix,PASSWORD_BCRYPT);
		}
		else
		{
			$random = md5(uniqid($prefix, true));
		}

		return $random;
	}

	/**
	 * Авторизует гостя
	 */
	protected function logInGuest ()
	{
		$this->ID = Users::GUEST_USER;
		$this->isAdmin = false;
		$this->isGuest = true;
		$this->delCookie();
	}

	protected function logInSysUser()
	{
		$this->ID = Users::SYSTEM_USER;
		$this->isAdmin = true;
		$this->isGuest = false;
	}

	/**
	 * Авторизует админа
	 *
	 * @param bool $rememberMe Если true, необходимо запомнить авторизацию
	 */
	protected function logInAdmin ($rememberMe=false)
	{
		$this->isAdmin = true;
		$this->isGuest = false;
		$this->setCookie($this->ID, $this->hash, $rememberMe);
	}

	/**
	 * Авторизует остальных пользователей (не админ/не гость)
	 *
	 * @param bool $rememberMe Если true, необходимо запомнить авторизацию
	 */
	protected function logInOther ($rememberMe=false)
	{
		$this->isGuest = false;
		$this->setCookie($this->ID, $this->hash, $rememberMe);
	}
	//</editor-fold>


	//<editor-fold defaultstate="collapse" desc="Методы работы с куками">
	/**
	 * Сохраняет cookie пользователя
	 *
	 * @param string $cookieName    Имя cookie
	 * @param string $value         Значение cookie
	 * @param int    $userID        ID пользователя
	 *
	 * @return bool
	 */
	public function setUserCookie ($cookieName, $value, $userID=null)
	{
		if (is_null($userID))
		{
			$userID = $this->ID;
		}

		return Users::setUserCookie($cookieName,$value,$userID);
	}

	/**
	 * Возвращает значение cookie пользователя
	 *
	 * @param string $cookieName    Имя cookie
	 * @param string $userID        ID пользователя
	 *
	 * @return null|string
	 */
	public function getUserCookie ($cookieName, $userID=null)
	{
		if (is_null($userID))
		{
			$userID = $this->ID;
		}

		return Users::getUserCookie($cookieName, $userID);
	}

	/**
	 * Возвращает true, если указанный cookie пользователя существует, false в противном случае
	 *
	 * @param string $cookieName Имя cookie
	 * @param int    $userID     ID пользователя
	 *
	 * @return bool
	 */
	public function issetUserCookie ($cookieName, $userID=null)
	{
		$r = Application::getInstance()->getContext()->getRequest();
		if (is_null($userID))
		{
			$userID = $this->ID;
		}
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

	/**
	 * Устанавливает основные cookie пользователя
	 *
	 * @param int    $userID     ID пользователя
	 * @param string $hash       Hash авторизации
	 * @param bool   $rememberMe Флаг необходимости запомнить авторизацию
	 */
	protected function setCookie ($userID=null, $hash, $rememberMe=false)
	{
		$r = Application::getInstance()->getContext()->getRequest();
		if ($rememberMe)
		{
			$time = time() + Users::REMEMBER_TIME;
		}
		else
		{
			$time = 0;
		}
		$r->setCookie('user_id',$userID,$time,'/');
		$r->setCookie('hash',$hash,$time,'/');
		$r->setCookie('remember',$time,$time,'/');
	}

	/**
	 * Удаляет основные cookie пользователя
	 */
	protected function delCookie ()
	{
		$r = Application::getInstance()->getContext()->getRequest();
		if (!is_null($r->getCookie('user_id')))
		{
			$r->setCookie('user_id',null,time()-30,'/');
		}
		if (!is_null($r->getCookie('hash')))
		{
			$r->setCookie('hash',null,time()-30,'/');
		}
		if (!is_null($r->getCookie('remember')))
		{
			$r->setCookie('remember',null,time()-30,'/');
		}
	}
	//</editor-fold>

}
<?php
/**
 * Ms\Core\Entity\User
 * Объект пользователя
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity;

use Ms\Core\Lib;
use Ms\Core\Tables\UsersTable;

class User
{
	/**
	 * ID пользователя admin
	 * @var int
	 * @access private
	 */
	private $ADMIN_USER = 1;

	/**
	 * ID пользователя guest
	 * @var int
	 * @access private
	 */
	private $GUEST_USER = 2;

	/**
	 * Время в секундах, на которое сохраняется сессия пользователя
	 * @var int
	 * @access private
	 */
	private $REMEMBER_TIME = 31536000; //365 дней

	/**
	 * ID текущего пользователя
	 * @var int ID пользователя
	 * @access protected
	 */
	protected $ID = null;

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

	/**
	 * Создает объект пользователя
	 */
	public function __construct ()
	{
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
				if ($this->ID == $this->ADMIN_USER)
				{
					$this->logInAdmin($rememberMe);
				}
				elseif ($this->ID == $this->GUEST_USER)
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
		return $this->ID;
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
		if ($this->ID == $this->ADMIN_USER)
		{
			$this->logInAdmin($rememberMe);
		}
		elseif ($this->ID == $this->GUEST_USER)
		{   //Авторизация гостя не должна проходить так, но на всякий случай
			$this->logInGuest();
		}
		else
		{
			$this->logInOther($rememberMe);
		}
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
	 * Вовзращает значение констант
	 *
	 * @param string $name Код константы
	 *
	 * @return int|null
	 */
	public function getConst ($name)
	{
		switch ($name)
		{
			case 'ADMIN_USER':
				return $this->ADMIN_USER;
			case 'GUEST_USER':
				return $this->GUEST_USER;
			case 'REMEMBER_TIME':
				return $this->REMEMBER_TIME;
			default:
				return NULL;
		}
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

		return md5(uniqid($prefix, true));
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
		$this->arParams[$strParamName] = $value;
	}

	/*	protected function issetAutorisedUser ()
		{
			return !is_null($this->ID);
		}*/

	/**
	 * Авторизует гостя
	 */
	protected function logInGuest ()
	{
		$this->ID = $this->GUEST_USER;
		$this->isAdmin = false;
		$this->isGuest = true;
		$this->delCookie();
	}

	/**
	 * Авторизует админа
	 *
	 * @param bool $rememberMe Если true, необходимо запомнить авторизацию
	 */
	protected function logInAdmin ($rememberMe=false)
	{
		$this->isAdmin = true;;
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
		$this->isAdmin = false;
		$this->isGuest = false;
		$this->setCookie($this->ID, $this->hash, $rememberMe);
	}

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
		$r = Application::getInstance()->getContext()->getRequest();
		if (is_null($userID))
		{
			$userID = $this->ID;
		}
		$cookieName = str_replace('ms_','',$cookieName);

		return $r->setCookie($cookieName.'_user_'.$userID,$value,(time()+$this->REMEMBER_TIME),'/');
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
		$r = Application::getInstance()->getContext()->getRequest();
		if (is_null($userID))
		{
			$userID = $this->ID;
		}
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
	protected function setCookie ($userID, $hash, $rememberMe=false)
	{
		$r = Application::getInstance()->getContext()->getRequest();
		if ($rememberMe)
		{
			$time = time() + $this->REMEMBER_TIME;
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
}
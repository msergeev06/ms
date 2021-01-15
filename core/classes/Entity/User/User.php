<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\User;

use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\System\Cookie;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Modules\WrongModuleNameException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Entity\Modules\Modules;
use Ms\Core\Tables\UsersTable;

/**
 * Класс Ms\Core\Entity\User\User
 * Объект пользователя системы
 */
class User
{
    /**
     * ID пользователя
     *
     * @var null|int
     */
    protected $ID = null;
    /**
     * Админ ли пользователь
     *
     * @var bool
     */
    protected $admin = false;
    /**
     * Список групп, в которые входит пользователь
     *
     * @var UserGroupCollection
     */
    protected $groups = null;
    /**
     * Проверочная строка при авторизации пользователя
     *
     * @var null|string
     */
    protected $hash = null;
    /**
     * Параметры пользователя
     *
     * @var UserParameters
     */
    protected $parameters = null;
    /**
     * Является ли пользователь системным
     *
     * @var bool
     */
    protected $system = false;

    /**
     * Конструктор класса User
     *
     * @param int|null $userID ID пользователя
     */
    public function __construct (int $userID = null)
    {
        $this->setID($userID);
        $this->parameters = new UserParameters();
    }

    /**
     * Проверяет, имеет ли право пользователь на совершение указанного действия
     *
     * @param string $moduleName   Имя модуля, для которого проверяются права доступа
     * @param string $accessName   Имя права доступа. Будет преобразовано к нижнему регистру
     * @param array  $arParams     Возможные дополнительные параметры, для уточнения прав доступа
     * @param bool   $bIgnoreAdmin Флаг, что при проверке игнорировать права администратора на выполнение любых действий
     *
     * @return bool
     * @throws WrongModuleNameException
     */
    public function can (string $moduleName, string $accessName, array $arParams = [], bool $bIgnoreAdmin = false)
    {
        if (!Modules::getInstance()->checkModuleName($moduleName))
        {
            throw new WrongModuleNameException($moduleName,__FILE__,__LINE__);
        }

        //Если пользователь - системный, ему вообще все можно
        if ($this->isSystem())
        {
            return true;
        }

        //Если пользователь администратор и не следует игнорировать этот факт, пользователь имеет право на любой действие
        if ($this->isAdmin() && !$bIgnoreAdmin)
        {
            return true;
        }
        $accessName = strtolower($accessName);

        try
        {
            $bCan = CanAccessController::getInstance()->can($this, $moduleName, $accessName, $arParams, $bIgnoreAdmin);
        }
        catch (SystemException $e)
        {
            return false;
        }

        return $bCan;
    }

    public function getParameters ()
    {
        return $this->parameters;
    }

    /**
     * Возвращает проверочную строку авторизации
     *
     * @return string|null
     */
    public function getHash (): string
    {
        return $this->hash;
    }

    /**
     * Устанавливает проверочную строку авторизации
     *
     * @param string|null $hash
     *
     * @return User
     */
    public function setHash (string $hash = null): User
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Возвращает ID пользователя
     *
     * @return int|null
     */
    public function getID ()
    {
        return $this->ID;
    }

    /**
     * Устанавливает ID пользователя
     *
     * @param int|null $ID
     *
     * @return User
     */
    public function setID (int $ID = null): User
    {
        $this->ID = $ID;

        return $this;
    }

    /**
     * Проверяет, является ли пользователь администратором
     *
     * @return bool
     */
    public function isAdmin (): bool
    {
        $result = UserController::getInstance()->isAdmin($this->ID);

        return $result ? $result : $this->admin;
    }

    /**
     * Устанавливает флаг того, что пользователь является администратором
     *
     * @param bool $admin
     *
     * @return User
     */
    public function setAdmin (bool $admin): User
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Проверяет, авторизован ли пользователь
     *
     * @return bool
     */
    public function isAuthorize ()
    {
        //Системный пользователь авторизован всегда
        if ($this->isSystem())
        {
            return true;
        }

        return Application::getInstance()->getAuthorizer()->isAuthorize();
    }

    /**
     * Возвращает TRUE, если пользователь не авторизован, т.е. является гостем
     *
     * @return bool
     */
    public function isGuest ()
    {
        return !$this->isAuthorize();
    }

    /**
     * Проверяет, является ли пользователь системным
     *
     * @return bool
     */
    public function isSystem (): bool
    {
        return $this->system;
    }

    /**
     * Устанавливает флаг того, что пользователь является системным
     *
     * @param bool $system
     *
     * @return User
     */
    public function setSystem (bool $system): User
    {
        $this->system = $system;

        return $this;
    }

    /**
     * Возвращает коллекцию групп, к которым принадлежит пользователь
     *
     * @return UserGroupCollection
     */
    public function getGroupsCollection ()
    {
        if (is_null($this->groups))
        {
            $arGroups = UserController::getInstance()->getGroups($this->ID);
            if (!$arGroups)
            {
                return new UserGroupCollection();
            }
            $this->groups = new UserGroupCollection();
            foreach ($arGroups as $group)
            {
                $this->groups->addGroup(
                    (new UserGroup($group['GROUP_ID']))
                        ->setName($group['GROUP_NAME'])
                        ->setCode($group['GROUP_CODE'])
                );
            }
        }

        return $this->groups;
    }

    /**
     * Возвращает true либо false на основе принадлежности пользователя к группам
     * Если используется логика 'or', вернет true, если пользователь состоит хотя бы в одной из групп
     * Если используется логика 'and', вернет true только если пользователь состоит во всех перечисленных группах
     * Если используется поле ID - ожидается массив ID групп
     * Если используется поле CODE - ожидается массив кодов групп
     *
     * @param array  $arGroups Массив групп для проверки, может содержать ID групп или их коды
     *                         в зависимости от используемого типа поля field
     * @param string $logic    Логика поиска 'or' или 'and'
     * @param string $field    Поле ID или CODE
     *
     * @return bool
     */
    public function isInGroups ($arGroups = [], $logic = 'or', $field = 'ID')
    {
        return UserController::getInstance()->isInGroups($this->ID, $arGroups, $logic, $field);
    }

    /**
     * Возвращает логин пользователя
     *
     * @return mixed
     */
    public function getLogin ()
    {
        if (!$this->parameters->isset('login'))
        {
            $this->getUserData();
        }

        return $this->parameters->getParameter('login');
    }

    /**
     * Возвращает имя пользователя
     *
     * @return string
     */
    public function getName ()
    {
        if (!$this->parameters->isset('name'))
        {
            $this->getUserData();
        }

        return (string)$this->parameters->getParameter('name');
    }

    /**
     * Возвращает ссылку на аватар пользователя
     *
     * @return string|null
     */
    public function getAvatar ()
    {
        //TODO: Доделать аватары пользователей
        if (!$this->parameters->isset('avatar'))
        {
            $this->getUserData();
        }

        return $this->parameters->getParameter('avatar');
    }

    /**
     * Возвращает TRUE, если пользователь был online:
     * если bNow = TRUE - менее 1 минуты назад
     * если bNow = FALSE - менее 5 минут назад
     *
     * @param bool $bNow
     *
     * @return bool
     */
    public function isOnline (bool $bNow = true)
    {
        $orm = ORMController::getInstance(new UsersTable());
        if ($this->ID > 0)
        {
            try
            {
                $arRes = $orm->getOne(
                    [
                        'select' => ['LAST_ACTIVITY'],
                        'filter' => ['ID' => $this->ID]
                    ]
                );
                if ($arRes)
                {
                    $now = new Date();
                    $last = new Date();
                    if ($bNow)
                    {
                        $last->modify('+1 minute');
                    }
                    else
                    {
                        $last->modify('+5 minute');
                    }
                    if ($last < $now)
                    {
                        return false;
                    }
                    else
                    {
                        return true;
                    }
                }
            }
            catch (SystemException $e)
            {
            }
        }

        return $this->isAuthorize();
    }

    /**
     * Возвращает значение cookie, если оно установлено, иначе возвращает NULL
     *
     * @param string $cookieName Имя cookie
     *
     * @return mixed|null
     */
    public function getCookie (string $cookieName)
    {
        $cookie = Application::getInstance()->getCookieController();
        if ($cookie->isset($this->getFullCookieName($cookieName)))
        {
            return $cookie->getCookie($this->getFullCookieName($cookieName));
        }

        return null;
    }

    /**
     * Возвращает преобразованное имя cookie вида cookieName_user_ID
     *
     * @param string $cookieName Имя cookie
     *
     * @return string
     */
    protected function getFullCookieName (string $cookieName)
    {
        $cookieName = strtolower($cookieName);
        $cookieName = str_replace('_user_' . $this->ID, '', $cookieName);

        return $cookieName . '_user_' . $this->ID;
    }

    public function setCookie (string $cookieName, $value)
    {
        $cookie = Application::getInstance()->getCookieController();
        $cookieName = strtolower($cookieName);

        $cookieName = str_replace('ms_', '', $cookieName);

        return $cookie->setCookie(
            (new Cookie($cookieName . '_user_' . $this->ID,$value))
                ->setExpires((time() + UserController::REMEMBER_TIME))
                ->setPath('/')
        );

    }

    public function getParam (string $parameterName)
    {
        return $this->parameters->getParameter($parameterName);
    }

    /**
     * Получает основные параметры пользователя
     */
    protected function getUserData ()
    {
        $orm = ORMController::getInstance(new UsersTable());
        $arRes = $orm->getById(
            $this->getID(),
            [
                'ACTIVE',
                'LOGIN',
                'EMAIL',
                'MOBILE',
                'NAME',
                'FIO_F',
                'FIO_I',
                'FIO_O',
                'AVATAR'
            ]
        );
        if ($arRes)
        {
            $this->parameters->addParameter('active',$arRes['ACTIVE']);
            $this->parameters->addParameter('login',$arRes['LOGIN']);
            $this->parameters->addParameter('email',$arRes['EMAIL']);
            $this->parameters->addParameter('mobile',$arRes['MOBILE']);
            $this->parameters->addParameter('name',$arRes['NAME']);
            $this->parameters->addParameter('fio_f',$arRes['FIO_F']);
            $this->parameters->addParameter('fio_i',$arRes['FIO_I']);
            $this->parameters->addParameter('fio_o',$arRes['FIO_O']);
            $this->parameters->addParameter('avatar',$arRes['AVATAR']);
        }
    }
}
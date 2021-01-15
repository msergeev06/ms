<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\User;

use Ms\Core\DbHandlers\UsersDbHandler;
use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\System\Cookie;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Arguments\ArgumentException;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Exceptions\Db\ValidateException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Tables\UsersTable;

/**
 * Класс Ms\Core\Entity\User\Authorizer
 * Управляет авторизацией пользователей
 */
class Authorizer
{
    protected $user = null;

    public function __construct (User $user)
    {
        $this->user = $user;
    }

    protected function isNoAuthFiles ()
    {
        $server = Application::getInstance()->getServer();
        return (
            preg_match('/\/gps\.php/is', $server->getRequestUri())
            || preg_match('/\/trackme\.php/is', $server->getRequestUri())
            || preg_match('/\/btraced\.php/is', $server->getRequestUri())
            || preg_match('/\/rss\.php/is', $server->getRequestUri())
        );
    }

    /**
     * Проверяет HTTP авторизацию
     *
     * @return void
     */
    public function checkHttpAuthorize ()
    {
        $settings = Application::getInstance()->getSettings();
        $server = Application::getInstance()->getServer();
        $bNeedCheckAuth = $settings->isNeedCheckAuth();
        if ($bNeedCheckAuth === false)
        {
            return;
        }
        $homeNetwork = $settings->getHomeNetwork();

        if (
            1 == 1
            && $homeNetwork != ''
            && !isset($argv[0])
            && (
                !$this->isNoAuthFiles()
                || $_REQUEST['op'] != ''
            )
        )
        {
            $p = preg_quote($homeNetwork);
            $p = str_replace('\*', '\d+?', $p);
            $p = str_replace(',', ' ', $p);
            $p = str_replace('  ', ' ', $p);
            $p = str_replace(' ', '|', $p);

            $remoteAddr = getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : $server->getRemoteAddr();

            if (!preg_match('/' . $p . '/is', $remoteAddr) && $remoteAddr != '127.0.0.1')
            {
                // password required
                //echo "password required for ".$remoteAddr;exit;
                //DebMes("checking access for ".$remoteAddr);

                if (!$server->isExists('PHP_AUTH_USER') || !$server->isExists('PHP_AUTH_PW'))
                {
                    header("WWW-Authenticate: Basic realm=\"SHF 'Доброжил'\"");
                    header("HTTP/1.0 401 Unauthorized");
                    // echo "Authorization required\n";
                    echo "Требуется авторизация\n";
                    exit;
                }
                else
                {
                    if (
                        !UserController::getInstance()->logIn
                        (
                              $server->get('PHP_AUTH_USER'),
                              $server->get('PHP_AUTH_PW'),
                              true
                        )
                    )
                    // if ($_SERVER['PHP_AUTH_USER'] != EXT_ACCESS_USERNAME || $_SERVER['PHP_AUTH_PW'] != EXT_ACCESS_PASSWORD)
                    {
                        // header("Location:$PHP_SELF\n\n");
                        header("WWW-Authenticate: Basic realm=\"SHF 'Доброжил'\"");
                        header("HTTP/1.0 401 Unauthorized");
                        // echo "Authorization required\n";
                        echo "Требуется авторизация\n";
                        exit;
                    }
                }
            }
        }
    }

    /**
     * Проверяет, авторизован ли пользователь
     *
     * @return bool
     */
    public function isAuthorize ()
    {
        $bOk = $this->getParamsFromSessionOrCookie($userID, $hash, $rememberMe, $issetInSession);
        $bOk2 = !is_null($this->user->getID());

        return $bOk && $bOk2;
    }

    /**
     * @param int &$userID ID пользователя
     * @param string &$hash HASH
     * @param bool &$rememberMe Запоминать авторизацию
     * @param bool &$issetInSession Данные существуют в сессии
     *
     * @return bool
     */
    protected function getParamsFromSessionOrCookie (&$userID, &$hash, &$rememberMe, &$issetInSession)
    {
        $cookieController = Application::getInstance()->getCookieController();
        $sessionController = Application::getInstance()->getSession();

        $userID = null;
        $hash = null;
        $rememberMe = false;
        $issetInSession = false;

        if ($sessionController->isset('user_id') && $sessionController->isset('hash'))
        {
            $userID = (int)$sessionController->get('user_id');
            $hash = $sessionController->get('hash');
            if ($sessionController->isset('remember') && (int)$sessionController->get('remember') > 0)
            {
                $rememberMe = true;
            }
            if ($cookieController->isset('remember') && (int)$cookieController->getCookie('remember') > 0)
            {
                $rememberMe = true;
            }

            $issetInSession = true;

            return true;
        }
        elseif ($cookieController->isset('user_id') && $cookieController->isset('hash'))
        {
            $userID = (int)$cookieController->getCookie('user_id');
            $hash = $cookieController->getCookie('hash');
            if ($cookieController->isset('remember') && (int)$cookieController->getCookie('remember') > 0)
            {
                $rememberMe = true;
            }

            return true;
        }

        return false;
    }

    /**
     * Пытается авторизовать указанного пользователя
     *
     * @return bool
     */
    public function loginAttempt()
    {
        $runOnSystemUser = Application::getInstance()->getAppParam('run_on_system_user');
        if ($runOnSystemUser === true)
        {
            return $this->logInSysUser();
        }
        $this->getParamsFromSessionOrCookie($userID, $hash, $rememberMe, $issetInSession);

        // msDebugNoAdmin($userID);
        // msDebugNoAdmin($hash);
        // msDebugNoAdmin($rememberMe);
        // msDebugNoAdmin($issetInSession);

        if (!is_null($userID) && !is_null($hash))
        {
            if ($this->isRightHash($userID, $hash))
            {
                $this->user->setID($userID);
                $this->user->setHash($hash);
                if (!$issetInSession)
                {
                    $this->setSession($userID,$hash);
                }
                if (UserController::getInstance()->isAdmin($this->user->getID()))
                {
                    // return $this->logInAdmin($rememberMe);
                    $this->user->setAdmin(true);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает TRUE, если hash пользователя, сохраненный в сессии или cookie верный, иначе возвращает FALSE
     *
     * @param int    $userID ID пользователя
     * @param string $hash   HASH
     *
     * @return bool
     */
    protected function isRightHash (int $userID, string $hash)
    {
        try
        {
            return !!UsersDbHandler::getInstance()->checkHash($userID, $hash);
        }
        catch (SystemException $e)
        {
            $e->writeToSysLogFile();
            return false;
        }
    }

    /**
     * Авторизует админа
     *
     * @param bool $rememberMe Если true, необходимо запомнить авторизацию
     *
     * @return bool
     */
    protected function logInAdmin ($rememberMe = false)
    {
        $this->user->setAdmin(true);

        return $this->logInOther($rememberMe);
    }

    /**
     * Удаляет основные cookie пользователя
     */
    protected function deleteCookie ()
    {
        $cookie = Application::getInstance()->getCookieController();

        if ($cookie->isset('user_id'))
        {
            $cookie->setCookie(
                (new Cookie('user_id',''))
                    ->setExpires((time() - 30))
                    ->setPath('/')
            );
        }
        if ($cookie->isset('hash'))
        {
            $cookie->setCookie(
                (new Cookie('hash',''))
                    ->setExpires((time() - 30))
                    ->setPath('/')
            );
        }
        if ($cookie->isset('remember'))
        {
            $cookie->setCookie(
                (new Cookie('remember',''))
                    ->setExpires((time() - 30))
                    ->setPath('/')
            );
        }

        return $this;
    }

    protected function setCookie (int $userID, string $hash, bool $rememberMe = false)
    {
        $cookie = Application::getInstance()->getCookieController();
        // msDebugNoAdmin($rememberMe);

        $expire = 0;
        if ($rememberMe)
        {
            $expire = time() + UserController::REMEMBER_TIME;
        }

        $cookie->setCookie(
            (new Cookie('user_id',(string)$userID))
                ->setExpires($expire)
                ->setPath('/')
        );

        $cookie->setCookie(
            (new Cookie('hash',$hash))
                ->setExpires($expire)
                ->setPath('/')
        );

        if ($rememberMe)
        {
            $cookie->setCookie(
                (new Cookie('remember','1'))
                    ->setExpires($expire)
                    ->setPath('/')
            );
        }
        else
        {
            $cookie->setCookie(
                (new Cookie('remember','0'))
                    ->setExpires($expire)
                    ->setPath('/')
            );
        }

        return true;
    }

    protected function setSession (int $userID, string $hash)
    {
        $sessionController = Application::getInstance()->getSession();
        // msDebugNoAdmin($rememberMe);

        $sessionController->set('user_id',(string)$userID);
        $sessionController->set('hash',(string)$hash);

        return true;
    }

    /**
     * Авторизует указанного пользователя
     *
     * @param int  $userID     ID пользователя
     * @param bool $rememberMe Флаг, запомнить авторизацию
     * @param bool $bFromForm  Флаг, авторизация через форму
     *
     * @return bool
     */
    public function logIn (int $userID, bool $rememberMe = false, bool $bFromForm = false)
    {
/*        $hash = $this->generateRandomString();
        $orm = ORMController::getInstance(new UsersTable());
        try
        {
            $orm->update((int)$userID, ["HASH" => $hash]);
        }
        catch (SystemException $e)
        {
            return false;
        }*/

        if ($bFromForm)
        {
            $hash = $this->generateRandomString();
            $this->clearUserData();
        }
        else
        {
            $this->getParamsFromSessionOrCookie($tmp1, $hash, $tmp2, $issetInSession);
        }
        $this->user->setID((int)$userID);
        $this->user->setHash($hash);

        if (UserController::getInstance()->isAdmin($userID))
        {
            return $this->logInAdmin($rememberMe);
        }
        elseif ($this->user->getID() > 0)
        {
            return $this->logInOther($rememberMe);
        }

        return false;
    }

    protected function clearUserData ()
    {
        $this->user->setAdmin(false);
        $this->user->setSystem(false);
        $this->user->getParameters()->clear();
    }

    /**
     * Генерирует случайную строку для хеша
     *
     * @param string $prefix [optional] Префикс
     *
     * @return string
     */
    public function generateRandomString ($prefix = null)
    {
        if (is_null($prefix))
        {
            $prefix = rand();
        }

        if (function_exists('password_hash'))
        {
            $random = password_hash($prefix, PASSWORD_BCRYPT);
        }
        else
        {
            $random = md5(uniqid($prefix, true));
        }

        return $random;
    }

    /**
     * Разавторизовать пользователя. Автоматически авторизует гостя
     *
     * @return bool
     */
    public function logOut ()
    {
        $cookie = Application::getInstance()->getCookieController();
        $orm = ORMController::getInstance(new UsersTable());
        if ($cookie->isset('user_id'))
        {
            try
            {
                $orm->update((int)$cookie->getCookie('user_id'), ["HASH" => null]);
            }
            catch (SystemException $e)
            {
                return false;
            }
        }
        $this->clearUserData();
        $this->clearUserAuth();

        return true;
    }



    protected function logInSysUser()
    {
        $this->user->setID(UserController::SYSTEM_USER);
        $this->user->setSystem(true);
        $this->user->setAdmin(true);

        return true;
    }

    protected function setNewHash (int $userID, string $hash = null)
    {
        try
        {
            $res = UsersDbHandler::getInstance()->updateHash($userID, $hash);
        }
        catch (SystemException $e)
        {
            $e->writeToSysLogFile();
            $res = new DBResult();
        }

        return $res;
    }

    protected function logInOther (bool $rememberMe = false)
    {
        if ((int)$this->user->getID() > 0)
        {
            if ($this->saveUserAuth($rememberMe))
            {
                $res = $this->setNewHash($this->user->getID(), $this->user->getHash());

                if (!$res->isSuccess())
                {
                    $this->clearUserAuth();

                    return false;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Сохраняем авторизацию пользователя в текущей сессии и в куках, если необходимо запомнить авторизацию
     *
     * @param bool $rememberMe Флаг необходимости запомнить авторизацию
     *
     * @return bool
     */
    protected function saveUserAuth (bool $rememberMe = false)
    {
        $bOk = $this->setSession($this->user->getID(), $this->user->getHash());

        if ($rememberMe)
        {
            $bOk2 = $this->setCookie($this->user->getID(), $this->user->getHash(), $rememberMe);
            $bOk = $bOk && $bOk2;
        }

        return $bOk;
    }

    protected function clearUserAuth ()
    {
        // $this->deleteCookie();
    }
}
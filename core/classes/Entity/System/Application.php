<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

use Ms\Core\Entity\Components\Component;
use Ms\Core\Entity\Components\ComponentDescription;
use Ms\Core\Entity\Db\Connection;
use Ms\Core\Entity\Db\ConnectionPool;
use Ms\Core\Entity\User\Authorizer;
use Ms\Core\Entity\User\User;
use Ms\Core\Entity\User\UserGroupCollection;
use Ms\Core\Exceptions\Classes\InvalidOperationException;
use Ms\Core\Exceptions\Classes\ObjectNotInstanceOfAClassException;
use Ms\Core\Exceptions\ComponentException;
use Ms\Core\Exceptions\Db\DbException;
use Ms\Core\Lib\Tools;

/**
 * Класс Ms\Core\Entity\System\Application
 * Основной объект приложений
 */
class Application extends Multiton
{
    /** @var array Данные о времени загрузки страницы */
    protected $arTimes = [];
    /** @var string Текущий статус загрузки страницы*/
    protected $state = 'ST';
    /** @var ConnectionPool */
    protected $connectionPool = null;
    /** @var ApplicationParametersCollection */
    protected $appParams = null;
    /** @var Breadcrumbs */
    protected $breadcrumbs = null;
    /** @var Server */
    protected $server = null;
    /** @var Settings */
    protected $settings = null;
    /** @var Context */
    protected $context = null;
    /** @var Session */
    protected $session = null;
    /** @var bool */
    protected static $isUtfMode = null;
    /** @var array  */
    protected $arIncludedCSS = [];
    /** @var array  */
    protected $arIncludedJS = [];
    /** @var null|string */
    protected $siteTemplate = null;
    /** @var null|User */
    protected $user = null;
    /** @var null|Authorizer */
    protected $authorizer = null;
    /** @var null|UserGroupCollection */
    protected $userGroups = null;
    /** @var array  */
    protected $arPluginsIncluded = [];
    /** @var CookieController */
    protected $cookieController = null;

    /**
     * Возвращает значение DocumentRoot
     *
     * @return string
     * @unittest
     */
    public function getDocumentRoot ()
    {
        return $this->settings->getDocumentRoot();
    }

    /**
     * Конвертирует строку из PascalCase в snake_case
     *
     * @param string $strPascalCase Строка в формате PascalCase
     *
     * @return string
     * @unittest
     */
    public function convertPascalCaseToSnakeCase(string $strPascalCase)
    {
        return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $strPascalCase));
    }

    /**
     * Конвертирует строку из snake_case в PascalCase
     *
     * @param string $strSnakeCase Строка в формате snake_case
     *
     * @return string
     * @unittest
     */
    public function convertSnakeCaseToPascalCase (string $strSnakeCase)
    {
        $strSnakeCase = strtolower($strSnakeCase);

        if (strpos($strSnakeCase,'_')!==false)
        {
            $arName = explode('_',$strSnakeCase);
            $strPascalCase = '';
            foreach ($arName as $name)
            {
                $strPascalCase.=Tools::setFirstCharToBig($name);
            }
        }
        else
        {
            $strPascalCase = Tools::setFirstCharToBig($strSnakeCase);
        }

        return $strPascalCase;
    }

    /**
     * Возвращает значение параметра приложения
     *
     * @param string $sParameterName Имя параметра
     *
     * @return mixed
     * @unittest
     */
    public function getAppParam(string $sParameterName)
    {
        return $this->appParams->getParameter($sParameterName);
    }

    /**
     * Возвращает объект системных настроек
     *
     * @return Settings
     * @unittest
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Устанавливает объект настроек
     *
     * @return $this
     * @unittest
     */
    public function setSettings ()
    {
        $this->settings = Settings::getInstance();

        return $this;
    }

    /**
     * Устанавливает объек сервер
     *
     * @param array $server
     *
     * @return $this
     * @unittest
     */
    public function setServer (array $server)
    {
        $this->server = (Server::getInstance())->init($server);

        return $this;
    }

    /**
     * Устанавливает объект пользователя
     *
     * @return $this
     * @unittest
     */
    public function setUser ()
    {
        $this->user = new User();

        return $this;
    }

    /**
     * Устанавилвает объект коллекции параметров приложения
     *
     * @return $this
     * @unittest
     */
    public function setApplicationParametersCollection ()
    {
        $this->appParams = new ApplicationParametersCollection();

        return $this;
    }

    /**
     * Устанавливает объект коллекции подключений
     *
     * @return $this
     * @unittest
     */
    public function setConnectionPool ()
    {
        $this->connectionPool = new ConnectionPool();

        return $this;
    }

    /**
     * Создает подключение по умолчанию и подключается к нему
     *
     * @return $this
     * @unittest
     */
    public function setConnectionDefault ()
    {
        try
        {
            $this->getConnectionPool()->getConnection('default')->connect();
        }
        catch (DbException $e)
        {
            die('DB not connected: '.$e->getMessage());
        }

        return $this;
    }

    /**
     * Возвращает объект текущего контекста
     *
     * @return Context
     */
    public function getContext ()
    {
        return $this->context;
    }

    /**
     * Устанавливает объект хлебных крошек
     *
     * @return $this
     * @unittest
     */
    public function setBreadcrumbs ()
    {
        $this->breadcrumbs = Breadcrumbs::getInstance();

        return $this;
    }

    /**
     * Устанавливает объект текущей сессии
     *
     * @return $this
     * @unittest
     */
    public function setSession ()
    {
        $this->session = Session::getInstance();

        return $this;
    }

    /**
     * Устанавливает объект, управляющий куками
     *
     * @return $this
     * @unittest
     */
    public function setCookieController ()
    {
        $cookiePrefix = $this->getSettings()->getCookiePrefix();
        $this->cookieController = new CookieController($cookiePrefix);

        return $this;
    }

    /**
     * Устанавливает объект коллекции групп пользователей
     *
     * @return $this
     * @unittest
     */
    public function setUserGroupsCollection ()
    {
        $this->userGroups = new UserGroupCollection();

        return $this;
    }

    /**
     * Устанавливает объект Авторизатора
     *
     * @return $this
     * @unittest
     */
    public function setAuthorizer ()
    {
        $this->authorizer = new Authorizer($this->user);
        $this->authorizer->checkHttpAuthorize();
        $this->authorizer->loginAttempt();
        // msDebugNoAdmin($_COOKIE);

        return $this;
    }

    /**
     * Инициализирует объект приложений
     *
     * @return Application
     * @unittest
     */
    public function init()
    {
        $this
            ->setSettings()
        ;
        $this->getSettings()->mergeLocalSettings();
        $this
            ->setServer($_SERVER)
            ->setApplicationParametersCollection()
            ->setConnectionPool()
            ->setConnectionDefault()

            ->setBreadcrumbs()
            ->setSession()
            ->setCookieController()
            ->setUser()
            ->setUserGroupsCollection()

            ->setAuthorizer()
        ;

        return $this;
    }

    /**
     * Возвращает объект системного авторизатора
     *
     * @return Authorizer
     * @unittest
     */
    public function getAuthorizer ()
    {
        return $this->authorizer;
    }

    /**
     * Возвращает объект для работы с технологией cookie
     *
     * @return CookieController
     * @unittest
     */
    public function getCookieController ()
    {
        return $this->cookieController;
    }

    /**
     * Устанавливает значение параметра приложения
     *
     * @param string $sParameterName Название параметра
     * @param mixed  $value          Значение параметра
     *
     * @return Application
     * @unittest
     */
    public function setAppParams(string $sParameterName, $value): Application
    {
        $this->appParams->addParameter($sParameterName, $value);

        return $this;
    }

    /**
     * Возвращает коллекцию подключений к БД
     *
     * @return ConnectionPool
     * @unittest
     */
    public function getConnectionPool ()
    {
        return $this->connectionPool;
    }

    /**
     * Возвращает указанное подключение к БД
     *
     * @param string $sConnectionName Имя подключения к БД
     *
     * @return Connection
     */
    public function getConnection (string $sConnectionName = 'default')
    {
        return $this->connectionPool->getConnection($sConnectionName);
    }

    /**
     * Устанавливает данные о загрузке страницы
     *
     * @param string $title Название статуса загрузки страницы
     * @param mixed  $value Время загрузки страницы
     *
     * @return Application
     * @unittest
     */
    public function setTimes($title, $value)
    {
        $this->arTimes[$title] = $value;

        return $this;
    }

    /**
     * Возвращает статус загрузки страницы
     *
     * @access public
     * @return string
     * @unittest
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Устанавливает статус загрузки страницы
     *
     * @param string $state Статус загрузки страницы
     *
     * @return Application
     * @unittest
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Возвращает запрашиваемое время статуса загрузки страницы
     *
     * @param string $title Название статуса загрузки страницы
     *
     * @access public
     * @return bool|mixed
     * @unittest
     */
    public function getTimes($title)
    {
        if (isset($this->arTimes[$title]))
        {
            return $this->arTimes[$title];
        }

        return false;
    }

    /**
     * Возвращает true если сервер работает в utf-8 ражиме. False - в ином случае.
     *
     * @return bool
     * @unittest
     */
    public static function isUtfMode()
    {
/*        if (is_null(static::$isUtfMode))
        {
            static::$isUtfMode = static::getInstance()->getSettings()->isUseUtf8();
        }

        return static::$isUtfMode;*/

        return true;
    }

    /**
     * Добавляет/перезаписывает значение указанного шаблона вывода
     *
     * @param string $view    Название шаблона
     * @param string $content Добавляемое значение
     * @param bool   $add     Флаг добавления true, перезаписи false
     *
     * @return Application
     * @unittest
     */
    public function addBufferContent(string $view, string $content, bool $add = true)
    {
        $buffer = Buffer::getInstance();

        if ($add)
        {
            $buffer->addContent($view,$content);
        }
        else
        {
            $buffer->setContent($view, $content);
        }

        return $this;
    }

    /**
     * Возвращает относительный путь к страницам сайта
     *
     * @param string $fullPath Полный путь относительно корня
     *
     * @return string
     * @unittest
     */
    public function getSitePath($fullPath)
    {
        return str_replace($this->getDocumentRoot(), '', $fullPath);
    }

    /**
     * Добавляет CSS файл к загружаемым на странице файлам стилей
     *
     * @param string $fullPath Путь к файлу CSS от корня
     *
     * @return bool
     * @unittest
     */
    public function addCSS($fullPath)
    {
        if (file_exists($fullPath))
        {
            $path = $this->getSitePath($fullPath);
            if (!in_array($path, $this->arIncludedCSS))
            {
                $this->arIncludedCSS[] = $path;
                $this->addBufferContent(
                    'head_css',
                    '<link href="' . $path . '" type="text/css"  rel="stylesheet" />' . "\n"
                );
            }
            return true;
        }

        return false;
    }

    /**
     * Добавляет JS файл к загружаемым на странице скриптам
     *
     * @param string $fullPath Путь к файлу JS от корня
     *
     * @return bool
     * @unittest
     */
    public function addJS($fullPath)
    {
        if (file_exists($fullPath))
        {
            $path = $this->getSitePath($fullPath);
            if (!in_array($path, $this->arIncludedJS))
            {
                $this->arIncludedJS[] = $path;
                $this->addBufferContent(
                    'head_js',
                    '<script type="text/javascript" src="' . $path . '"></script>' . "\n"
                );
            }

            return true;
        }

        return false;
    }

    /**
     * Добавляет произвольный JS код в нижнюю часть страницы
     *
     * @param string $code Добавляемый код JS
     *
     * @return Application
     * @unittest
     */
    public function addJsToDownPage($code)
    {
        $this->addBufferContent('down_js', $code . "\n");

        return $this;
    }

    /**
     * Сохраняет буфер в указанный шаблон вывода и удаляет его
     *
     * @param string $view Название шаблона
     *
     * @return Application
     * @unittest
     */
    public function cleanBufferContent($view)
    {
        $buffer = Buffer::getInstance();

        try
        {
            $buffer->cleanBufferContent($view);
        }
        catch (InvalidOperationException $e)
        {
            die($e->showException());
        }

        return $this;
    }

    /**
     * Меняет кодировку строки
     *
     * @param string $string
     * @param string $from
     * @param string $to
     *
     * @return false|string
     * @unittest
     */
    public function convertCharset (string $string, string $from, string $to)
    {
        return iconv($from, $to, $string);
    }

    /**
     * Сохраняет буфер в указанный шаблон вывода
     *
     * @param string $view Название шаблона
     *
     * @return $this
     * @unittest
     */
    public function endBufferContent($view)
    {
        $buffer = Buffer::getInstance();
        try
        {
           $buffer->endBufferContent($view);
        }
        catch (InvalidOperationException $e)
        {
            die($e->showException());
        }

        return $this;
    }

    /**
     * Завершает буферизацию вывода и выводит буфер страницы
     *
     * @return $this
     * @unittest
     */
    public function endBufferPage()
    {
        Buffer::getInstance()->endBufferPage();

        return $this;
    }

    /**
     * Возвращает объект хлебных крошек
     *
     * @return Breadcrumbs
     * @unittest
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    /**
     * Возвращает объект текущей сессии
     *
     * @return Session
     * @unittest
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Возвращает объект сервера
     *
     * @return Server
     * @unittest
     */
    public function getServer ()
    {
        return $this->server;
    }

    /**
     * Возвращает ближайший к текущему каталогу файл, переходя выше по дереву документов
     * Если файл не был найдет, возвращает false
     *
     * @param string $filename Имя файла
     *
     * @access public
     * @return bool|string
     * @unittest
     */
    public function getNearestFile($filename)
    {
        $url = $this->getServer()->getRequestUri();
        $arUrl = explode('/', $url);
        foreach ($arUrl as $i => $r)
        {
            if (strlen($r) == 0)
            {
                unset ($arUrl[$i]);
            }
        }
        $docRoot = $this->getDocumentRoot();
        while (!empty($arUrl))
        {
            $checkUrl = $docRoot;
            foreach ($arUrl as $u)
            {
                $checkUrl .= '/' . $u;
            }
            $checkUrl .= '/' . $filename;
            if (file_exists($checkUrl))
            {
                return $checkUrl;
            }
            array_pop($arUrl);
        }

        return false;
    }

    /**
     * Обрабатывает собранный буфер страницы и возвращает его
     *
     * @param string $pageBuffer Буфер страницы
     *
     * @return mixed
     */
    public function getPage($pageBuffer)
    {
        $buffer = Buffer::getInstance();

        return $buffer->getPage($pageBuffer);
    }

    /**
     * Возвращает текущий url
     *
     * @access public
     * @uses   Server::getRequestUri
     * @return null|string
     * @unittest
     */
    public function getRequestUrl()
    {
        return $this->getServer()->getRequestUri();
    }

    /**
     * Возвращает шаблон для текущего раздела
     *
     * @access public
     * @return string
     * @unittest
     */
    public function getSiteTemplate()
    {
        if (!is_null($this->siteTemplate))
        {
            return $this->siteTemplate;
        }

        $template = Settings::getInstance()->getTemplate();
        $url = $this->getServer()->getRequestUri();
        $arUrl = explode('/', $url);
        foreach ($arUrl as $i => $r)
        {
            if (strlen($r) == 0)
            {
                unset ($arUrl[$i]);
            }
        }
        $docRoot = $this->getDocumentRoot();
        while (!empty($arUrl))
        {
            $checkUrl = $docRoot;
            foreach ($arUrl as $u)
            {
                $checkUrl .= '/' . $u;
            }
            $checkUrl .= '/.template.php';
            if (file_exists($checkUrl))
            {
                $templateTmp = include($checkUrl);
                if (file_exists($this->settings->getTemplatesRoot() . '/' . $templateTmp . '/header.php'))
                {
                    return $templateTmp;
                }
            }
            array_pop($arUrl);
        }

        return $template;
    }

    /**
     * Позволяет установить произвольный шаблон для страницы.
     * Для сброса вызывается без параметра
     *
     * @param null|string $templateName Имя шаблона, либо null
     * @unittest
     */
    public function setSiteTemplate($templateName = null)
    {
        $this->siteTemplate = $templateName;
    }

    /**
     * Возвращает объект пользователя, если не инициализирован, инициализирует
     *
     * @access public
     * @return User
     * @unittest
     */
    public function getUser()
    {
        if (is_null($this->user))
        {
            $this->setUser();
        }

        return $this->user;
    }

    /**
     * Подключает указанный компонент
     *
     * @param string    $component         Метка компонента (namespace:componentName)
     * @param string    $componentTemplate Шаблон компонента
     * @param array     $componentParams   Массив параметров компонента
     * @param Component $parentComponent   Родительский компонент, вызвавший этот
     *
     * @return string|Component Строка с ошибкой, либо объект компонента
     * @unittest
     */
    public function includeComponent(
        $component,
        $componentTemplate = '',
        $componentParams = [],
        Component $parentComponent = null
    ) {
        list($namespace, $componentName) = explode(':', $component);
        $componentsRoot = $this->settings->getComponentsRoot() . '/'
                          . $namespace . '/' . $componentName;
        if (!file_exists($componentsRoot))
        {
            try
            {
                throw new ComponentException(
                    $component,
                    'Ошибка подключения компонента: Компонент не существует'
                );
            }
            catch (ComponentException $e)
            {
                $e->writeToSysLogFile();
                $e->showException();
            }

            return 'Ошибка подключения компонента "' . $component . '" (компонент не существует)<br>';
        }
        if (file_exists($componentsRoot . '/.description.php'))
        {
            $componentDescription = include($componentsRoot . '/.description.php');
            if (!($componentDescription instanceof ComponentDescription))
            {
                try
                {
                    throw new ObjectNotInstanceOfAClassException('\Ms\Core\Entity\Components\ComponentDescription');
                }
                catch (ObjectNotInstanceOfAClassException $e)
                {
                    $e->writeToSysLogFile();
                    $e->showException();

                    return 'Ошибка подключения компонента "' . $component . '" (отсутствует имя класса)<br>';

                }
            }
            $componentClassName = $componentDescription->getClassName();
        }
        else
        {
            try
            {
                throw new ComponentException(
                    $component,
                    'Ошибка подключения компонента: Отсутствует файл .description.php'
                );
            }
            catch (ComponentException $e)
            {
                $e->writeToSysLogFile();
                $e->showException();
            }

            return 'Ошибка подключения компонента "' . $component . '" (отсутствует файл .description.php)<br>';
        }

        if (file_exists($componentsRoot . '/class.php'))
        {
            include_once($componentsRoot . '/class.php');

            return new $componentClassName($component, $componentTemplate, $componentParams, $parentComponent);
        }
        else
        {
            try
            {
                throw new ComponentException(
                    $component,
                    'Ошибка подключения компонента: Отсутствует файл class.php'
                );
            }
            catch (ComponentException $e)
            {
                $e->writeToSysLogFile();
                $e->showException();
            }

            return 'Ошибка подключения компонента "' . $component . '" (отсутствует файл class.php)<br>';
        }
    }

    /**
     * Подключает указанный плагин
     *
     * @param string $pluginName Имя плагина
     *
     * @return bool
     * @unittest
     */
    public function includePlugin($pluginName = null)
    {
        if (!is_null($pluginName) && !isset($this->arPluginsIncluded[$pluginName]))
        {
            $pluginName = str_replace('-', '_', strtolower($pluginName));
            if (file_exists($this->settings->getMsRoot() . '/plugins/' . $pluginName . '.php'))
            {
                $bInc = include($this->settings->getMsRoot() . '/plugins/' . $pluginName . '.php');
                if ($bInc === true)
                {
                    $this->arPluginsIncluded[$pluginName] = true;

                    return true;
                }
            }

            return false;
        }
        elseif (!is_null($pluginName))
        {
            return true;
        }

        return false;
    }

    /**
     * Устанавливает Refresh для страницы. Отложенная функция
     *
     * @param string $url  Путь, куда будет произведен refresh
     * @param int    $time Время в секундах
     *
     * @return Application
     * @unittest
     */
    public function setRefresh($url = '', $time = 0)
    {
        Buffer::getInstance()->setRefresh($url, $time);

        return $this;
    }

    /**
     * Устанавливает title страницы
     *
     * @param string $title [optional] title страницы, по-умолчанию '' (без названия)
     *
     * @return Application
     * @unittest
     */
    public function setTitle($title = '')
    {
        $this->addBufferContent('page_title', $title, false);

        return $this;
    }

    /**
     * Добавляет шаблон вывода на страницу
     *
     * @param string $view Название шаблона
     *
     * @return Application
     * @unittest
     */
    public function showBufferContent($view)
    {
        Buffer::getInstance()->showBufferContent($view);

        return $this;
    }

    /**
     * Выводит шаблон для размещения кода JS внизу страницы
     * @unittest
     */
    public function showDownJs()
    {
        $this->showBufferContent('down_js');
    }

    /**
     * Выводит meta-теги на странице
     * @unittest
     */
    public function showMeta()
    {
        $this->showBufferContent('refresh');
        $this->showBufferContent('head_css');
        $this->showBufferContent('head_js');
        $this->showBufferContent('head_script');
    }

    /**
     * Возвращает метку вместо которой будет установлен title страницы
     *
     * @param string $title [optional] Title страницы
     * @unittest
     */
    public function showTitle($title = null)
    {
        if (!is_null($title))
        {
            $this->addBufferContent('page_title', $title, false);
        }

        $this->showBufferContent('page_title');
    }

    /**
     * Начинает обработку буфера для указанного шаблона вывода
     *
     * @param string $view Название шаблона
     *
     * @return Application
     * @unittest
     */
    public function startBufferContent($view)
    {
        Buffer::getInstance()->startContent($view);

        return $this;
    }

    /**
     * Стартует буферизацию страницы
     *
     * @return Application
     * @unittest
     */
    public function startBufferPage()
    {
        Buffer::getInstance()->startBufferPage();

        return $this;
    }

    /**
     * Сбрасывает установленный ранее параметр приложения
     *
     * @param string $sParamName Имя параметра
     *
     * @return Application
     * @unittest
     */
    public function unsetAppParam($sParamName)
    {
        if ($this->appParams->issetParameter($sParamName))
        {
            $this->appParams->unsetParameter($sParamName);
        }

        return $this;
    }


}
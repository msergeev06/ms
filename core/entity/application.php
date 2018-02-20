<?php
/**
 * MSergeev\Core\Entity\Application
 * Основной объект приложений
 *
 * @package MSergeev\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace MSergeev\Core\Entity;

use MSergeev\Core\Lib;

/**
 * Class Application
 *
 * @package MSergeev\Core
 * @subpackage Entity
 */
class Application
{
	/**
	 * Объект приложений
	 * @var Application
	 * @access protected
	 * @static
	 */
	protected static $instance = null;

	/**
	 * Флаг подключения основных параметров
	 * @var bool
	 * @access protected
	 */
	protected $isBasicKernelInitialized = false;

	/**
	 * Флаг подключения расширенных параметров
	 * @var bool
	 * @access protected
	 */
	protected $isExtendedKernelInitialized = false;

	/**
	 * Объект базы данных
	 * @var Db\DataBase
	 * @access protected
	 */
	protected $connection;

	/**
	 * Объект контекста
	 * @var Context
	 * @access protected
	 */
	protected $context;

	/**
	 * DocumentRoot сайта
	 * @var string
	 * @access protected
	 */
	protected $documentRoot;

	/**
	 * Текущий статус загрузки страницы
	 * @var string
	 * @access protected
	 */
	protected $state;

	/**
	 * Объект пользователя
	 * @var User
	 * @access protected
	 */
	protected $user;

	/**
	 * Данные о времени загрузки страницы
	 * @var array
	 * @access protected
	 */
	protected $arTimes = array();

	/**
	 * Объект настроек
	 * @var Settings
	 * @access protected
	 */
	protected $settings;

	/**
	 * Список подключенных плагинов
	 * @var array
	 * @access private
	 */
	private $arPluginsIncluded = array();

	private $siteTemplate = null;

	/**
	 * Конструктор объекта приложений
	 * @access protected
	 */
	protected function __construct () {}

	/**
	 * Возвращает объект приложений
	 *
	 * @access public
	 * @static
	 * @return Application
	 */
	public static function getInstance()
	{
		if (!isset(static::$instance))
			static::$instance = new static();

		return static::$instance;
	}

	/**
	 * Инициализирует основные параметры объекта приложений
	 *
	 * @access public
	 */
	public function initializeBasicKernel()
	{
		if ($this->isBasicKernelInitialized)
			return;
		$this->isBasicKernelInitialized = true;

		//$this->initializeExceptionHandler();
		//$this->initializeCache();
		$this->createDatabaseConnection();
		$this->initOptions();
		if (!defined('NO_USER_USE') || NO_USER_USE===false)
		{
			$this->logInUser();
		}
	}

	/**
	 * Создает объект базы данных
	 *
	 * @access protected
	 */
	protected function createDatabaseConnection ()
	{
		$this->connection = new Db\DataBase();
	}

	/**
	 * Инициализирует опции
	 *
	 * @access protected
	 */
	protected function initOptions()
	{
		Lib\Options::init();
	}

	/**
	 * Создает объект пользователя
	 *
	 * @access protected
	 */
	protected function logInUser()
	{
		$this->user = new User();
	}

	/**
	 * Возвращает объект базы данных, если инициализирован, либо false
	 *
	 * @access public
	 * @return bool|Db\DataBase
	 */
	public function getConnection ()
	{
		if (!is_null($this->connection))
		{
			return $this->connection;
		}

		return false;
	}

	/**
	 * Возвращает объект пользователя, если инициализирован, либо false
	 *
	 * @access public
	 * @return bool|User
	 */
	public function getUser()
	{
		if (!is_null($this->user))
			return $this->user;

		return false;
	}

	/**
	 * Инициализирует расширенные параметры
	 *
	 * @param array $params Массив параметров
	 * @access public
	 */
	public function initializeExtendedKernel(array $params)
	{
		if ($this->isExtendedKernelInitialized)
			return;
		$this->isExtendedKernelInitialized = true;

		$this->initializeContext($params);

	}

	/**
	 * Устанавливает данные о загрузке страницы
	 *
	 * @param string $title Название статуса загрузки страницы
	 * @param mixed  $value Время загрузки страницы
	 * @access public
	 */
	public function setTimes ($title, $value)
	{
		$this->arTimes[$title] = $value;
	}

	/**
	 * Возвращает запрашиваемое время статуса загрузки страницы
	 *
	 * @param string $title Название статуса загрузки страницы
	 *
	 * @access public
	 * @return bool|mixed
	 */
	public function getTimes ($title)
	{
		if (isset($this->arTimes[$title]))
		{
			return $this->arTimes[$title];
		}

		return false;
	}

	/**
	 * Устанавливает статус загрузки страницы
	 *
	 * @param string $state Статус загрузки страницы
	 * @access public
	 */
	public function setState ($state)
	{
		$this->state = $state;
	}

	/**
	 * Возвращает статус загрузки страницы
	 *
	 * @access public
	 * @return string
	 */
	public function getState ()
	{
		return $this->state;
	}

	/**
	 * Возвращает DocumentRoot сайта, ища информацию в различных источниках
	 *
	 * @access public
	 * @return string
	 */
	public function getDocumentRoot ()
	{
		if (isset($this->documentRoot))
			return $this->documentRoot;

		if ($_SERVER['DOCUMENT_ROOT']=='')
		{
			$this->documentRoot = '/var/www';
		}
		else
		{
			$this->documentRoot = $_SERVER['DOCUMENT_ROOT'];
		}

		$arSettings = $this->readSettings();

		if (isset($arSettings['Site']['DocumentRoot']))
		{
			$this->documentRoot = $arSettings['Site']['DocumentRoot'];
		}

		return $this->documentRoot;
	}

	/**
	 * Инициализирует объект настроек
	 *
	 * @access public
	 */
	public function loadSettings ()
	{
		$arSettings = $this->readSettings();

		$this->settings = new Settings($arSettings);
	}

	/**
	 * Возвращает объект настроек
	 *
	 * @access public
	 * @return Settings
	 */
	public function getSettings ()
	{
		return $this->settings;
	}

	/**
	 * Возвращает относительный путь к страницам сайта
	 *
	 * @param string $fullPath Полный путь относительно корня
	 *
	 * @access public
	 * @return string
	 */
	public function getSitePath ($fullPath)
	{
		return str_replace($this->getDocumentRoot(),'',$fullPath);
	}

	/**
	 * Стартует буферизацию страницы. Являетя оберткой функции Lib\Buffer::start
	 *
	 * @param string $name Идентификатор страницы
	 *
	 * @uses Lib\Buffer::start
	 * @access public
	 * @return bool
	 */
	public function startBuffer ($name='page')
	{
		Lib\Buffer::start($name);

		return true;
	}

	/**
	 * Возвращает метку вместо которой будет установлен title страницы
	 * Является оберткой функции Lib\Buffer::showTitle
	 *
	 * @param string $title [optional] Title страницы
	 * @uses Lib\Buffer::showTitle
	 * @access public
	 * @return string
	 */
	public function showTitle($title=null)
	{
		return Lib\Buffer::showTitle($title);
	}

	/**
	 * Устанавливает title страницы
	 * Является оберткой функции Lib\Buffer::setTitle
	 *
	 * @param string $title [optional] title страницы, по-умолчанию '' (без названия)
	 * @uses Lib\Buffer::setTitle
	 * @access public
	 */
	public function setTitle($title='')
	{
		Lib\Buffer::setTitle($title);
	}

	/**
	 * Выводит meta-теги на странице
	 * Является оберткой сразу 3 функций: Lib\Buffet::showRefresh, Lib\Buffer::showCSS, Lib\Buffer::showJS
	 *
	 * @uses Lib\Buffer::showRefresh
	 * @uses Lib\Buffer::showCSS
	 * @uses Lib\Buffer::showJS
	 * @access public
	 *
	 * @return string Метка на страницу
	 */
	public function showMeta ()
	{
		return Lib\Buffer::showRefresh()."\n"
			.Lib\Buffer::showCSS()."\n"
			.Lib\Buffer::showJS();
	}

	/**
	 * Добавляет JS файл к загружаемым на странице скриптам
	 * Является оберткой функции Lib\Buffer::addJS
	 *
	 * @param string $file Путь к файлу JS от корня
	 * @uses Lib\Buffer::addJS
	 * @access public
	 * @return bool
	 */
	public function addJS ($file)
	{
		if (file_exists($file))
		{
			Lib\Buffer::addJS($file);

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Добавляет CSS файл к загружаемым на странице файлам стилей
	 * Является оберткой функции Lib\Buffer::addCSS
	 *
	 * @param string $file Путь к файлу CSS от корня
	 * @uses Lib\Buffer::addCSS
	 * @access public
	 * @return bool
	 */
	public function addCSS ($file)
	{
		if (file_exists($file))
		{
			Lib\Buffer::addCSS($file);

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Добавляет произвольный JS код в нижнюю часть страницы
	 * Является оберткой функции Lib\Buffer::addJsToDownPage
	 *
	 * @param string $code Добавляемый код JS
	 * @uses Lib\Buffer::addJsToDownPage
	 * @access public
	 * @return bool
	 */
	public function addJsToDownPage ($code=null)
	{
		Lib\Buffer::addJsToDownPage($code);

		return true;
	}

	/**
	 * Пожключает указанный плагин
	 *
	 * @param string $pluginName Имя плагина
	 * @access public
	 * @return bool
	 */
	public function includePlugin ($pluginName=null)
	{
		if (!is_null($pluginName) && !isset($this->arPluginsIncluded[$pluginName]))
		{
			$pluginName=str_replace('-','_',strtolower($pluginName));
			if (file_exists($this->settings->getMsRoot().'/plugins/'.$pluginName.'.php'))
			{
				$bInc = include($this->settings->getMsRoot().'/plugins/'.$pluginName.'.php');
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
	 * Возвращает шаблон для текущего раздела
	 *
	 * @access public
	 * @return string
	 */
	public function getSiteTemplate ()
	{
		if (!is_null($this->siteTemplate))
		{
			return $this->siteTemplate;
		}

		$template = $this->settings->getTemplate();
		$url = $this->context->getServer()->getRequestUri();
		$arUrl = explode('/',$url);
		foreach ($arUrl as $i=>$r)
		{
			if (strlen($r)==0)
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
				$checkUrl.='/'.$u;
			}
			$checkUrl.='/.template.php';
			if (file_exists($checkUrl))
			{
				$templateTmp = include($checkUrl);
				if (file_exists($this->settings->getTemplatesRoot().'/'.$templateTmp.'/header.php'))
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
	 */
	public function setSiteTemplate ($templateName=null)
	{
		$this->siteTemplate = $templateName;
	}

	/**
	 * Возвращает ближайший к текущему каталогу файл, переходя выше по дереву документов
	 * Если файл не был найдет, возвращает false
	 *
	 * @param string $filename Имя файла
	 *
	 * @access public
	 * @return bool|string
	 */
	public function getNearestFile ($filename)
	{
		$url = $this->context->getServer()->getRequestUri();
		$arUrl = explode('/',$url);
		foreach ($arUrl as $i=>$r)
		{
			if (strlen($r)==0)
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
				$checkUrl.='/'.$u;
			}
			$checkUrl.='/'.$filename;
			if (file_exists($checkUrl))
			{
				return $checkUrl;
			}
			array_pop($arUrl);
		}

		return false;
	}

	/**
	 * Возвращает текущий url
	 *
	 * @access public
	 * @uses Server::getRequestUri
	 * @return null|string
	 */
	public function getRequestUrl ()
	{
		return $this->context->getServer()->getRequestUri();
	}

	/**
	 * Возвращает метку установки кода плагина Webix
	 *
	 * @access public
	 * @return string
	 */
	public function showWebixJS()
	{
		return Lib\Buffer::showWebixJS();
	}

	/**
	 * Завершает буферизацию вывода и выводит результат
	 * Явдяется оберткой функции Lib\Buffer::end
	 *
	 * @uses Lib\Buffer::end
	 * @access public
	 */
	public function endBuffer()
	{
		Lib\Buffer::end();
	}

	/**
	 * Подключает указанный компонент
	 *
	 * @param string $component         Метка компонента (namespace:componentName)
	 * @param string $componentTemplate Шаблон компонента
	 * @param array  $componentParams   Массив параметров компонента
	 *
	 * @access public
	 * @return string|Component Строка с ошибкой, либо объект компонента
	 */
	public function includeComponent($component, $componentTemplate='', $componentParams=array())
	{
		list($namespace,$componentName) = explode(':',$component);
		$componentsRoot = $this->settings->getComponentsRoot().'/'
			.$namespace.'/'.$componentName;
		$componentClassName = '';
		if (!file_exists($componentsRoot))
		{
			echo '<span style="color:red;">Ошибка подключения компонента "'.$component.'" (компонент не существует)</span><br>';
			return 'Ошибка подключения компонента "'.$component.'" (компонент не существует)<br>';
		}
		if (file_exists($componentsRoot.'/.description.php'))
		{
			$arDescription = include($componentsRoot.'/.description.php');
			if (isset($arDescription['CLASS_NAME']))
			{
				$componentClassName = $arDescription['CLASS_NAME'];
			}
			else
			{
				echo '<span style="color:red;">Ошибка подключения компонента "'.$component.'" (отсутствует имя класса)</span><br>';
				return 'Ошибка подключения компонента "'.$component.'" (отсутствует имя класса)<br>';
			}
		}
		else
		{
			echo '<span style="color:red;">Ошибка подключения компонента "'.$component.'" (отсутсвует файл .description.php)</span><br>';
			return 'Ошибка подключения компонента "'.$component.'" (отсутсвует файл .description.php)<br>';
		}

		if (file_exists($componentsRoot.'/class.php'))
		{
			include_once ($componentsRoot.'/class.php');

			return new $componentClassName($component,$componentTemplate,$componentParams);
		}
		else
		{
			echo '<span style="color:red;">Ошибка подключения компонента "'.$component.'" (отсутствует файл class.php)</span><br>';
			return 'Ошибка подключения компонента "'.$component.'" (отсутствует файл class.php)<br>';
		}

	}

	/**
	 * Читает файл настроек из CoreRoot, дополняет из LocalRoot и из DocumentRoot
	 *
	 * @access protected
	 * @return array|mixed
	 */
	protected function readSettings ()
	{
		$arSettings = array();

		if (file_exists($this->getDocumentRoot().'/ms/.settings.php'))
		{
			$arSettings = include($this->getDocumentRoot().'/ms/.settings.php');
		}

		if (file_exists($this->getDocumentRoot().'/local/.settings.php'))
		{
			$arTemp = include($this->getDocumentRoot().'/local/.settings.php');
			$arSettings = static::mergeSettings($arSettings,$arTemp);
		}

		if (file_exists($this->getDocumentRoot().'/.settings.php'))
		{
			$arTemp = include($this->getDocumentRoot().'/.settings.php');
			$arSettings = static::mergeSettings($arSettings,$arTemp);
		}

		return $arSettings;
	}

	/**
	 * Объединяет файлы настроек
	 *
	 * @param array $arSettings - текущий массив настроек
	 * @param array $arMerge - новые настройки
	 *
	 * @return array
	 */
	protected function mergeSettings (array $arSettings, array $arMerge)
	{
		if (!empty($arMerge))
		{
			foreach ($arMerge as $section=>$arr)
			{
				if (is_array($arr) && !empty($arr))
				{
					foreach ($arr as $setting=>$value)
					{
						$arSettings[$section][$setting] = $value;
					}
				}
			}
		}

		return $arSettings;
	}

	/**
	 * Создает объект контекста и объект сервера
	 *
	 * @access protected
	 * @param array $params
	 */
	protected function initializeContext(array $params)
	{
		$context = new Context($this);

		$server = new Server($params["server"]);

		$request = new HttpRequest(
			$server,
			$params["get"],
			$params["post"],
			$params["files"],
			$params["cookie"]
		);

		//$response = new HttpResponse($context);

		$context->initialize($request, $server, array('env' => $params["env"]));

		$this->setContext($context);
	}

	/**
	 * Устанавливает объект контекст
	 *
	 * @param Context $context Объект контекста
	 * @access protected
	 */
	protected function setContext (Context $context)
	{
		$this->context = $context;
	}

	/**
	 * Возвращает объект контекста
	 *
	 * @access public
	 * @return Context
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Устанавливает Refresh для страницы. Отложенная функция
	 *
	 * @param string $url   Путь, куда будет произведен refresh
	 * @param int    $time  Время в секундах
	 * @access public
	 */
	public function setRefresh ($url='',$time=0)
	{
		Lib\Buffer::setRefresh($url,$time);
	}

	/**
	 * Возвращает true если сервер работает в utf-8 ражиме. False - в ином случае.
	 *
	 * @access public
	 * @return bool
	 */
	public static function isUtfMode()
	{
		static $isUtfMode = null;
		if ($isUtfMode === null)
		{
			$isUtfMode = static::$instance->getSettings()->useUtf();
		}
		return $isUtfMode;
	}

}
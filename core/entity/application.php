<?php
/**
 * Ms\Core\Entity\Application
 * Основной объект приложений
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity;

use Ms\Core\Exception\InvalidOperationException;
use Ms\Core\Lib;

/**
 * Class Application
 *
 * @package Ms\Core
 * @subpackage Entity
 */
class Application
{
	//<editor-fold defaultstate="collapse" desc="Init variables">
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
	 * Объект сессии
	 * @var Session
	 * @access protected
	 */
	protected $session=null;

	/**
	 * Список подключенных плагинов
	 * @var array
	 * @access private
	 */
	private $arPluginsIncluded = array();

	private $siteTemplate = null;

	private $arBuffer = array();

	private $arStack = array();

	private $arIncludedJS = array();

	private $arIncludedCSS = array();

	/**
	 * @var Breadcrumbs
	 */
	private $breadcrumbs = null;

	private $appParams = array();
	//</editor-fold>

	//<editor-fold defaultstate="collapse" desc="Construct">
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
		{
			static::$instance = new static();
		}

		return static::$instance;
	}
	//</editor-fold>

	//<editor-fold defaultstate="collapse" desc="Инициализация">
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
		$this->breadcrumbs = Breadcrumbs::getInstance();

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

	public function getSession()
	{
		if (is_null($this->session))
		{
			$this->session = new Session();
		}

		return $this->session;
	}

	//</editor-fold>

	//<editor-fold defaultstate="collapse" desc="Пользоатели">
	/**
	 * Создает объект пользователя
	 *
	 * @access protected
	 */
	protected function logInUser()
	{
		$this->user = User::getObject();
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

	//</editor-fold>

	//<editor-fold defaultstate="collapse" desc="Параметры приложения">
	/**
	 * Добавляет параметр приложения
	 *
	 * @param string $sParamName Имя параметра
	 * @param mixed $mValue Значение параметра
	 */
	public function addAppParam ($sParamName, $mValue)
	{
		$this->appParams[strtolower($sParamName)] = $mValue;
	}

	/**
	 * Возвращает значение установленного ранее параметра приложения, либо null
	 *
	 * @param string $sParamName Имя параметра
	 *
	 * @return mixed|null
	 */
	public function getAppParam ($sParamName)
	{
		if (isset($this->appParams[strtolower($sParamName)]))
		{
			return $this->appParams[strtolower($sParamName)];
		}

		return null;
	}

	/**
	 * Сбрасывает установленный ранее параметр приложения
	 *
	 * @param string $sParamName Имя параметра
	 */
	public function unsetAppParam ($sParamName)
	{
		if (isset($this->appParams[strtolower($sParamName)]))
		{
			unset($this->appParams[strtolower($sParamName)]);
		}
	}
	//</editor-fold>

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
			$this->documentRoot = dirname(__FILE__).'/../../../';
		}
		else
		{
			$this->documentRoot = $_SERVER['DOCUMENT_ROOT'];
		}

		$arSettings = $this->readSettings();

		if (isset($arSettings['site']['documentroot']))
		{
			$this->documentRoot = $arSettings['site']['documentroot'];
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
	 * Возвращает объект хлебных крошек
	 *
	 * @return Breadcrumbs
	 */
	public function getBreadcrumbs ()
	{
		return $this->breadcrumbs;
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
	 * Подключает указанный компонент
	 *
	 * @param string    $component          Метка компонента (namespace:componentName)
	 * @param string    $componentTemplate  Шаблон компонента
	 * @param array     $componentParams    Массив параметров компонента
	 * @param Component $parentComponent    Родительский компонент, вызвавший этот
	 *
	 * @access public
	 * @return string|Component Строка с ошибкой, либо объект компонента
	 */
	public function includeComponent($component, $componentTemplate='', $componentParams=array(), Component $parentComponent=null)
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

			return new $componentClassName($component,$componentTemplate,$componentParams, $parentComponent);
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





	//<editor-fold defaultstate="collapse" desc="Работа с буфером вывода">
	/**
	 * Стартует буферизацию страницы
	 */
	public function startBufferPage()
	{
		ob_start(array(&$this,'getPage'));
		$this->arBuffer['page_title'] = '';
		$this->arBuffer['head_css'] = '';
		$this->arBuffer['head_js'] = '';
		$this->arBuffer['head_script'] = '';
	}

	/**
	 * Завершает буферизацию вывода и выводит буфер страницы
	 */
	public function endBufferPage ()
	{
		if (defined('SHOW_SQL_WORK_TIME') && SHOW_SQL_WORK_TIME === true)
		{
			$DB = $this->getConnection();
			echo '<div style="border: 1px solid black; background-color: white; padding: 10px;">';
			echo '<p>'.$DB->getCountQuery().' '.Lib\Tools::sayRusRight($DB->getCountQuery(),'запрос','запроса','запросов').' за '.$DB->getAllQueryTime().' сек.</p><hr>';
			$arLogs = $DB->getSqlLogs();
			foreach($arLogs as $hash=>$sql)
			{
				echo '<p>',$hash,':<br><pre>',$sql['SQL'],'</pre></p><hr>';
			}
			echo "</div><br>";

		}
		Lib\Events::runEvents(
			'core',
			'OnEndBufferPage',
			array('BUFFER'=>&$this->arBuffer)
		);
		ob_end_flush();
	}

	/**
	 * Обрабатывает собранный буфер страницы и возвращает его
	 *
	 * @param string $buffer Буфер страницы
	 *
	 * @return mixed
	 */
	public function getPage ($buffer)
	{
		if (!empty($this->arBuffer))
		{
			foreach ($this->arBuffer as $name=>$buff)
			{
				if ($name == "head_script")
				{
					$buff = "<script>\n".$buff."\n</script>\n";
				}
				$buffer = str_replace('#'.strtoupper($name).'#',$buff,$buffer);
			}
		}

		$buffer = preg_replace('/[#]{1}[A-Z0-9_]+[#]{1}/','',$buffer);
		$buffer = preg_replace('/[%]{1}([A-Z0-9_]+)[%]{1}/','#$1#',$buffer);

		return $buffer;
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
		if ($url!='')
		{
			//Если работает в windows (denwer)
			if (strpos ($url,'\\')!==false)
			{
				list(,$url) = explode('www',$url);
				$url = str_replace('\\','/',$url);
			}
			//TODO: Разобраться, что тут происходит
			$url = str_replace(Application::getInstance()->getSettings()->getSiteProtocol(),'',$url);
			$url = str_replace('://','',$url);
			$url = str_replace(Application::getInstance()->getContext()->getServer()->getHttpHost(),'',$url);
			$url = $this->getSitePath($url);
		}
		else
		{
			$url = Application::getInstance()->getContext()->getServer()->getRequestUri();
		}
		$this->arBuffer['refresh'] = '<META HTTP-EQUIV=REFRESH CONTENT="'
			.(int)$time
			.'; URL='
			.'//'.Application::getInstance()->getContext()->getServer()->getHttpHost().$url
			.'">';
	}

	/**
	 * Добавляет шаблон вывода на страницу
	 *
	 * @param string $view Название шаблона
	 */
	public function showBufferContent($view)
	{
		if (!isset($this->arBuffer[strtolower($view)]))
		{
			$this->arBuffer[strtolower($view)] = '';
		}

		echo '#',strtoupper($view),'#';
	}

	/**
	 * Добавляет/перезаписывает значение указанного шаблона вывода
	 *
	 * @param string $view Название шаблона
	 * @param string $content Добавляемое значение
	 * @param bool $add Флаг добавления true, перезаписи false
	 */
	public function addBufferContent ($view, $content, $add=true)
	{
		if (!isset($this->arBuffer[strtolower($view)]))
		{
			$this->arBuffer[strtolower($view)] = '';
		}

		if ($add)
		{
			$this->arBuffer[strtolower($view)] .= $content;
		}
		else
		{
			$this->arBuffer[strtolower($view)] = $content;
		}
	}

	/**
	 * Начинает обработку буфера для указанного шаблона вывода
	 *
	 * @param string $view Название шаблона
	 */
	public function startBufferContent($view)
	{
		ob_start();
		$this->arStack[] = strtolower($view);
	}

	/**
	 * Сохраняет буфер в указанный шаблон вывода
	 *
	 * @param string $view Название шаблона
	 */
	public function endBufferContent ($view)
	{
		try
		{
			$stack = array_pop($this->arStack);
			if ($stack == $view)
			{
				$buffer = ob_get_flush();
				$this->arBuffer[$view] = $buffer;
			}
			else
			{
				throw new InvalidOperationException('Wrong name of view content');
			}
		}
		catch (InvalidOperationException $e)
		{
			die($e->showException());
		}
	}

	/**
	 * Сохраняет буфер в указанный шаблон вывода и удаляет его
	 *
	 * @param string $view Название шаблона
	 */
	public function cleanBufferContent ($view)
	{
		try
		{
			$stack = array_pop($this->arStack);
			if ($stack == $view)
			{
				$buffer = ob_get_clean();
				$this->arBuffer[$view] = $buffer;
			}
			else
			{
				throw new InvalidOperationException('Wrong name of view content');
			}
		}
		catch (InvalidOperationException $e)
		{
			die($e->showException());
		}
	}

	/**
	 * Возвращает метку вместо которой будет установлен title страницы
	 *
	 * @param string $title [optional] Title страницы
	 */
	public function showTitle($title=null)
	{
		if (!is_null($title))
		{
			$this->addBufferContent('page_title',$title,false);
		}

		$this->showBufferContent('page_title');
	}

	/**
	 * Устанавливает title страницы
	 *
	 * @param string $title [optional] title страницы, по-умолчанию '' (без названия)
	 */
	public function setTitle($title='')
	{
		$this->addBufferContent('page_title',$title,false);
	}

	/**
	 * Выводит meta-теги на странице
	 */
	public function showMeta ()
	{
		$this->showBufferContent('refresh');
		$this->showBufferContent('head_css');
		$this->showBufferContent('head_js');
		$this->showBufferContent('head_script');
	}

	/**
	 * Добавляет JS файл к загружаемым на странице скриптам
	 *
	 * @param string $fullPath Путь к файлу JS от корня
	 *
	 * @return bool
	 */
	public function addJS ($fullPath)
	{
		if (file_exists($fullPath))
		{
			$path = $this->getSitePath($fullPath);
			if (!in_array($path,$this->arIncludedJS))
			{
				$this->arIncludedJS[] = $path;
				$this->addBufferContent(
					'head_js',
					'<script type="text/javascript" src="'.$path.'"></script>'."\n"
				);
			}
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Добавляет CSS файл к загружаемым на странице файлам стилей
	 *
	 * @param string $fullPath Путь к файлу CSS от корня
	 */
	public function addCSS ($fullPath)
	{
		if (file_exists($fullPath))
		{
			$path = $this->getSitePath($fullPath);
			if (!in_array($path,$this->arIncludedCSS))
			{
				$this->arIncludedCSS[] = $path;
				$this->addBufferContent(
					'head_css',
					'<link href="'.$path.'" type="text/css"  rel="stylesheet" />'."\n"
				);
			}
		}
	}

	/**
	 * Добавляет произвольный JS код в нижнюю часть страницы
	 *
	 * @param string $code Добавляемый код JS
	 */
	public function addJsToDownPage ($code)
	{
		$this->addBufferContent('down_js',$code."\n");
	}

	/**
	 * Выводит шаблон для размещения кода JS внизу страницы
	 */
	public function showDownJs ()
	{
		$this->showBufferContent('down_js');
	}
	//</editor-fold>
}
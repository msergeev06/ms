<?php
/**
 * Ms\Core\Entity\Component
 * Основной объект компонентов. Все компоненты наследуют его или его потомков
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity;
use Ms\Core\Exception;

/**
 * Class Component
 * @package Ms\Core
 * @subpackage Entity
 */
abstract class Component
{
	/**
	 * Namespace компонента
	 * @var string
	 * @access protected
	 */
	protected $namespace;

	/**
	 * Имя компонента
	 * @var string
	 * @access protected
	 */
	protected $componentName;

	/**
	 * Путь от корня к компонентам
	 * @var string
	 * @access protected
	 */
	protected $componentsRoot;

	/**
	 * Путь от корня к шаблонам
	 * @var string
	 * @access protected
	 */
	protected $templatesRoot;

	/**
	 * Шаблон по-умолчанию
	 * @var string
	 * @access protected
	 */
	protected $siteTemplate;

	/**
	 * Имя шаблона компонента
	 * @var string
	 * @access protected
	 */
	protected $componentTemplate;
	/**
	 * Массив объектов Параметры компонента
	 * @var ComponentParameter[]
	 * @access protected
	 */
	protected $arComponentParams;

	/**
	 * Массив необработанных параметров компонента
	 * @var array
	 * @access protected
	 */
	protected $arRawParams;

	/**
	 * Массив значений параметров компонента
	 * @var array
	 * @access public
	 */
	public $arParams = array();

	/**
	 * Массив результата работы компонента
	 * @var array
	 * @access public
	 */
	public $arResult = array();

	/**
	 * Конструктор компонента
	 *
	 * @param string $component Namespace и название компонента, в виде namespace:componentName
	 * @param string $template  Используемый шаблон компонента
	 * @param array  $arParams  Массив значений параметров компонента
	 */
	public function __construct ($component, $template='.default', $arParams=array())
	{
		//Инициализируем основные параметры компонента
		$this->arRawParams = $arParams;

		list($this->namespace,$this->componentName) = explode(':',$component);
		$this->componentsRoot = Application::getInstance()->getSettings()->getComponentsRoot();
		$this->templatesRoot = Application::getInstance()->getSettings()->getTemplatesRoot();
		$this->siteTemplate = Application::getInstance()->getSiteTemplate();
		if ($template == '') $template = '.default';
		$this->componentTemplate = $template;
		//Обрабатываем параметры компонента
		$this->initParams();
		//Вызываем основную функцию компонента (запускаем компонент)
		$this->run();
	}

	/**
	 * Функция проверяет наличие файла параметров компонента и при его наличии выполняет метод обработки
	 * @uses Component::loadParameters
	 * @access protected
	 */
	protected function initParams ()
	{
		if (file_exists($this->componentsRoot.'/'.$this->namespace.'/'.$this->componentName.'/.parameters.php'))
		{
			$this->loadParameters($this->componentsRoot.'/'.$this->namespace.'/'.$this->componentName.'/.parameters.php');
		}
	}

	/**
	 * Функция принимает данные из файла параметров компонента и формирует массив значений параметров
	 * исходя из переданного массива значений
	 *
	 * @param string $path Путь к файлу параметров компонента
	 */
	private function loadParameters ($path)
	{
		$arComponentParams = include($path);
		if (!empty($arComponentParams))
		{
			foreach($arComponentParams as $code=>$ar_params)
			{
				if (isset($this->arRawParams[$code]))
				{
					$value = $this->arRawParams[$code];
				}
				else
				{
					$value = null;
				}
				$this->arComponentParams[$code] = new ComponentParameter($code,$ar_params,$value);
				$this->arParams[$code] = $this->arComponentParams[$code]->getValue();
			}
		}
	}

	/**
	 * Основная функция запуска компонента. Должна быть обязательно переопределена.
	 */
	abstract public function run ();

	/**
	 * Возвращает путь к текущему шаблону компонента.
	 * Шаблон компонента, ищется его в следующих местах (в порядке приоритета)
	 * 1. В текущем шаблоне сайта
	 * 2. В шаблоне сайта по-умолчанию
	 * 3. В шаблонах компонента
	 *
	 * @return bool|string Путь до шаблона компонента, либо false
	 */
	public function getTemplatePath ()
	{
		// /ms/templates/[templateSite]/components/[namespace]/[componentName]/[templateComponent]/
		$path = $this->templatesRoot.'/'.$this->siteTemplate.'/components/'
			.$this->namespace.'/'.$this->componentName.'/'
			.$this->componentTemplate;
		if (file_exists($path))
		{
			return $path;
		}

		// /ms/templates/.default/components/[namespace]/[componentName]/[templateComponent]/
		$path = $this->templatesRoot.'/.default/components/'
			.$this->namespace.'/'.$this->componentName.'/'
			.$this->componentTemplate;
		if (file_exists($path))
		{
			return $path;
		}

		// /ms/components/[namespace]/[componentName]/templates/[templateComponent]/
		$path = $this->componentsRoot.'/'.$this->namespace.'/'.$this->componentName
			.'/templates/'.$this->componentTemplate;
		if (file_exists($path))
		{
			return $path;
		}

		return false;
	}

	/**
	 * Пытается подключить шаблон компонента. Если шаблон не найден, выводит сообщение об ошибке
	 *
	 * @access public
	 * @return mixed|null
	 */
	public function includeTemplate()
	{
		$path = $this->getTemplatePath();
		if ($path !== false)
		{
			return $this->includeTemplateFiles($path);
		}

		echo '<span style="color: red">Ошибка подключения шаблона "'
			.$this->componentTemplate.'" компонента "'.$this->componentName.'". Шаблон не найден!</span><br>';

		return null;
	}

	/**
	 * Метод подключает шаблон компонента при этом подключаются (если существуют) следующие файлы:
	 * 1. Файл параметров шаблона компонента (параметры добавляются к уже существующим параметрам компонента)
	 * 2. Файл изменения логики работы компонента.
	 * 3. Файл стилей style.css
	 * 4. Файл JavaScript script.js
	 * 5. Файл шаблона template.php
	 *
	 * @param string $path Папка шаблона компонента
	 *
	 * @access private
	 * @return mixed|null
	 */
	private function includeTemplateFiles ($path)
	{
		//Если в шаблоне есть дополнительные параметры, загружаем их
		if (file_exists($path.'/.parameters.php'))
		{
			$this->loadParameters($path.'/.parameters.php');
		}

		//Загружаем файл модификации данных, если существует
		if (file_exists($path.'/result_modifier.php'))
		{
			include($path.'/result_modifier.php');
		}

		//Если существуют стили, подключаем их
		if (file_exists($path.'/style.css'))
		{
			Application::getInstance()->addCSS($path.'/style.css');
		}

		//Если существует скрипт js, подключаем его
		if (file_exists($path.'/script.js'))
		{
			Application::getInstance()->addJS($path.'/script.js');
		}

		//Подключаем шаблон
		if (file_exists($path.'/template.php'))
		{
			$return = include($path.'/template.php');
			return $return;
		}

		return null;
	}
}
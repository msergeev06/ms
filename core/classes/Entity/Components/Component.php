<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2017 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Components;

use Ms\Core\Entity\Components\Parameters\ParameterCollection;
use Ms\Core\Entity\Components\Parameters\ParameterGroupCollection;
use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\System\Application;
use Ms\Core\Exceptions\Arguments\ArgumentException;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Exceptions\Db\ValidateException;
use Ms\Core\Tables\UrlrewriteTable;

/**
 * Класс Ms\Core\Entity\Components\Component
 * Основной класс компонентов. Все компоненты наследуют его или его потомков
 */
abstract class Component
{
    /**
     * @var array $arParams Массив значений параметров компонента
     */
    public $arParams = [];
    /**
     * @var array $arResult Массив результата работы компонента
     */
    public $arResult = [];
    /**
     * @var array $arRawParams Массив необработанных параметров компонента
     */
    protected $arRawParams;
    /**
     * @var string $componentName Имя компонента
     */
    protected $componentName;
    /**
     * @var string $componentTemplate Имя шаблона компонента
     */
    protected $componentTemplate;
    /**
     * @var string $componentsRoot Путь от корня к компонентам
     */
    protected $componentsRoot;
    /**
     * @var string $namespace Пространство име компонента
     */
    protected $namespace;
    /**
     * Коллекция параметров компонента и их значений
     *
     * @var \Ms\Core\Entity\Components\Parameters\ParameterCollection $parameterCollection
     */
    protected $parameterCollection;
    /**
     * Коллекция групп параметров компонентов
     *
     * @var \Ms\Core\Entity\Components\Parameters\ParameterGroupCollection $arComponentParams
     */
    protected $parameterGroupCollection;
    /**
     * @var \Ms\Core\Entity\Components\Component|null $parentComponent Родительский компонент
     */
    protected $parentComponent = null;
    /**
     * @var string $siteTemplate Шаблон по-умолчанию
     */
    protected $siteTemplate;
    /**
     * @var string $templatesRoot Путь от корня к шаблонам
     */
    protected $templatesRoot;

    /**
     * Конструктор компонента
     *
     * @param string    $component       Пространство имен и название компонента, в виде namespace:componentName
     * @param string    $template        Используемый шаблон компонента
     * @param array     $arParams        Массив значений параметров компонента
     * @param Component $parentComponent Родительский компонент
     *
     * @throws ArgumentException
     * @unittest
     */
    public function __construct ($component, $template = '.default', $arParams = [], Component $parentComponent = null)
    {
        //Инициализируем основные параметры компонента
        $this->arRawParams = $arParams;
        $this->parameterCollection = new ParameterCollection();

        if (!(strpos($component, ':') !== false))
        {
            throw new ArgumentException(
                'Имя компонента должно быть указано в виде namespace:componentName',
                'component'
            );
        }
        list($this->namespace, $this->componentName) = explode(':', $component);
        $this->componentsRoot = Application::getInstance()->getSettings()->getComponentsRoot();
        $this->templatesRoot = Application::getInstance()->getSettings()->getTemplatesRoot();
        $this->siteTemplate = Application::getInstance()->getSiteTemplate();
        if ($template == '')
        {
            $template = '.default';
        }
        $this->componentTemplate = $template;
        $this->parentComponent = $parentComponent;
        //Обрабатываем параметры компонента
        $this->initParams();
        if (isset($_REQUEST['init_paths']) && $_REQUEST['init_paths'] == 'Y')
        {
            //Проверяем наличие необходимых путей в таблице
            $this->checkUrlRewritePaths();
        }
        if (isset($_REQUEST['show_running_components']) && $_REQUEST['show_running_components'] == 'Y')
        {
            echo get_called_class() . '<br>';
        }
        //Вызываем основную функцию компонента (запускаем компонент)
        $this->run();
    }

    /**
     * Возвращает имя используемого класса компонента
     *
     * @return string
     * @unittest
     */
    public function getClassName ()
    {
        return get_called_class();
    }

    /**
     * Возвращает путь к файлу для подключения шаблона компонента хлебных крошек
     *
     * @param string $sTemplateName Имя шаблона (необязательный)
     *
     * @return null|string
     * @unittest
     */
    public function getIncludeTemplatePath ($sTemplateName = null)
    {
        $path = $this->getTemplatePath($sTemplateName);
        $fileName = (is_null($sTemplateName)) ? 'template' : $sTemplateName;
        if ($path !== false)
        {
            return $path . '/' . $fileName . '.php';
        }

        return null;
    }

    /**
     * Возвращает путь к текущему шаблону компонента.
     * Шаблон компонента, ищется его в следующих местах (в порядке приоритета)
     * 1. В текущем шаблоне сайта
     * 2. В шаблоне сайта по-умолчанию
     * 3. В шаблонах компонента
     *
     * @param string $sTemplateName Имя шаблона
     *
     * @return bool|string Путь до шаблона компонента, либо false
     * @unittest
     */
    public function getTemplatePath ($sTemplateName = null)
    {
        if (is_null($sTemplateName))
        {
            $sTemplateName = 'template';
        }
        // /ms/templates/[templateSite]/components/[namespace]/[componentName]/[templateComponent]/
        $path = $this->templatesRoot . '/' . $this->siteTemplate . '/components/'
                . $this->namespace . '/' . $this->componentName . '/'
                . $this->componentTemplate;
        if (file_exists($path . '/' . $sTemplateName . '.php'))
        {
            return $path;
        }

        // /ms/templates/.default/components/[namespace]/[componentName]/[templateComponent]/
        $path = $this->templatesRoot . '/.default/components/'
                . $this->namespace . '/' . $this->componentName . '/'
                . $this->componentTemplate;
        if (file_exists($path . '/' . $sTemplateName . '.php'))
        {
            return $path;
        }

        // /ms/components/[namespace]/[componentName]/templates/[templateComponent]/
        $path = $this->componentsRoot . '/' . $this->namespace . '/' . $this->componentName
                . '/templates/' . $this->componentTemplate;
        if (file_exists($path . '/' . $sTemplateName . '.php'))
        {
            return $path;
        }

        return false;
    }

    /**
     * Пытается подключить шаблон компонента. Если шаблон не найден, выводит сообщение об ошибке
     *
     * @param string $sTemplateName Имя шаблона (необязательный)
     *
     * @return mixed|null
     * @unittest
     */
    public function includeTemplate ($sTemplateName = null)
    {
        $path = $this->getTemplatePath($sTemplateName);
        if ($path !== false)
        {
            return $this->includeTemplateFiles($path, $sTemplateName);
        }

        echo '<span style="color: red">Ошибка подключения шаблона "'
             . $this->componentTemplate . '" компонента "' . $this->componentName
             . '". Шаблон не найден!</span><br>';

        return null;
    }

    /**
     * Основная функция запуска компонента. Должна быть обязательно переопределена.
     *
     * @unittest
     */
    abstract public function run ();

    protected function checkArParams ()
    {
    }

    /**
     * Возвращает коллекцию путей компонента.
     * Должен быть переопределен в компонентах работающих с более, чем 1 страницей
     *
     * @return ComponentPathCollection
     */
    protected function getPathsCollection ()
    {
        return new ComponentPathCollection();
    }

    /**
     * Добавляет несуществующие в БД пути компонента
     *
     * @param ComponentPathCollection $addCollection
     */
    private function addNewUrlRewritePaths (ComponentPathCollection $addCollection)
    {
        if ($addCollection->isEmpty())
        {
            return;
        }
        $arAdd = [];
        /** @var ComponentPaths $objPath */
        foreach ($addCollection as $objPath)
        {
            $arAdd[] = [
                'COMPONENT_NAME' => $objPath->getComponentFullName(),
                'CONDITION'      => $objPath->getCondition(),
                'RULE'           => $objPath->getRule(),
                'PATH'           => $objPath->getPath()
            ];
        }
        if (!empty($arAdd))
        {
            try
            {
                $orm = ORMController::getInstance(new UrlrewriteTable());
                $orm->insert($arAdd);
            }
            catch (ArgumentNullException $e)
            {
            }
            catch (SqlQueryException $e)
            {
            }
            catch (ValidateException $e)
            {
            }
        }
    }

    /**
     * Проверяет необходимость добавления новых путей компонента. И если она есть - добавляет их
     */
    private function checkUrlRewritePaths ()
    {
        $pathsCollection = $this->getPathsCollection();
        if ($pathsCollection->isEmpty())
        {
            return;
        }
        //		msDebug($pathsCollection);

        $arFilter = ['LOGIC' => 'OR'];
        foreach ($pathsCollection as $objPath)
        {
            if ($objPath instanceof ComponentPaths && $objPath->checkObject())
            {
                $arFilter[] = [
                    'COMPONENT_NAME' => $objPath->getComponentFullName(),
                    'CONDITION'      => $objPath->getCondition(),
                    'RULE'           => $objPath->getRule(),
                    'PATH'           => $objPath->getPath()
                ];
            }
        }
        if (count($arFilter) <= 1)
        {
            return;
        }

        $orm = ORMController::getInstance(new UrlrewriteTable());
        $arRes = $orm->getList(
            [
                'filter' => $arFilter
            ]
        );
        if (!$arRes)
        {
            $this->addNewUrlRewritePaths($pathsCollection);

            return;
        }
        $addCollection = new ComponentPathCollection();
        foreach ($pathsCollection as $objPath)
        {
            $bFind = false;
            foreach ($arRes as $ar_res)
            {
                if (
                    $ar_res['COMPONENT_NAME'] == $objPath->getComponentFullName()
                    && $ar_res['CONDITION'] == $objPath->getCondition()
                    && $ar_res['RULE'] == $objPath->getRule()
                    && $ar_res['PATH'] == $objPath->getPath()
                )
                {
                    $bFind = true;
                    break;
                }
            }
            if (!$bFind)
            {
                $addCollection->addPath($objPath);
            }
        }
        if (!$addCollection->isEmpty())
        {
            $this->addNewUrlRewritePaths($addCollection);
        }
    }

    /**
     * Метод подключает шаблон компонента при этом подключаются (если существуют) следующие файлы:
     * 1. Файл параметров шаблона компонента (параметры добавляются к уже существующим параметрам компонента)
     * 2. Файл изменения логики работы компонента.
     * 3. Файл стилей style.css
     * 4. Файл JavaScript script.js
     * 5. Файл шаблона template.php
     *
     * @param string $path          Папка шаблона компонента
     * @param string $sTemplateName Имя шаблона
     *
     * @access private
     * @return mixed|null
     */
    private function includeTemplateFiles ($path, $sTemplateName = null)
    {
        if (is_null($sTemplateName))
        {
            $sTemplateName = 'template';
        }
        else
        {
            $sTemplateName = str_replace('-', '_', $sTemplateName);
        }

        //Если в шаблоне есть дополнительные параметры, загружаем их
        if (file_exists($path . '/.parameters.php'))
        {
            $this->loadParameters($path . '/.parameters.php', $arRawParams);
        }

        //Загружаем файл модификации данных, если существует
        if (file_exists($path . '/result_modifier.php'))
        {
            include($path . '/result_modifier.php');
        }

        //Если существуют стили, подключаем их
        if (file_exists($path . '/style.css'))
        {
            Application::getInstance()->addCSS($path . '/style.css');
        }

        //Если существует скрипт js, подключаем его
        if (file_exists($path . '/script.js'))
        {
            Application::getInstance()->addJS($path . '/script.js');
        }

        //Подключаем нужный шаблон
        if (file_exists($path . '/' . $sTemplateName . '.php'))
        {
            $return = include($path . '/' . $sTemplateName . '.php');

            return $return;
        }

        return null;
    }

    /**
     * Функция проверяет наличие файла параметров компонента и при его наличии выполняет метод обработки
     *
     * @uses   Component::loadParameters
     */
    private function initParams ()
    {
        $arRawParams = $this->arRawParams;
        $path = $this->componentsRoot . '/'
                . $this->namespace . '/' . $this->componentName . '/.parameters.php';
        if (file_exists($path))
        {
            $this->loadParameters($path, $arRawParams);
        }

        if (!empty($arRawParams))
        {
            foreach ($arRawParams as $code => $value)
            {
                if (!isset($this->arParams[$code]))
                {
                    $this->arParams[$code] = $value;
                }
            }
        }
    }

    /**
     * Функция принимает данные из файла параметров компонента и формирует массив значений параметров
     * исходя из переданного массива значений
     *
     * @param string $path        Путь к файлу параметров компонента
     *
     * @param array  $arRawParams Массив полученных параметров
     *
     * @return Component
     */
    private function loadParameters ($path, &$arRawParams)
    {
        $collection = include($path);

        $arRawParams = $this->arRawParams;
        if ($collection instanceof ParameterGroupCollection && !$collection->isEmpty())
        {
            /** @var \Ms\Core\Entity\Components\Parameters\ParameterGroup $group */
            foreach ($collection as $group)
            {
                if (!$group->isEmpty())
                {
                    /** @var \Ms\Core\Entity\Components\Parameters\Parameter $parameter */
                    foreach ($group as $parameter)
                    {
                        $code = $parameter->getCode();
                        if (isset($arRawParams[$code]))
                        {
                            $parameter->setValue($arRawParams[$code]);
                            unset($arRawParams[$code]);
                        }
                        $this->parameterCollection->addParameter($parameter);
                        $this->arParams[$code] = $parameter->getValue();
                    }
                }
            }
        }

        $this->parameterGroupCollection = $collection;

        return $this;
    }
}
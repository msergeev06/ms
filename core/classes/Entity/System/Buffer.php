<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

use Ms\Core\Entity\Db\QueryInfo;
use Ms\Core\Entity\Events\EventController;
use Ms\Core\Exceptions\Classes\InvalidOperationException;
use Ms\Core\Lib\Tools;

/**
 * Класс Ms\Core\Entity\System\Buffer
 * Управляет буфером вывода.
 */
class Buffer extends Multiton
{
    /** @var array  */
    protected $arBuffer = [];
    /** @var array  */
    protected $arStack = [];

    public function issetView (string $viewName)
    {
        $this->normalizeViewName($viewName);

        return array_key_exists($viewName,$this->arBuffer);
    }

    public function createView (string $viewName)
    {
        $this->normalizeViewName($viewName);

        if (!$this->issetView($viewName))
        {
            $this->arBuffer[$viewName] = '';
        }

        return $this;
    }

    public function addContent (string $viewName, string $content)
    {
        $this
            ->normalizeViewName($viewName)
            ->createView($viewName)
        ;

        $this->arBuffer[$viewName] .= $content;

        return $this;
    }

    public function getContent (string $viewName)
    {
        return isset($this->arBuffer[$viewName]) ? $this->arBuffer[$viewName] : null;
    }

    public function setContent (string $viewName, string $content)
    {
        $this
            ->normalizeViewName($viewName)
            ->createView($viewName)
        ;

        $this->arBuffer[$viewName] = $content;
    }

    public function popStack ()
    {
        return array_pop ($this->arStack);
    }

    /**
     * Сохраняет буфер в указанный шаблон вывода и удаляет его
     *
     * @param string $viewName
     *
     * @throws InvalidOperationException
     */
    public function cleanBufferContent(string $viewName)
    {
        $this->normalizeViewName($viewName);
        $stack = $this->popStack();
        if ($stack == $viewName)
        {
            $buffer = ob_get_clean();
            $this->setContent($viewName,$buffer);
        }
        else
        {
            throw new InvalidOperationException('Wrong name of view content');
        }
    }

    /**
     * Сохраняет буфер в указанный шаблон вывода
     *
     * @param string $viewName Название шаблона
     *
     * @throws InvalidOperationException
     */
    public function endBufferContent($viewName)
    {
        $this->normalizeViewName($viewName);
        $stack = $this->popStack();
        if ($stack == $viewName)
        {
            $buffer = ob_get_flush();
            $this->setContent($viewName, $buffer);
        }
        else
        {
            throw new InvalidOperationException('Wrong name of view content');
        }
    }

    /**
     * Завершает буферизацию вывода и выводит буфер страницы
     *
     * @return Buffer
     */
    public function endBufferPage()
    {
        $conn = Application::getInstance()->getConnection();
        if ($conn->isStatisticsUsage())
        {
            $statistics = $conn->getQueryStatistics();
            echo '<div style="border: 1px solid black; background-color: white; padding: 10px;">';
            echo '<p>' . $statistics->getQueryCount() . ' '
                 . Tools::sayRusRight(
                     $statistics->getQueryCount(),
                     'запрос',
                     'запроса',
                     'запросов'
                )
                 . ' за ' . $statistics->getAllQueryTime() . ' сек.</p><hr>';
            /** @var QueryInfo $queryInfo */
            foreach ($statistics as $queryInfo)
            {
                echo '<p>', $queryInfo->getUniqueId(), ' :<br><pre>', $queryInfo->getQuerySql(), '</pre><br>',
                    '(за ',$queryInfo->getQueryTime(),' сек)</p><hr>';
            }
            echo "</div><br>";
        }
        EventController::getInstance()->runEvents(
            'core',
            'OnEndBufferPage',
            ['BUFFER' => &$this->arBuffer]
        );
        ob_end_flush();

        return $this;
    }

    /**
     * Обрабатывает собранный буфер страницы и возвращает его
     *
     * @param string $buffer Буфер страницы
     *
     * @return mixed
     */
    public function getPage($buffer)
    {
        if (!empty($this->arBuffer))
        {
            foreach ($this->arBuffer as $name => $buff)
            {
                if ($name == "head_script")
                {
                    $buff = "<script>\n" . $buff . "\n</script>\n";
                }
                $buffer = str_replace('#' . strtoupper($name) . '#', $buff, $buffer);
            }
        }

        $buffer = preg_replace('/[#]{1}[A-Z0-9_]+[#]{1}/', '', $buffer);
        $buffer = preg_replace('/[%]{1}([A-Z0-9_]+)[%]{1}/', '#$1#', $buffer);

        return $buffer;
    }

    /**
     * Устанавливает Refresh для страницы. Отложенная функция
     *
     * @param string $url  Путь, куда будет произведен refresh
     * @param int    $time Время в секундах
     *
     * @return Buffer
     */
    public function setRefresh($url = '', $time = 0)
    {
        // msDebugNoAdmin(debug_backtrace());
        // msDebugNoAdmin($url);
        $app = Application::getInstance();
        if (array_key_exists('refresh',$this->arBuffer) && $this->arBuffer['refresh'] != '')
        {
            return $this;
        }
        if ($url != '')
        {
            if (strpos($url, '\\') !== false)
            {
                list(, $url) = explode('www', $url);
                $url = str_replace('\\', '/', $url);
            }
            // msDebugNoAdmin($url);
            $url = str_replace(Settings::getInstance()->getSiteProtocol(), '', $url);
            // msDebugNoAdmin($url);
            $url = str_replace('://', '', $url);
            // msDebugNoAdmin($url);
            $url = str_replace($app->getServer()->getHttpHost(), '', $url);
            // msDebugNoAdmin($url);
            $url = $app->getSitePath($url);
            // msDebugNoAdmin($url);
        }
        else
        {
            $url = $app->getServer()->getRequestUri();
        }
        $this->arBuffer['refresh'] = '<META HTTP-EQUIV=REFRESH CONTENT="'
                                     . (int)$time
                                     . '; URL='
                                     . '//' . $app->getServer()->getHttpHost()
                                     . $url
                                     . '">';

        return $this;
    }

    protected function normalizeViewName (string &$viewName)
    {
        $viewName = strtolower($viewName);

        return $this;
    }

    /**
     * Добавляет шаблон вывода на страницу
     *
     * @param string $view Название шаблона
     *
     * @return Buffer
     */
    public function showBufferContent($view)
    {
        $this->createView($view);

        echo '#', strtoupper($view), '#';

        return $this;
    }

    /**
     * Начинает обработку буфера для указанного шаблона вывода
     *
     * @param string $view Название шаблона
     *
     * @return Buffer
     */
    public function startContent($view)
    {
        $this->normalizeViewName($view);
        ob_start();

        $this->arStack[] = $view;

        return $this;
    }

    /**
     * Стартует буферизацию страницы
     */
    public function startBufferPage()
    {
        ob_start([&$this, 'getPage']);

        $this->setContent('page_view','');
        $this->setContent('head_css','');
        $this->setContent('head_js','');
        $this->setContent('head_script','');
    }


}
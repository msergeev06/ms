<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Events;

use Ms\Core\Api\ApiAdapter;
use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\Errors\FileLogger;
use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\Modules\Loader;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\Classes\ClassNotFoundException;
use Ms\Core\Exceptions\Classes\MethodNotFoundException;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Exceptions\IO\FileNotFoundException;
use Ms\Core\Exceptions\Modules\LoaderException;
use Ms\Core\Exceptions\Modules\ModuleDoesNotExistsException;
use Ms\Core\Exceptions\Modules\WrongModuleNameException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Interfaces\ILogger;

/**
 * Класс Ms\Core\Entity\Events\EventController
 * Управление событиями
 */
class EventController extends Multiton
{
    /** @var ILogger */
    protected $logger = null;
    /** @var array  */
    protected $arLoadedFromDB = [];

    protected function __construct ()
    {
        $this->logger = new FileLogger('core');
    }

    /**
     * Возвращает коллекцию обработчиков событий
     *
     * @return EventHandlersCollection
     * @unittest
     */
    public function getHandlersCollection ()
    {
        return EventRegistrar::getInstance()->getEventHandlersCollection();
    }

    /**
     * Устанавливает логгер
     *
     * @param ILogger $logger
     *
     * @return $this
     * @unittest
     */
    public function setLogger (ILogger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Возвращает логгер
     *
     * @return ILogger
     * @unittest
     */
    public function getLogger ()
    {
        return $this->logger;
    }

    /**
     * Возвращает массив зарегистрированных обработчиков заданного события
     *
     * @param string $eventModule Идентификатор пакета который инициирует событие
     * @param string $eventID     Идентификатор события
     * @param bool   $bFromDB     Флаг принудительной загрузки обработчиков событий из DB
     *
     * @return EventHandler[]    Пустой массив, или массив обработчиков
     * @unittest
     */
    public function getModuleEvents (string $eventModule, string $eventID, bool $bFromDB = false)
    {
        if (!isset($this->arLoadedFromDB[$eventModule][$eventID]) || $bFromDB)
        {
            $this->getEventHandlersFromDB ($eventModule, $eventID);
        }
        $collection = EventRegistrar::getInstance()->getEventHandlersCollection();
        $arReturn = $collection->getListByEvent($eventModule, $eventID);

        return $arReturn;
    }

    /**
     * Выполняет зарегистрированный обработчик события
     *
     * @param EventHandler $eventHandler Обработчик события
     * @param array        $arParams     Передаваемые параметры события
     *
     * @return mixed|bool
     * @unittest
     */
    public function execute (EventHandler $eventHandler, array $arParams = [])
    {
        //Перед исполнением обработчика:
        //либо подключаем модуль обработчика, если он задан, не пустой и не равен core
        if (
            !is_null($eventHandler->getEventModule())
            && $eventHandler->getEventModule() != ''
            && $eventHandler->getEventModule() != 'core'
        ) {
            try
            {
                Loader::includeModule($eventHandler->getEventModule());
            }
            catch (LoaderException $e)
            {
                $e->addMessageToLog($this->logger);

                return null;
            }
            catch (ModuleDoesNotExistsException $e)
            {
                $e->addMessageToLog($this->logger);

                return null;
            }
        }
        //либо подключаем указанный файл, если он задан и существует
        elseif (!is_null($eventHandler->getFileFullPath()) && file_exists($eventHandler->getFileFullPath()))
        {
            include_once ($eventHandler->getFileFullPath());
        }

        if (
            !is_null($eventHandler->getHandlerMethodArg())
            && is_array($eventHandler->getHandlerMethodArg())
            && count($eventHandler->getHandlerMethodArg())
        ) {
            $args = array_merge($eventHandler->getHandlerMethodArg(), $arParams);
        }
        else
        {
            $args = $arParams;
        }

        return call_user_func_array($eventHandler->getCallback(), $args);
    }

    /**
     * Получает список зарегистрированных обработчиков события и запускает их
     *
     * @param string $eventModule Идентификатор модуля который инициирует событие
     * @param string $eventID     Идентификатор события
     * @param array  $arParams    Параметры события
     * @param bool   $fromDB      Флаг принудительной загрузки обработчиков событий из DB
     *
     * @return bool
     * @unittest
     */
    public function runEvents (
        string $eventModule,
        string $eventID,
        array $arParams = [],
        bool $fromDB = false
    ) {
        if (!$this->isNeedRunEvents())
        {
            return true;
        }

        try
        {
            if ($arEvents = $this->getModuleEvents($eventModule, $eventID, $fromDB))
            {
                foreach ($arEvents as $sort => $events)
                {
                    if (!empty($events))
                    {
                        /** @var EventHandler $handler */
                        foreach ($events as $handler)
                        {
                            $bStop = $this->execute($handler, $arParams);
                            if ($bStop === false)
                            {
                                return false;
                            }
                        }
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            $exception = new SystemException($e->getMessage(),$e->getCode(),$e->getFile(),$e->getLine(),$e);
            $exception->addMessageToLog($this->logger);
        }

        return true;
    }

    protected function isNeedRunEvents ()
    {
        return !(defined('NOT_RUN_EVENTS') && NOT_RUN_EVENTS === true);
    }

    /**
     * Возвращает информацию о возникающих событиях указанного модуля в виде коллекции
     * Если у модуля события не описаны или описаны не коллекцией - вернет пустую коллекцию
     *
     * @param string $module - имя модуля, либо для ядра 'ms.core', 'core' или '' или null
     *
     * @return \Ms\Core\Entity\Events\Info\Collection
     * @unittest
     */
    public static function getModuleEventsInfo ($module = null)
    {
        if (!is_null($module))
        {
            $module = strtolower($module);
        }
        $includePath = null;
        if (is_null($module) || $module == 'ms.core' || $module == 'core' || $module == '')
        {
            $path = Application::getInstance()->getSettings()->getCoreRoot() . '/events.php';
            if (file_exists($path))
            {
                $includePath = $path;
            }
        }
        else
        {
            $path = Application::getInstance()->getSettings()->getLocalModulesRoot() . '/' . $module . '/events.php';
            if (file_exists($path))
            {
                $includePath = $path;
            }
            else
            {
                $path = Application::getInstance()->getSettings()->getModulesRoot() . '/' . $module . '/events.php';
                if (file_exists($path))
                {
                    $includePath = $path;
                }
            }
        }
        if (!is_null($includePath))
        {
            $col = include($includePath);
            if ($col instanceof \Ms\Core\Entity\Events\Info\Collection)
            {
                return $col;
            }
        }

        return new \Ms\Core\Entity\Events\Info\Collection();
    }

    /**
     * @param string $fromModule
     * @param string $eventID
     *
     * @return bool|mixed
     */
    protected function getEventHandlersFromDB (string $fromModule, string $eventID)
    {
        if (
            !Application::getInstance()->getConnectionPool()->offsetExists('default')
            || !Application::getInstance()->getConnection()->isSuccess()
        ) {
            return false;
        }

        try
        {
            $arRes = $this->getOrmEventHandlersTable()->getList(
                [
                    'filter' => [
                        'FROM_MODULE' => $fromModule,
                        'EVENT_ID'    => $eventID
                    ],
                    'order' => ['SORT' => 'DESC']
                ]
            );
        }
        catch (ArgumentTypeException $e)
        {
            $e->addMessageToLog($this->logger);

            return false;
        }
        catch (SqlQueryException $e)
        {
            $e->addMessageToLog($this->logger);

            return false;
        }
        if (!$arRes)
        {
            return false;
        }
        $this->arLoadedFromDB[$fromModule][$eventID] = $this->parseEventHandlersFromDB($arRes);

        return $this->arLoadedFromDB[$fromModule][$eventID];
    }

    /**
     * @param array $arRes
     *
     * @return bool
     */
    protected function parseEventHandlersFromDB (array $arRes)
    {
        if (empty($arRes))
        {
            return false;
        }
        foreach ($arRes as $arHandler)
        {
            try
            {
                $handler = (new ModuleEventHandler($arHandler['FROM_MODULE'], $arHandler['EVENT_ID']))
                    ->setHandlerClassMethod($arHandler['TO_CLASS'], $arHandler['TO_METHOD']);
                if (!is_null($arHandler['TO_MODULE_ID']))
                {
                    $handler->setHandlerModule($arHandler['TO_MODULE_ID']);
                }
                if (!is_null($arHandler['TO_PATH']))
                {
                    $handler->setFileFullPath($arHandler['TO_PATH']);
                }
                if (!empty($arHandler['TO_METHOD_ARG']) && is_array($arHandler['TO_METHOD_ARG']))
                {
                    $handler->setHandlerMethodArg($arHandler['TO_METHOD_ARG']);
                }
            }
            catch (ClassNotFoundException $e)
            {
                $e->addMessageToLog($this->logger);

                continue;
            }
            catch (MethodNotFoundException $e)
            {
                $e->addMessageToLog($this->logger);

                continue;
            }
            catch (FileNotFoundException $e)
            {
                $e->addMessageToLog($this->logger);

                continue;
            }
            catch (WrongModuleNameException $e)
            {
                $e->addMessageToLog($this->logger);

                continue;
            }
            EventRegistrar::getInstance()->getEventHandlersCollection()->addHandler($handler);
        }

        return true;
    }

    /**
     * @return ORMController
     */
    private function getOrmEventHandlersTable ()
    {
        return ApiAdapter::getInstance()->getEventsApi()->getOrmEventHandlersTable();
    }
}
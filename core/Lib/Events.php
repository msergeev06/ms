<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Tables;
use Ms\Core\Entity\System\Application;

/**
 * Класс Ms\Core\Lib\Events
 * Управление событиями
 */
class Events
{
    /**
     * @var array
     */
    private static $arEvents = [];

    /**
     * @var array
     */
    private static $arEventsModules = [];

    /**
     * @var bool
     */
    private static $bGetEventHandlersFromDB = false;

    /**
     * Регистрирует произвольный обработчик callback события eventID модуля fromModule.
     * Если указан полный путь к файлу с обработчиком fullPath, то он будет
     * автоматически подключен перед вызовом обработчика. Вызывается на каждом
     * хите и работает до момента окончания работы скрипта.
     *
     * @api
     *
     * @param string       $fromModule          Идентификатор модуля, который
     *                                          будет инициировать событие
     * @param string       $eventID             Идентификатор события
     * @param string|array $callback            Название функции обработчика.
     *                                          Если это метод класса, то массив вида
     *                                          Array(класс(объект), название метода)
     * @param int          $sort                Очередность (порядок), в котором
     *                                          выполняется данный обработчик
     *                                          (обработчиков данного события
     *                                          может быть больше одного).
     *                                          Необязательный параметр,
     *                                          по умолчанию равен 100
     * @param bool|string  $fullPath            Полный путь к файлу для
     *                                          подключения при возникновении
     *                                          события перед
     *                                          вызовом callback
     *
     * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/events/method_add_event_handler
     */
    public static function addEventHandler($fromModule, $eventID, $callback, $sort = 100, $fullPath = false)
    {
        $arHash = [
            'FROM_MODULE' => $fromModule,
            'EVENT_ID'    => $eventID,
            'SORT'        => $sort,
            'CALLBACK'    => $callback,
            'FULL_PATH'   => $fullPath
        ];
        $hash = md5(serialize($arHash));
        self::$arEvents[$fromModule][$eventID][$sort][$hash] = [
            'CALLBACK'  => $callback,
            'FULL_PATH' => $fullPath
        ];
    }

    /**
     * Регистрирует обработчик события. Выполняется один раз (при установке модуля) и этот обработчик события
     * действует до момента вызова события unRegisterModuleDependences
     *
     * @param string $fromModule            Идентификатор модуля, который будет
     *                                      инициировать событие
     * @param string $eventID               Идентификатор события
     * @param string $toModule              Идентификатор модуля, содержащий
     *                                      функцию-обработчик события.
     *                                      Необязательный
     * @param string $toClass               Класс принадлежащий модулю $toModule,
     *                                      метод которого является
     *                                      функцией-обработчиком события.
     *                                      Необязательный параметр.
     *                                      По умолчанию - ""
     *                                      (будет просто подключен файл
     *                                      /ms/modules/$toModule/include.php)
     * @param string $toMethod              Метод класса $toClass являющийся
     *                                      функцией-обработчиком события.
     *                                      Необязательный параметр.
     *                                      По умолчанию - ""
     *                                      (будет просто подключен файл
     *                                      /ms/modules/$toModule/include.php)
     * @param int    $sort                  Очередность (порядок), в котором
     *                                      выполняется данный обработчик
     *                                      (обработчиков данного события может
     *                                      быть больше одного).
     *                                      Необязательный параметр, по умолчанию
     *                                      равен 100
     * @param string $toPath                Относительный путь к исполняемому
     *                                      файлу
     * @param string $fullPath              Полный путь к исполняемому файлу
     * @param array  $toMethodArg           Массив аргументов для
     *                                      функции-обработчика событий.
     *                                      Необязательный параметр.
     *
     * @return \Ms\Core\Entity\Db\Result\DBResult
     *
     * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/events/method_register_module_dependences
     */
    public static function registerModuleDependences(
        $fromModule,
        $eventID,
        $toModule = '',
        $toClass = "",
        $toMethod = "",
        $sort = 100,
        $toPath = "",
        $fullPath = "",
        $toMethodArg = []
    ) {
        $arAdd = [
            'FROM_MODULE' => strtolower($fromModule),
            'EVENT_ID'    => $eventID,
            'SORT'        => intval($sort)
        ];
        if ($toModule != '')
        {
            $arAdd['TO_MODULE_ID'] = strtolower($toModule);
        }
        if ($toClass != '')
        {
            $arAdd['TO_CLASS'] = $toClass;
        }
        if ($toMethod != '')
        {
            $arAdd['TO_METHOD'] = $toMethod;
        }
        if (
            $toPath != ''
            && file_exists(
                Application::getInstance()->getSettings()->getMsRoot()
                . $toPath
            )
        )
        {
            $arAdd['TO_PATH'] = $toPath;
        }
        if ($fullPath != '' && file_exists($fullPath))
        {
            $arAdd['FULL_PATH'] = $fullPath;
        }
        if (!empty($toMethodArg))
        {
            $arAdd['TO_METHOD_ARG'] = $toMethodArg;
        }

        return Tables\EventHandlersTable::add($arAdd);
    }

    /**
     * Удаляет регистрационную запись обработчика события
     *
     * @param string $fromModule        Идентификатор модуля который инициирует
     *                                  событие
     * @param string $eventID           Идентификатор события
     * @param string $toModule          Идентификатор модуля содержащий
     *                                  функцию-обработчик события
     * @param string $toClass           Класс принадлежащий модулю $toModule,
     *                                  метод которого является
     *                                  функцией-обработчиком события.
     *                                  Необязательный параметр.
     *                                  По умолчанию - "".
     * @param string $toMethod          Метод класса $toClass являющийся
     *                                  функцией-обработчиком события.
     *                                  Необязательный параметр.
     *                                  По умолчанию - "".
     * @param string $toPath            Необязательный параметр, по умолчанию пустой
     * @param string $fullPath          Полный путь к исполняемому файлу
     * @param array  $toMethodArg       Массив аргументов для функции-обработчика
     *                                  событий.
     *                                  Необязательный параметр
     *
     * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/events/method_un_register_module_dependences
     */
    public static function unRegisterModuleDependences(
        $fromModule,
        $eventID,
        $toModule = '',
        $toClass = "",
        $toMethod = "",
        $toPath = "",
        $fullPath = "",
        $toMethodArg = []
    ) {
        $arFilter = [
            'FROM_MODULE' => strtolower($fromModule),
            'EVENT_ID'    => $eventID
        ];
        if ($toModule != '')
        {
            $arFilter['TO_MODULE_ID'] = strtolower($toModule);
        }
        if ($toClass != '')
        {
            $arFilter['TO_CLASS'] = $toClass;
        }
        if ($toMethod != '')
        {
            $arFilter['TO_METHOD'] = $toMethod;
        }
        if ($toPath != '')
        {
            $arFilter['TO_PATH'] = $toPath;
        }
        if ($fullPath != '')
        {
            $arFilter['FULL_PATH'] = $fullPath;
        }
        if (!empty($toMethodArg))
        {
            $arFilter['TO_METHOD_ARG'] = $toMethodArg;
        }

        $arRes = Tables\EventHandlersTable::getList(
            [
                'select' => ['ID'],
                'filter' => $arFilter
            ]
        );
        if ($arRes)
        {
            foreach ($arRes as $arHandler)
            {
                Tables\EventHandlersTable::delete($arHandler['ID']);
            }
        }
    }

    /**
     * Возвращает массив зарегистрированных обработчиков заданного события
     *
     * @param string $fromModule Идентификатор пакета который инициирует событие
     * @param string $eventID    Идентификатор события
     * @param bool   $fromDB     Флаг принудительной загрузки обработчиков событий из DB
     *
     * @return array                Пустой массив, или массив обработчиков
     * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/events/get_module_events
     */
    public static function getModuleEvents($fromModule, $eventID, $fromDB = false)
    {
        if (!self::$bGetEventHandlersFromDB || $fromDB)
        {
            if ($fromDB)
            {
                self::getEventHandlersFromDB($fromModule, $eventID);
            }
            else
            {
                self::getEventHandlersFromDB();
            }
        }

        $arReturn = [];
        if (isset(self::$arEventsModules[$fromModule][$eventID]))
        {
            $arReturn = self::$arEventsModules[$fromModule][$eventID];
        }

        if (isset(self::$arEvents[$fromModule][$eventID]))
        {
            foreach (self::$arEvents[$fromModule][$eventID] as $sort => $events)
            {
                foreach ($events as $hash => $event)
                {
                    $arReturn[$sort][$hash] = $event;
                }
            }
        }


        if (!empty($arReturn))
        {
            $arTmp = $arReturn;
            $arReturn = [];
            foreach ($arTmp as $sort => $arEvents)
            {
                foreach ($arEvents as $hash => $event)
                {
                    $arReturn[$sort][] = $event;
                }
            }

            krsort($arReturn);

            return $arReturn;
        }
        else
        {
            return [];
        }
    }

    /**
     * Выполняет зарегистрированный обработчик события
     *
     * @param array $arEvent  - массив параметров события
     * @param array $arParams - массив параментров, передаваемых в обработчик
     *
     * @return bool|mixed
     * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/events/method_execute_module_event
     */
    public static function executeModuleEvent($arEvent, $arParams = [])
    {
        $r = true;

        if (
            isset($arEvent["TO_MODULE_ID"])
            && $arEvent["TO_MODULE_ID"] <> ""
            && $arEvent["TO_MODULE_ID"] <> "core"
        )
        {
            //Подключаем нужный пакет
            if (!Loader::includeModule(strtolower($arEvent["TO_MODULE_ID"])))
            {
                return null;
            }
        }
        elseif (
            isset($arEvent["TO_PATH"])
            && $arEvent["TO_PATH"] <> ""
            && file_exists(
                Application::getInstance()->getSettings()->getMsRoot()
                . $arEvent["TO_PATH"]
            )
        )
        {
            $r = include_once(
                Application::getInstance()->getSettings()->getMsRoot()
                . $arEvent["TO_PATH"]
            );
        }
        elseif ($arEvent['FULL_PATH'] !== false && file_exists($arEvent['FULL_PATH']))
        {
            //Выполняем код из заданного файла
            $r = include_once($arEvent['FULL_PATH']);
        }

        if (array_key_exists("CALLBACK", $arEvent))
        {
            if (
                isset($arEvent["TO_METHOD_ARG"])
                && is_array($arEvent["TO_METHOD_ARG"])
                && count($arEvent["TO_METHOD_ARG"])
            )
            {
                $args = array_merge($arEvent["TO_METHOD_ARG"], $arParams);
            }
            else
            {
                $args = $arParams;
            }

            return call_user_func_array($arEvent["CALLBACK"], $args);
        }
        elseif (
            $arEvent["TO_CLASS"] != ""
            && !is_null($arEvent["TO_CLASS"])
            && $arEvent["TO_METHOD"] != ""
            && !is_null($arEvent["TO_METHOD"])
        )
        {
            if (is_array($arEvent["TO_METHOD_ARG"]) && count($arEvent["TO_METHOD_ARG"]))
            {
                $args = array_merge($arEvent["TO_METHOD_ARG"], $arParams);
            }
            else
            {
                $args = $arParams;
            }

            //php bug: http://bugs.php.net/bug.php?id=47948
            class_exists($arEvent["TO_CLASS"]);

            return call_user_func_array(
                [$arEvent["TO_CLASS"], $arEvent["TO_METHOD"]],
                $args
            );
        }
        else
        {
            return $r;
        }
    }

    /**
     * Получает список зарегистрированных обработчиков события и запускает их
     *
     * @param string $fromModule Идентификатор модуля который инициирует событие
     * @param string $eventID    Идентификатор события
     * @param array  $arParams   Параметры события
     * @param bool   $fromDB     Флаг принудительной загрузки обработчиков событий
     *                           из DB
     *
     * @return bool
     * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/events/method_run_events
     */
    public static function runEvents(
        $fromModule,
        $eventID,
        $arParams = [],
        $fromDB = false
    ) {
        if ($arEvents = static::getModuleEvents($fromModule, $eventID, $fromDB))
        {
            foreach ($arEvents as $sort => $ar_events)
            {
                foreach ($ar_events as $arEvent)
                {
                    $bStop = static::executeModuleEvent($arEvent, $arParams);
                    if ($bStop === false)
                    {
                        return $bStop;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Получает информацию о зарегистрированных обработчиках событий из DB
     *
     * @param null|string $fromModule Идентификатор инициализирующего модуля
     * @param null|string $eventID    Идентификатор обновляемого события
     */
    private static function getEventHandlersFromDB($fromModule = null, $eventID = null)
    {
        if (is_null($fromModule) && is_null($eventID))
        {
            self::$bGetEventHandlersFromDB = true;
        }

        $arList = [
            'order' => ['SORT' => 'DESC']
        ];
        if (!is_null($fromModule) && !is_null($eventID))
        {
            $arList['filter'] = [
                'FROM_MODULE' => strtolower($fromModule),
                'EVENT_ID'    => $eventID
            ];
        }

        $arRes = Tables\EventHandlersTable::getList($arList);

        if ($arRes)
        {
            foreach ($arRes as $arHandler)
            {
                unset($arHandler['ID']);
                $hash = md5(serialize($arHandler));
                $fromMod = $arHandler['FROM_MODULE'];
                unset($arHandler['FROM_MODULE']);
                $eventID = $arHandler['EVENT_ID'];
                unset($arHandler['EVENT_ID']);
                $sort = $arHandler['SORT'];
                unset($arHandler['SORT']);

                self::$arEventsModules[$fromMod][$eventID][$sort][$hash] = $arHandler;
            }
        }
        //msDebug(self::$arEventsModules);
    }

    /**
     * Возвращает информацию о возникающих событиях указанного модуля в виде массива
     *
     * Если у модуля события не описаны, вернет пустой массив
     *
     * @param string $module - имя модуля, либо для ядра 'core' или '' или null
     *
     * @return array
     * @link  http://docs.dobrozhil.ru/doku.php/ms/core/lib/events/method_get_module_events_info
     */
    public static function getModuleEventsInfo($module = null)
    {
        if (!is_null($module))
        {
            $module = strtolower($module);
        }
        if (is_null($module) || $module == 'core' || $module == '')
        {
            $path = Application::getInstance()->getSettings()->getCoreRoot();
        }
        else
        {
            $path = Application::getInstance()->getSettings()->getModulesRoot() . '/' . $module;
        }
        if (file_exists($path . '/events.php'))
        {
            return include($path . '/events.php');
        }

        return [];
    }
}



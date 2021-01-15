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
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\Classes\ClassNotFoundException;
use Ms\Core\Exceptions\Classes\MethodNotFoundException;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Exceptions\Db\ValidateException;
use Ms\Core\Exceptions\IO\FileNotFoundException;
use Ms\Core\Exceptions\Modules\WrongModuleNameException;
use Ms\Core\Interfaces\ILogger;
use Ms\Core\Entity\Modules\Modules;

/**
 * Класс Ms\Core\Entity\Events\EventRegistrar
 * Устанавливает/удаляет обработчики событий
 */
class EventRegistrar extends Multiton
{
    /** @var EventHandlersCollection */
    protected $eventHandlersCollection = null;
    /** @var ILogger */
    protected $logger = null;

    protected function __construct ()
    {
        $this->logger = new FileLogger('core');
        $this->eventHandlersCollection = new EventHandlersCollection();
    }

    /**
     * Регистрирует произвольный обработчик callback события eventID модуля fromModule.
     * Если указан полный путь к файлу с обработчиком fullPath, то он будет
     * автоматически подключен перед вызовом обработчика. Вызывается на каждом
     * хите и работает до момента окончания работы скрипта.
     *
     * @param string      $eventModule          Идентификатор модуля, который будет инициировать событие
     * @param string      $eventID              Идентификатор события
     * @param string      $className            Класс обработчика события, с пространством имен
     * @param string      $methodName           Метод класса обработчика события, с пространством имен
     * @param int         $sort                 Очередность (порядок), в котором выполняется данный обработчик
     *                                          (обработчиков данного события может быть больше одного).
     *                                          Необязательный параметр, по умолчанию равен 100
     * @param bool|string $fullPath             Полный путь к файлу для подключения при возникновении события перед
     *                                          вызовом callback
     *
     * @return EventRegistrar
     * @throws ClassNotFoundException
     * @throws FileNotFoundException
     * @throws MethodNotFoundException
     * @throws WrongModuleNameException
     * @unittest
     */
    public function addEventHandler (
        string $eventModule,
        string $eventID,
        string $className,
        string $methodName,
        int $sort = 100,
        $fullPath = false
    ) {
        $handler = (new EventHandler($eventModule, $eventID))
            ->setCallback($className, $methodName)
            ->setSort($sort)
        ;
        if ($fullPath)
        {
            $handler->setFileFullPath($fullPath);
        }
        $this->eventHandlersCollection->addHandler($handler);

        return $this;
    }

    /**
     * Регистрирует обработчик события. Выполняется один раз (при установке модуля) и этот обработчик события
     * действует до момента вызова события unRegisterModuleDependencies
     *
     * @param string $fromModule  Идентификатор модуля, который будет инициировать событие
     * @param string $eventID     Идентификатор события
     * @param string $toModule    Идентификатор модуля, содержащий функцию-обработчик события. Необязательный
     * @param string $toClass     Класс принадлежащий модулю $toModule, метод которого является функцией-обработчиком
     *                            события. Необязательный параметр. По умолчанию - "" (будет просто подключен файл
     *                            /ms/modules/$toModule/include.php)
     * @param string $toMethod    Метод класса $toClass являющийся функцией-обработчиком события. Необязательный
     *                            параметр. По умолчанию - "" (будет просто подключен файл
     *                            /ms/modules/$toModule/include.php)
     * @param int    $sort        Очередность (порядок), в котором выполняется данный обработчик (обработчиков данного
     *                            события может быть больше одного). Необязательный параметр, по умолчанию равен 100
     * @param string $toPath      Полный путь к исполняемому файлу
     * @param array  $toMethodArg Массив аргументов для функции-обработчика событий. Необязательный параметр.
     *
     * @return bool
     * @throws ClassNotFoundException
     * @throws FileNotFoundException
     * @throws MethodNotFoundException
     * @throws WrongModuleNameException
     * @unittest
     */
    public function addModuleEventHandler (
        string $fromModule,
        string $eventID,
        string $toModule = '',
        string $toClass = '',
        string $toMethod = '',
        int $sort = 100,
        string $toPath = '',
        array $toMethodArg = []
    ) {
        if (!Modules::getInstance()->checkModuleName($fromModule))
        {
            throw new WrongModuleNameException($fromModule, __FILE__, __LINE__);
        }
        if ($toModule != '' && !Modules::getInstance()->checkModuleName($toModule))
        {
            throw new WrongModuleNameException($toModule, __FILE__, __LINE__);
        }

        $arAdd = [
            'FROM_MODULE' => $fromModule,
            'EVENT_ID' => $eventID,
            'SORT' => ((int)$sort >= 0) ? (int)$sort : 100,
        ];
        if ($toModule != '')
        {
            $arAdd['TO_MODULE'] = $toModule;
        }
        if ($toClass != '')
        {
            if (!class_exists($toClass))
            {
                throw new ClassNotFoundException($toClass);
            }
            $arAdd['TO_CLASS'] = $toClass;
            if ($toMethod != '')
            {
                if (!method_exists($toClass, $toMethod))
                {
                    throw new MethodNotFoundException($toClass, $toMethod,__FILE__, __LINE__);
                }
                $arAdd['TO_METHOD'] = $toMethod;
            }
        }
        if ($toPath != '')
        {
            if (!file_exists($toPath))
            {
                throw new FileNotFoundException($toPath);
            }
            $arAdd['TO_PATH'] = $toPath;
        }
        if (!empty($toMethodArg))
        {
            $arAdd['TO_METHOD_ARG'] = $toMethodArg;
        }

        try
        {
            return $this->getOrmEventHandlersTable()->insert($arAdd)->isSuccess();
        }
        catch (ArgumentNullException $e)
        {
            $e->addMessageToLog($this->logger);

            return false;
        }
        catch (SqlQueryException $e)
        {
            $e->addMessageToLog($this->logger);

            return false;
        }
        catch (ValidateException $e)
        {
            $e->addMessageToLog($this->logger);

            return false;
        }
    }

    /**
     * Удаляет регистрационную запись обработчика события
     *
     * @param string $fromModule  Идентификатор модуля который инициирует событие
     * @param string $eventID     Идентификатор события
     * @param string $toModule    Идентификатор модуля содержащий функцию-обработчик события
     * @param string $toClass     Класс принадлежащий модулю $toModule, метод которого является функцией-обработчиком
     *                            события. Необязательный параметр.По умолчанию - "".
     * @param string $toMethod    Метод класса $toClass являющийся функцией-обработчиком события. Необязательный
     *                            параметр. По умолчанию - "".
     * @param string $toPath      Полный путь к исполняемому файлу
     * @param array  $toMethodArg Массив аргументов для функции-обработчика событий. Необязательный параметр
     *
     * @return bool
     * @unittest
     */
    public function deleteModuleEventHandler (
        string $fromModule,
        string $eventID,
        string $toModule = '',
        string $toClass = '',
        string $toMethod = '',
        string $toPath = '',
        array $toMethodArg = []
    ) {
        $arFilter = [
            'FROM_MODULE' => strtolower($fromModule),
            'EVENT_ID' => $eventID
        ];
        if ($toModule != '')
        {
            $arFilter['TO_MODULE'] = strtolower($toModule);
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
        if (!empty($toMethodArg))
        {
            $arFilter['TO_METHOD_ARG'] = $toMethodArg;
        }

        try
        {
            $arRes = $this->getOrmEventHandlersTable()->getList(
                [
                    'select' => ['ID'],
                    'filter' => $arFilter
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
        if ($arRes)
        {
            $conn = Application::getInstance()->getConnection();
            $conn->startTransaction();
            foreach ($arRes as $arHandler)
            {
                try
                {
                    $bResult = $this->getOrmEventHandlersTable()->delete($arHandler['ID'])->isSuccess();
                    if (!$bResult)
                    {
                        $conn->rollbackTransaction();

                        return false;
                    }
                }
                catch (ArgumentNullException $e)
                {
                    $e->addMessageToLog($this->logger);
                    $conn->rollbackTransaction();

                    return false;
                }
                catch (SqlQueryException $e)
                {
                    $e->addMessageToLog($this->logger);
                    $conn->rollbackTransaction();

                    return false;
                }
            }
            $conn->commitTransaction();

            return true;
        }

        return false;
    }

    /**
     * Возвращает коллекцию обработчиков событий
     *
     * @return EventHandlersCollection
     * @unittest
     */
    public function getEventHandlersCollection (): EventHandlersCollection
    {
        return $this->eventHandlersCollection;
    }

    /**
     * <Описание>
     *
     * @return FileLogger|ILogger
     * @unittest
     */
    public function getLogger ()
    {
        return $this->logger;
    }

    /**
     * <Описание>
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
     * @return ORMController
     */
    private function getOrmEventHandlersTable ()
    {
        return ApiAdapter::getInstance()->getEventsApi()->getOrmEventHandlersTable();
    }
}
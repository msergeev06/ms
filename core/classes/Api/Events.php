<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Api;

use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\Events\EventController;
use Ms\Core\Entity\Events\EventHandler;
use Ms\Core\Entity\Events\EventRegistrar;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Exceptions\Classes\ClassNotFoundException;
use Ms\Core\Exceptions\Classes\MethodNotFoundException;
use Ms\Core\Exceptions\IO\FileNotFoundException;
use Ms\Core\Exceptions\Modules\WrongModuleNameException;
use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Api\Events
 * API методы работы с событиями
 */
class Events extends Multiton
{
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
        if (class_exists('Ms\Core\Entity\Events\EventController'))
        {
            return EventController::getInstance()->runEvents($eventModule, $eventID, $arParams, $fromDB);
        }

        return true;
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
    public function getEvents (string $eventModule, string $eventID, bool $bFromDB = false)
    {
        if (class_exists('Ms\Core\Entity\Events\EventController'))
        {
            return EventController::getInstance()->getModuleEvents($eventModule, $eventID, $bFromDB);
        }

        return [];
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
        return EventController::getInstance()->execute($eventHandler, $arParams);
    }

    /**
     * Возвращает ссылку на объект контроллера обработчиков событий
     *
     * @return \Ms\Core\Entity\Events\EventController
     * @unittest
     */
    public function getEventController ()
    {
        return EventController::getInstance();
    }

    /**
     * Возвращает ссылку на объект регистратора обработчиков событий
     *
     * @return EventRegistrar
     * @unittest
     */
    public function getEventRegistrar ()
    {
        return EventRegistrar::getInstance();
    }

    /**
     * Создает обработчик событий, работающий на конкретном хите
     *
     * @param string $eventModule   Модуль, генерирующий событие
     * @param string $eventID       Идентификатор события
     * @param string $className     Имя класса обработчика события
     * @param string $methodName    Имя метода обработчика события
     * @param int    $sort          Сортировка обработчика события
     * @param bool|string $fullPath Полный путь к файлу для подключения при возникновении события перед вызовом callback
     *
     * @return bool|EventRegistrar
     * @unittest
     */
    public function addEventHandler (
        string $eventModule,
        string $eventID,
        string $className,
        string $methodName,
        int $sort = 100,
        $fullPath = false
    )
    {
        try
        {
            $reg = EventRegistrar::getInstance()->addEventHandler(
                $eventModule,
                $eventID,
                $className,
                $methodName,
                $sort,
                $fullPath
            )
            ;
        }
        catch (SystemException $e)
        {
            return false;
        }

        return $reg;
    }

    /**
     * Возвращает ссылку на объект ORMController для таблицы EventHandlersTable
     *
     * @return ORMController
     * @unittest
     */
    public function getOrmEventHandlersTable()
    {
        return ORMController::getInstance(new \Ms\Core\Tables\EventHandlersTable());
    }
}
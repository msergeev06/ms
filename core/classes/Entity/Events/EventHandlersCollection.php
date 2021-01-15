<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Events;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Events\EventHandlersCollection
 * Коллекция обработчиков событий
 */
class EventHandlersCollection extends Dictionary
{
    /**
     * <Описание>
     *
     * @param EventHandler $eventHandler
     * @unittest
     */
    public function addHandler (EventHandler $eventHandler)
    {
        $this->offsetSet($eventHandler->getHash(), $eventHandler);
    }

    /**
     * <Описание>
     *
     * @param string $hash
     *
     * @return EventHandler|null
     * @unittest
     */
    public function getHandlerByHash (string $hash)
    {
        return $this->offsetGet($hash);
    }

    /**
     * <Описание>
     *
     * @param string $hash
     *
     * @return bool
     * @unittest
     */
    public function issetByHash(string $hash)
    {
        return $this->offsetExists($hash);
    }

    /**
     * <Описание>
     *
     * @param string $className
     * @param string $methodName
     *
     * @return EventHandler|null
     * @unittest
     */
    public function getByClassMethod (string $className, string $methodName)
    {
        if ($this->isEmpty())
        {
            return null;
        }
        /**
         * @var string $hash
         * @var EventHandler $handler
         */
        foreach ($this->values as $hash => $handler)
        {
            if (!$arClassMethod = $this->getClassAndMethod($handler))
            {
                continue;
            }
            if ($arClassMethod['CLASS'] == $className && $arClassMethod['METHOD'] == $methodName)
            {
                return $handler;
            }
        }

        return null;
    }

    /**
     * <Описание>
     *
     * @param string $className
     * @param string $methodName
     *
     * @return bool
     * @unittest
     */
    public function issetByClassMethod (string $className, string $methodName)
    {
        if ($this->isEmpty())
        {
            return false;
        }
        /**
         * @var string $hash
         * @var EventHandler $handler
         */
        foreach ($this->values as $hash => $handler)
        {
            if (!$arClassMethod = $this->getClassAndMethod($handler))
            {
                continue;
            }
            if ($arClassMethod['CLASS'] == $className && $arClassMethod['METHOD'] == $methodName)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * <Описание>
     *
     * @param string $eventModule
     * @param string $eventID
     *
     * @return array
     * @unittest
     */
    public function getListByEvent (string $eventModule, string $eventID)
    {
        if ($this->isEmpty())
        {
            return [];
        }

        $arReturn = [];
        /**
         * @var string $hash
         * @var EventHandler $handler
         */
        foreach ($this->values as $hash => $handler)
        {
            if ($handler->getEventModule() == $eventModule && $handler->getEventID() == $eventID)
            {
                $arReturn[$handler->getSort()][] = $handler;
            }
        }
        if (!empty($arReturn))
        {
            krsort($arReturn);

            return $arReturn;
        }

        return [];
    }

    /**
     * <Описание>
     *
     * @param string $eventModule
     * @param string $eventID
     *
     * @return bool
     * @unittest
     */
    public function issetByEvent (string $eventModule, string $eventID)
    {
        if ($this->isEmpty())
        {
            return false;
        }
        /**
         * @var string $hash
         * @var EventHandler $handler
         */
        foreach ($this->values as $hash => $handler)
        {
            if ($handler->getEventModule() == $eventModule && $handler->getEventID() == $eventID)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * <Описание>
     *
     * @param EventHandler $eventHandler
     *
     * @return array|bool
     */
    protected function getClassAndMethod (EventHandler $eventHandler)
    {
        $callback = $eventHandler->getCallback();
        if (empty($callback))
        {
            return false;
        }
        if (count($callback) == 2)
        {
            return ['CLASS'=>$callback[0], 'METHOD' => $callback[1]];
        }

        return false;
    }
}
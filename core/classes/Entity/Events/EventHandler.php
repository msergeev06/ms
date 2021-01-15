<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Events;

use Ms\Core\Exceptions\Classes\ClassNotFoundException;
use Ms\Core\Exceptions\Classes\MethodNotFoundException;
use Ms\Core\Exceptions\IO\FileNotFoundException;
use Ms\Core\Exceptions\Modules\WrongModuleNameException;
use Ms\Core\Entity\Modules\Modules;

/**
 * Класс Ms\Core\Entity\Events\EventHandler
 * Объект обработчика события
 */
class EventHandler
{
    /** @var string */
    protected $eventModule = null;
    /** @var string */
    protected $eventID = null;
    /** @var null|array */
    protected $callback = null;
    /** @var int  */
    protected $sort = 100;
    /** @var bool|string */
    protected $fileFullPath = false;

    public function __construct (string $eventModule, string $eventID)
    {
        if (!Modules::getInstance()->checkModuleName($eventModule))
        {
            throw new WrongModuleNameException($eventModule, __FILE__, __LINE__);
        }

        $this->eventModule = $eventModule;
        $this->eventID = $eventID;
    }

    /**
     * <Описание>
     *
     * @return string
     * @unittest
     */
    public function getEventModule ()
    {
        return $this->eventModule;
    }

    /**
     * <Описание>
     *
     * @return string
     * @unittest
     */
    public function getEventID ()
    {
        return $this->eventID;
    }

    /**
     * <Описание>
     *
     * @return array|null
     * @unittest
     */
    public function getCallback ()
    {
        return $this->callback;
    }

    /**
     * <Описание>
     *
     * @param string $className
     * @param string $methodName
     *
     * @return $this
     * @throws ClassNotFoundException
     * @throws MethodNotFoundException
     * @unittest
     */
    public function setCallback (string $className, string $methodName)
    {
        if (!class_exists($className))
        {
            throw new ClassNotFoundException($className,__FILE__,__LINE__);
        }
        if (!method_exists($className, $methodName))
        {
            throw new MethodNotFoundException($className, $methodName, __FILE__, __LINE__);
        }
        $this->callback = [$className, $methodName];

        return $this;
    }

    /**
     * <Описание>
     *
     * @return int
     * @unittest
     */
    public function getSort ()
    {
        return $this->sort;
    }

    /**
     * <Описание>
     *
     * @param int $sort
     *
     * @return $this
     * @unittest
     */
    public function setSort (int $sort = 100)
    {
        if ((int)$sort < 0)
        {
            $sort = 100;
        }

        $this->sort = (int)$sort;

        return $this;
    }

    /**
     * <Описание>
     *
     * @return bool|string
     * @unittest
     */
    public function getFileFullPath ()
    {
        return $this->fileFullPath;
    }

    /**
     * <Описание>
     *
     * @param string $fileFullPath
     *
     * @return $this
     * @throws FileNotFoundException
     * @unittest
     */
    public function setFileFullPath (string $fileFullPath)
    {
        if (!file_exists($fileFullPath))
        {
            throw new FileNotFoundException($fileFullPath);
        }
        $this->fileFullPath = $fileFullPath;

        return $this;
    }

    /**
     * <Описание>
     *
     * @return $this
     * @unittest
     */
    public function unsetFileFullPath ()
    {
        $this->fileFullPath = false;

        return $this;
    }

    /**
     * <Описание>
     *
     * @return null
     * @unittest
     */
    public function getHandlerModule ()
    {
        return null;
    }

    /**
     * <Описание>
     *
     * @return array
     * @unittest
     */
    public function getHandlerMethodArg ()
    {
        return [];
    }

    /**
     * <Описание>
     *
     * @return string
     * @unittest
     */
    public function getHash ()
    {
        $arHash = [
            'FROM_MODULE' => $this->getEventModule(),
            'EVENT_ID' => $this->getEventID(),
            'SORT' => $this->getSort(),
            'CALLBACK' => $this->getCallback(),
            'FULL_PATH' => $this->getFileFullPath()
        ];

        return md5(serialize($arHash));
    }
}
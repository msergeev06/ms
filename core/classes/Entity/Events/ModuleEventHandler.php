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
use Ms\Core\Exceptions\Modules\WrongModuleNameException;
use Ms\Core\Entity\Modules\Modules;

/**
 * Класс Ms\Core\Entity\Events\ModuleEventHandler
 * Регистрируемый в БД обработчик события модуля
 */
class ModuleEventHandler extends EventHandler
{
    /** @var null|string */
    protected $handlerModule = null;
    /** @var null|string */
    protected $handlerClass = null;
    /** @var null|string */
    protected $handlerMethod = null;
    /** @var array */
    protected $handlerMethodArg = [];

    /**
     * Конструктор класса ModuleEventHandler
     *
     * @param string $eventModule
     * @param string $eventID
     *
     * @throws WrongModuleNameException
     */
    public function __construct (string $eventModule, string $eventID)
    {
        parent::__construct($eventModule, $eventID);
    }

    /**
     * <Описание>
     *
     * @return array|null
     * @unittest
     */
    public function getCallback ()
    {
        return [$this->handlerClass, $this->handlerMethod];
    }

    /**
     * <Описание>
     *
     * @param string $className
     * @param string $methodName
     *
     * @return $this|EventHandler
     * @throws ClassNotFoundException
     * @throws MethodNotFoundException
     * @unittest
     */
    public function setCallback (string $className, string $methodName)
    {
        parent::setCallback($className, $methodName);

        if (is_array($this->callback) && count($this->callback) == 2)
        {
            $this->handlerClass = $this->callback[0];
            $this->handlerMethod = $this->callback[1];
        }

        return $this;
    }

    /**
     * <Описание>
     *
     * @return string|null
     * @unittest
     */
    public function getHandlerModule ()
    {
        return $this->handlerModule;
    }

    /**
     * <Описание>
     *
     * @param string $handlerModule
     *
     * @return $this
     * @throws WrongModuleNameException
     * @unittest
     */
    public function setHandlerModule (string $handlerModule)
    {
        if (!Modules::getInstance()->checkModuleName($handlerModule))
        {
            throw new WrongModuleNameException($handlerModule,__FILE__, __LINE__);
        }
        $this->handlerModule = $handlerModule;

        return $this;
    }

    /**
     * <Описание>
     *
     * @return string|null
     * @unittest
     */
    public function getHandlerClass ()
    {
        return $this->handlerClass;
    }

    /**
     * <Описание>
     *
     * @return string|null
     * @unittest
     */
    public function getHandlerMethod ()
    {
        return $this->handlerMethod;
    }

    /**
     * <Описание>
     *
     * @param string $handlerClass
     * @param string $handlerMethod
     *
     * @return $this
     * @throws ClassNotFoundException
     * @throws MethodNotFoundException
     * @unittest
     */
    public function setHandlerClassMethod (string $handlerClass, string $handlerMethod)
    {
        if (!class_exists($handlerClass))
        {
            throw new ClassNotFoundException($handlerClass,__FILE__,__LINE__);
        }
        if (!method_exists($handlerClass,$handlerMethod))
        {
            throw new MethodNotFoundException($handlerClass, $handlerMethod, __FILE__, __LINE__);
        }
        $this->handlerClass = $handlerClass;
        $this->handlerMethod = $handlerMethod;

        return $this;
    }

    /**
     * <Описание>
     *
     * @return array
     * @unittest
     */
    public function getHandlerMethodArg ()
    {
        return $this->handlerMethodArg;
    }

    /**
     * <Описание>
     *
     * @param array $handlerMethodArg
     *
     * @return $this
     * @unittest
     */
    public function setHandlerMethodArg (array $handlerMethodArg = [])
    {
        $this->handlerMethodArg = $handlerMethodArg;

        return $this;
    }

}
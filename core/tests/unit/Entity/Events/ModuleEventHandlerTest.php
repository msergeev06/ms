<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Events\ModuleEventHandler;

/**
 * Класс ${NAMESPACE}\ModuleEventHandlerTest
 * Тесты класса \Ms\Core\Entity\Events\ModuleEventHandler
 */
class ModuleEventHandlerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ModuleEventHandler */
    protected $ob = null;

    protected function setUp ()
    {
        $this->ob = new ModuleEventHandler('core','OnTest');
    }

    /**
     * @covers \Ms\Core\Entity\Events\ModuleEventHandler::setCallback
     * @covers \Ms\Core\Entity\Events\ModuleEventHandler::getCallback
     */
    public function testGetCallback ()
    {
        try
        {
            $this->ob->setCallback(self::class, 'eventHandler');
        }
        catch (\Ms\Core\Exceptions\Classes\ClassNotFoundException $e)
        {
            if (!class_exists(self::class))
            {
                $this->assertTrue(true);
            }
            else
            {
                $this->assertTrue(false, 'Wrong exception: ' . $e->getMessage());
            }
            return;
        }
        catch (\Ms\Core\Exceptions\Classes\MethodNotFoundException $e)
        {
            if (!method_exists(self::class, 'eventHandler'))
            {
                $this->assertTrue(true);
            }
            else
            {
                $this->assertTrue(false, 'Wrong exception: ' . $e->getMessage());
            }
            return;
        }
        $callback = $this->ob->getCallback();
        $this->assertTrue(is_array($callback));
        $this->assertArrayHasKey(0, $callback);
        $this->assertEquals(self::class, $callback[0]);
        $this->assertArrayHasKey(1, $callback);
        $this->assertEquals('eventHandler', $callback[1]);
    }

    /**
     * @covers \Ms\Core\Entity\Events\ModuleEventHandler::setHandlerModule
     * @covers \Ms\Core\Entity\Events\ModuleEventHandler::getHandlerModule
     */
    public function testGetHandlerModule ()
    {
        try
        {
            $this->ob->setHandlerModule('core');
        }
        catch (\Ms\Core\Exceptions\Modules\WrongModuleNameException $e)
        {
            if (!\Ms\Core\Entity\Modules\Loader::issetModule('core'))
            {
                $this->assertTrue(true);
            }
            else
            {
                $this->assertTrue(false, 'Wrong exception: ' . $e->getMessage());
            }
            return;
        }
        $this->assertEquals('core',$this->ob->getHandlerModule());
    }

    /**
     * @covers \Ms\Core\Entity\Events\ModuleEventHandler::setHandlerClassMethod
     * @covers \Ms\Core\Entity\Events\ModuleEventHandler::getHandlerClass
     * @covers \Ms\Core\Entity\Events\ModuleEventHandler::getHandlerMethod
     */
    public function testGetHandlerClass ()
    {
        try
        {
            $this->ob->setHandlerClassMethod(self::class, 'eventHandler');
        }
        catch (\Ms\Core\Exceptions\Classes\ClassNotFoundException $e)
        {
            if (!class_exists(self::class))
            {
                $this->assertTrue(true);
            }
            else
            {
                $this->assertTrue(false, 'Wrong exception: ' . $e->getMessage());
            }
            return;
        }
        catch (\Ms\Core\Exceptions\Classes\MethodNotFoundException $e)
        {
            if (!method_exists(self::class, 'eventHandler'))
            {
                $this->assertTrue(true);
            }
            else
            {
                $this->assertTrue(false, 'Wrong exception: ' . $e->getMessage());
            }
            return;
        }
        $this->assertEquals(self::class, $this->ob->getHandlerClass());
        $this->assertEquals('eventHandler',$this->ob->getHandlerMethod());
    }

    /**
     * @covers \Ms\Core\Entity\Events\ModuleEventHandler::setHandlerMethodArg
     * @covers \Ms\Core\Entity\Events\ModuleEventHandler::getHandlerMethodArg
     */
    public function testGetHandlerMethodArg ()
    {
        $this->ob->setHandlerMethodArg(['one','two','three']);
        $arg = $this->ob->getHandlerMethodArg();
        $this->assertTrue($arg[0] == 'one' && $arg[1] == 'two' && $arg[2] == 'three');
    }

    public static function eventHandler ()
    {
        return 'OK';
    }
}
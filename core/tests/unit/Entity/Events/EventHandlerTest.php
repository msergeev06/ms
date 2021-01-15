<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Events\EventHandler;

/**
 * Класс \EventHandlerTest
 * Тесты класса \Ms\Core\Entity\Events\EventHandler
 */
class EventHandlerTest extends \PHPUnit\Framework\TestCase
{
    /** @var EventHandler */
    protected $ob = null;

    protected function setUp ()
    {
        $this->ob = new EventHandler('core','OnTest');
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandler::getEventModule
     */
    public function testGetEventModule ()
    {
        $this->assertEquals('core',$this->ob->getEventModule());
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandler::getEventID
     */
    public function testGetEventID ()
    {
        $this->assertEquals('OnTest',$this->ob->getEventID());
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandler::getCallback
     * @covers \Ms\Core\Entity\Events\EventHandler::setCallback
     */
    public function testGetCallback ()
    {
        try
        {
            $this->ob->setCallback(self::class, 'eventHandler');
            $callback = $this->ob->getCallback();
            $this->assertEquals(self::class,$callback[0]);
            $this->assertEquals('eventHandler',$callback[1]);
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
        }
        catch (\Ms\Core\Exceptions\Classes\MethodNotFoundException $e)
        {
            if (!method_exists(self::class,'eventHandler'))
            {
                $this->assertTrue(true);
            }
            else
            {
                $this->assertTrue(false, 'Wrong exception: ' . $e->getMessage());
            }
        }
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandler::getSort
     * @covers \Ms\Core\Entity\Events\EventHandler::setSort
     */
    public function testGetSort ()
    {
        $this->ob->setSort(123);
        $this->assertEquals(123, $this->ob->getSort());
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandler::setFileFullPath
     * @covers \Ms\Core\Entity\Events\EventHandler::getFileFullPath
     * @covers \Ms\Core\Entity\Events\EventHandler::unsetFileFullPath
     */
    public function testGetFileFullPath ()
    {
        try
        {
            $this->ob->setFileFullPath(__FILE__);
            $this->assertEquals(__FILE__,$this->ob->getFileFullPath());
        }
        catch (\Ms\Core\Exceptions\IO\FileNotFoundException $e)
        {
            if (!file_exists(__FILE__))
            {
                $this->assertTrue(true);
            }
            else
            {
                $this->assertTrue(false, 'Wrong exception: '.$e->getMessage());
            }
        }
        $this->ob->unsetFileFullPath();
        $this->assertFalse($this->ob->getFileFullPath());
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandler::getHandlerModule
     */
    public function testGetHandlerModule ()
    {
        $this->assertTrue(is_null($this->ob->getHandlerModule()));
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandler::getHandlerMethodArg
     */
    public function testGetHandlerMethodArg ()
    {
        $ar = $this->ob->getHandlerMethodArg();
        $this->assertTrue(is_array($ar) && empty($ar));
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandler::getHash
     */
    public function testGetHash ()
    {
        $arHash = [
            'FROM_MODULE' => $this->ob->getEventModule(),
            'EVENT_ID' => $this->ob->getEventID(),
            'SORT' => $this->ob->getSort(),
            'CALLBACK' => $this->ob->getCallback(),
            'FULL_PATH' => $this->ob->getFileFullPath()
        ];
        $this->assertEquals(md5(serialize($arHash)),$this->ob->getHash());
    }

    public static function eventHandler ()
    {
        return 'OK';
    }
}
<?php
require_once(dirname(__FILE__) . '/../../../autoloader.php');

use Ms\Core\Api\Events;

/**
 * Класс \EventsTest
 * Тесты класса Ms\Core\Api\Events
 */
class EventsTest extends \PHPUnit\Framework\TestCase
{
    protected $app;

    /**
     * @covers \Ms\Core\Api\Events::getEventController
     */
    public function testGetEventController ()
    {
        $res = Events::getInstance()->getEventController();
        $this->assertInstanceOf('Ms\Core\Entity\Events\EventController', $res);
    }

    /**
     * @covers \Ms\Core\Api\Events::getEventRegistrar
     */
    public function testGetEventRegistrar ()
    {
        $res = Events::getInstance()->getEventRegistrar();
        $this->assertInstanceOf('Ms\Core\Entity\Events\EventRegistrar', $res);
    }

    /**
     * @covers \Ms\Core\Api\Events::getOrmEventHandlersTable
     */
    public function testGetOrmEventHandlersTable ()
    {
        $res = Events::getInstance()->getOrmEventHandlersTable();
        $this->assertInstanceOf('Ms\Core\Entity\Db\Tables\ORMController', $res);
    }

    /**
     * @covers \Ms\Core\Api\Events::addEventHandler
     */
    public function testAddEventHandler ()
    {
        $class = self::class;
        $res = Events::getInstance()->addEventHandler(
            'core',
            'OnTest',
            $class,
            'onTestHandler'
        );
        $this->assertInstanceOf('Ms\Core\Entity\Events\EventRegistrar', $res);
        if ($res)
        {
            $this->assertTrue($res->getEventHandlersCollection()->issetByEvent('core','OnTest'));
        }
    }

    /**
     * @covers \Ms\Core\Api\Events::runEvents
     */
    public function testRunEvents ()
    {
        $res = Events::getInstance()->addEventHandler(
            'core',
            'OnTest',
            self::class,
            'onTestHandler'
        );
        if ($res)
        {
            $time = time ();
            $result = Events::getInstance()->runEvents('core','OnTest', [$time]);
            $this->assertTrue($result);
        }
    }

    /**
     * @covers \Ms\Core\Api\Events::getEvents
     */
    public function testGetEvents ()
    {
        Events::getInstance()->addEventHandler(
            'core',
            'OnTest',
            self::class,
            'onTestHandler'
        );
        $res = Events::getInstance()->getEvents('core','OnTest');
        $this->assertFalse(empty($res));
        $this->assertArrayHasKey(100,$res,print_r($res,true));
        $this->assertInstanceOf('\Ms\Core\Entity\Events\EventHandler',$res[100][0]);
    }

    /**
     * @covers \Ms\Core\Api\Events::execute
     */
    public function testExecute ()
    {
        Events::getInstance()->addEventHandler(
            'core',
            'OnTest',
            self::class,
            'onTestHandler'
        );
        $res = Events::getInstance()->getEvents('core','OnTest');
        $this->assertFalse(empty($res));
        $this->assertArrayHasKey(100,$res);
        $tmp = $res[100];
        $this->assertArrayHasKey(0,(array)$tmp);
        $handler = $res[100][0];
        $this->assertInstanceOf('\Ms\Core\Entity\Events\EventHandler',$handler);
        if ($handler instanceof \Ms\Core\Entity\Events\EventHandler)
        {
            $time = time ();
            $result = Events::getInstance()->execute($handler, [$time]);
            $this->assertEquals('Event handler is OK in '.$time,$result);
        }
        else
        {
            $this->assertTrue(false);
        }
    }

    public static function onTestHandler ($time = null)
    {
        if (is_null($time))
        {
            $time = time ();
        }
        return 'Event handler is OK in '.$time;
    }

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance();
        $this->app
            ->setSettings()
            ->setApplicationParametersCollection()
            ->setConnectionPool()
        ;
    }
}

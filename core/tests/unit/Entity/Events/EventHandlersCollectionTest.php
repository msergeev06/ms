<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Events\EventHandlersCollection;

/**
 * Класс \EventHandlersCollectionTest
 * Тесты класса \Ms\Core\Entity\Events\EventHandlersCollection
 */
class EventHandlersCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var EventHandlersCollection */
    protected $ob = null;
    /** @var \Ms\Core\Entity\Events\EventHandler */
    protected $handler = null;

    protected function setUp ()
    {
        $this->ob = new EventHandlersCollection();
        $this->handler = new \Ms\Core\Entity\Events\EventHandler('core','OnTest');
        $this->handler->setCallback(self::class, 'eventHandler');
        $this->ob->addHandler($this->handler);
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandlersCollection::addHandler
     * @covers \Ms\Core\Entity\Events\EventHandlersCollection::issetByHash
     * @covers \Ms\Core\Entity\Events\EventHandlersCollection::issetByClassMethod
     * @covers \Ms\Core\Entity\Events\EventHandlersCollection::issetByEvent
     */
    public function testAddHandler ()
    {
        $this->assertTrue($this->ob->issetByHash($this->handler->getHash()));
        $this->assertTrue($this->ob->issetByClassMethod(self::class, 'eventHandler'));
        $this->assertTrue($this->ob->issetByEvent('core','OnTest'));
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandlersCollection::getHandlerByHash
     */
    public function testGetHandlerByHash ()
    {
        $get = $this->ob->getHandlerByHash($this->handler->getHash());
        $this->assertEquals($get->getHash(), $this->handler->getHash());
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandlersCollection::getByClassMethod
     */
    public function testGetByClassMethod ()
    {
        $get2 = $this->ob->getByClassMethod(self::class, 'eventHandler');
        $this->assertEquals($get2->getHash(), $this->handler->getHash());
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventHandlersCollection::getListByEvent
     */
    public function testGetListByEvent ()
    {
        $arList = $this->ob->getListByEvent('core','OnTest');
        $this->assertEquals($arList[100][0]->getHash(),$this->handler->getHash());
    }

    public static function eventHandler ()
    {
        return 'OK';
    }
}
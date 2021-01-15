<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Errors\Error;

/**
 * Класс \ErrorTest
 * Тесты класса \Ms\Core\Entity\Errors\Error
 */
class ErrorTest extends \PHPUnit\Framework\TestCase
{
    /** @var Error */
    protected $ob = null;

    protected function setUp ()
    {
        $this->ob = new Error();
    }

    /**
     * @covers \Ms\Core\Entity\Errors\Error::setCode
     * @covers \Ms\Core\Entity\Errors\Error::getCode
     */
    public function testSetCode ()
    {
        $this->ob->setCode('TEST');
        $this->assertEquals('TEST',$this->ob->getCode());
    }

    /**
     * @covers \Ms\Core\Entity\Errors\Error::setMessage
     * @covers \Ms\Core\Entity\Errors\Error::getMessage
     */
    public function testSetMessage ()
    {
        $this->ob->setMessage('Test message');
        $this->assertEquals('Test message',$this->ob->getMessage());
    }

    /**
     * @covers \Ms\Core\Entity\Errors\Error::__toString
     */
    public function testToString ()
    {
        $this->ob->setCode('TEST');
        $this->ob->setMessage('Test message');
        $this->assertEquals('Ошибка [TEST]: Test message',(string)$this->ob);
        $this->assertEquals('Ошибка [TEST]: Test message',$this->ob->__toString());
    }
}
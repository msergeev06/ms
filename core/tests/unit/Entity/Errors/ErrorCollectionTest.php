<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Errors\ErrorCollection;

/**
 * Класс \ErrorCollectionTest
 * Тесты класса \Ms\Core\Entity\Errors\ErrorCollection
 */
class ErrorCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var ErrorCollection */
    protected $ob = null;

    protected function setUp ()
    {
        $this->ob = new ErrorCollection();
    }

    /**
     * @covers \Ms\Core\Entity\Errors\ErrorCollection::addError
     * @covers \Ms\Core\Entity\Errors\ErrorCollection::getError
     * @covers \Ms\Core\Entity\Errors\ErrorCollection::issetError
     * @covers \Ms\Core\Entity\Errors\ErrorCollection::unsetError
     */
    public function testAddError ()
    {
        $this->ob->addError(new \Ms\Core\Entity\Errors\Error('Test message','TEST1'));
        $this->assertEquals('Test message',$this->ob->getError('TEST1')->getMessage());
        $this->assertTrue($this->ob->issetError('TEST1'));
        $this->ob->unsetError('TEST1');
        $this->assertFalse($this->ob->issetError('TEST1'));
    }
}
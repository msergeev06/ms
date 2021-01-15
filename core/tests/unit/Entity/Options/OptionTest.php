<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Options\Option;

/**
 * Класс \OptionTest
 * Тесты класса \Ms\Core\Entity\Options\Option
 */
class OptionTest extends \PHPUnit\Framework\TestCase
{
    /** @var Option */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
            ->setConnectionPool()
            ->setApplicationParametersCollection()
        ;
        $this->app->getSettings()->mergeLocalSettings();
        $this->app->getConnectionPool()->getConnection()->connect();

        $this->ob = new Option('core','test_option','test_string');
    }

    /**
     * @covers \Ms\Core\Entity\Options\Option::getModuleName
     */
    public function testGetModuleName ()
    {
        $this->assertEquals('core',$this->ob->getModuleName());
    }

    /**
     * @covers \Ms\Core\Entity\Options\Option::getOptionName
     */
    public function testGetOptionName ()
    {
        $this->assertEquals('test_option',$this->ob->getOptionName());
    }

    /**
     * @covers \Ms\Core\Entity\Options\Option::setOptionFullName
     * @covers \Ms\Core\Entity\Options\Option::getOptionFullName
     */
    public function testSetOptionFullName ()
    {
        $this->ob->setOptionFullName('ms.dobrozhil','option_test');
        $this->assertEquals('ms.dobrozhil:option_test',$this->ob->getOptionFullName());
    }

    /**
     * @covers \Ms\Core\Entity\Options\Option::getOptionValue
     */
    public function testGetOptionValue ()
    {
        $this->assertEquals('test_string',$this->ob->getOptionValue());
    }

    /**
     * @covers \Ms\Core\Entity\Options\Option::setOptionValue
     * @covers \Ms\Core\Entity\Options\Option::getValueInt
     * @covers \Ms\Core\Entity\Options\Option::getValueFloat
     * @covers \Ms\Core\Entity\Options\Option::getValueBool
     * @covers \Ms\Core\Entity\Options\Option::getValueString
     */
    public function testSetOptionValue ()
    {
        $this->ob->setOptionValue('123.56ab');
        $this->assertEquals(123,$this->ob->getValueInt());
        $this->assertEquals(123.56,$this->ob->getValueFloat());
        $this->assertTrue($this->ob->getValueBool());
        $this->assertEquals('123.56ab',$this->ob->getValueString());
    }
}
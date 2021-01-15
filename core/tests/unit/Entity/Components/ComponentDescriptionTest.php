<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use Ms\Core\Entity\Components\ComponentDescription;

/**
 * Класс \ComponentDescriptionTest
 * Тест класса \Ms\Core\Entity\Components\ComponentDescription
 */
class ComponentDescriptionTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {

    }

    public function classNameProvider ()
    {
        return [
            ['Ms\Core\Entity\Components\Component'],
            ['Csdfgsggr']
        ];
    }

    /**
     * @covers \Ms\Core\Entity\Components\ComponentDescription::__construct
     * @covers \Ms\Core\Entity\Components\ComponentDescription::getClassName
     * @dataProvider classNameProvider
     *
     * @param string $className
     */
    public function testConstruct ($className)
    {
        try
        {
            $obj = new ComponentDescription($className);
            $this->assertInstanceOf(\Ms\Core\Entity\Components\ComponentDescription::class, $obj);
            $this->assertEquals($className, $obj->getClassName());
        }
        catch (\Exception $e)
        {
            $this->assertTrue(!class_exists($className));
        }
    }

    /**
     * @covers \Ms\Core\Entity\Components\ComponentDescription::setName
     * @covers \Ms\Core\Entity\Components\ComponentDescription::getName
     */
    public function testSetName ()
    {
        try
        {
            $obj = new ComponentDescription(Ms\Core\Entity\Components\Component::class);
            $obj->setName('Test');
            $this->assertEquals('Test', $obj->getName());
        }
        catch (\Ms\Core\Exceptions\Classes\ClassNotFoundException $e)
        {
            $this->assertTrue(false);
        }
    }

    /**
     * @covers \Ms\Core\Entity\Components\ComponentDescription::setDescription
     * @covers \Ms\Core\Entity\Components\ComponentDescription::getDescription
     */
    public function testSetDescription ()
    {
        try
        {
            $obj = new ComponentDescription(Ms\Core\Entity\Components\Component::class);
            $obj->setDescription('Test Description');
            $this->assertEquals('Test Description', $obj->getDescription());
        }
        catch (\Ms\Core\Exceptions\Classes\ClassNotFoundException $e)
        {
            $this->assertTrue(false);
        }
    }

    /**
     * @covers \Ms\Core\Entity\Components\ComponentDescription::setModuleName
     * @covers \Ms\Core\Entity\Components\ComponentDescription::getModuleName
     */
    public function testSetModuleName ()
    {
        try
        {
            $obj = new ComponentDescription(Ms\Core\Entity\Components\Component::class);
            $obj->setModuleName('core');
            $this->assertEquals('core', $obj->getModuleName());
        }
        catch (\Ms\Core\Exceptions\Classes\ClassNotFoundException $e)
        {
            $this->assertTrue(false);
        }
    }
}
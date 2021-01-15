<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Options\Options;

/**
 * Класс \OptionsTest
 * Тесты класса \Ms\Core\Entity\Options\Options
 */
class OptionsOptionsTest extends \PHPUnit\Framework\TestCase
{
    /** @var Options */
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

        $this->ob = Options::getInstance();
    }

    /**
     * @covers \Ms\Core\Entity\Options\Options::getOptionBool
     */
    public function testGetOptionBool ()
    {
        $this->assertTrue($this->ob->getOptionBool('core','test',true));
    }

    /**
     * @covers \Ms\Core\Entity\Options\Options::getOptionFloat
     */
    public function testGetOptionFloat ()
    {
        $this->assertEquals(
            123.56,
            $this->ob->getOptionFloat(
                'core',
                'test_float',
                123.56
            )
        );
    }

    /**
     * @covers \Ms\Core\Entity\Options\Options::getOptionFullName
     */
    public function testGetOptionFullName ()
    {
        $this->assertEquals(
            'ms.core:test',
            $this->ob->getOptionFullName(
                'core',
                'test'
            )
        );
    }

    /**
     * @covers \Ms\Core\Entity\Options\Options::getOptionInt
     */
    public function testGetOptionInt ()
    {
        $this->assertEquals(
            123,
            $this->ob->getOptionInt(
                'core',
                'test_int',
                123
            )
        );
    }

    /**
     * @covers \Ms\Core\Entity\Options\Options::getOptionStr
     */
    public function testGetOptionStr ()
    {
        $this->assertEquals(
            'test_string',
            $this->ob->getOptionStr(
                'core',
                'test_string',
                'test_string'
            )
        );
    }

    /**
     * @covers \Ms\Core\Entity\Options\Options::getOptionsCollection
     */
    public function testGetOptionsCollection ()
    {
        $this->assertInstanceOf(
            \Ms\Core\Entity\Options\OptionsCollection::class,
            $this->ob->getOptionsCollection()
        );
    }

    /**
     * @covers \Ms\Core\Entity\Options\Options::setDefaultOption
     */
    public function testSetDefaultOption ()
    {
        $this->ob->setDefaultOption('core','test','test_string');
        $this->assertEquals(
            'test_string',
            $this
                ->ob
                ->getOptionsCollection()
                ->getOptionByFullName(
                    'ms.core:test'
                )
                ->getValueString()
        );
    }

    /**
     * @covers \Ms\Core\Entity\Options\Options::setDefaultOptionsFromFile
     */
    public function testSetDefaultOptionsFromFile ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Options\Options::setDefaultOptionsFromFile');
    }

    /**
     * @covers \Ms\Core\Entity\Options\Options::setOption
     */
    public function testSetOption ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Options\Options::setOption');
    }
}
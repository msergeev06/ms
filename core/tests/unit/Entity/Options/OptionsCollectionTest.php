<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Options\OptionsCollection;

/**
 * Класс \OptionsCollectionTest
 * Тесты класса \Ms\Core\Entity\Options\OptionsCollection
 */
class OptionsCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var OptionsCollection */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();

        $this->ob = new OptionsCollection();
    }

    /**
     * @covers \Ms\Core\Entity\Options\OptionsCollection::setOption
     * @covers \Ms\Core\Entity\Options\OptionsCollection::getOption
     * @covers \Ms\Core\Entity\Options\OptionsCollection::getOptionByFullName
     */
    public function testSetOption ()
    {
        $this->ob->setOption(
            new \Ms\Core\Entity\Options\Option(
                'core',
                'test_option',
                'test_string'
            )
        );
        $this->assertInstanceOf(
            \Ms\Core\Entity\Options\Option::class,
            $this->ob->getOption(
                'core',
                'test_option'
            )
        );
        $this->assertEquals(
            'test_string',
            $this->ob->getOptionByFullName('ms.core:test_option')
                ->getValueString()
        );
    }

    /**
     * @covers \Ms\Core\Entity\Options\OptionsCollection::setOptionValue
     */
    public function testSetOptionValue ()
    {
        $this->ob->setOptionValue('core','test_option','another_string');
        $this->assertEquals(
            'another_string',
            $this->ob->getOptionByFullName('ms.core:test_option')->getValueString()
        );
    }

    /**
     * @covers \Ms\Core\Entity\Options\OptionsCollection::unsetOption
     */
    public function testUnsetOption ()
    {
        $this->ob->setOption(new \Ms\Core\Entity\Options\Option('core','test','test'));
        $this->ob->unsetOption('core','test');
        $this->assertTrue(is_null($this->ob->getOptionByFullName('ms.core:test')));
    }
}
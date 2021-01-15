<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\System\ApplicationParametersCollection;

/**
 * Класс \ApplicationParametersCollectionTest
 * Тесты класса \Ms\Core\Entity\System\ApplicationParametersCollection
 */
class ApplicationParametersCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var ApplicationParametersCollection */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();

        $this->ob = new ApplicationParametersCollection();
    }

    /**
     * @covers \Ms\Core\Entity\System\ApplicationParametersCollection::addParameter
     * @covers \Ms\Core\Entity\System\ApplicationParametersCollection::getParameter
     * @covers \Ms\Core\Entity\System\ApplicationParametersCollection::unsetParameter
     * @covers \Ms\Core\Entity\System\ApplicationParametersCollection::issetParameter
     */
    public function testAddParameter ()
    {
        $this->ob->addParameter('test_param','test_string');
        $this->assertEquals('test_string',$this->ob->getParameter('test_param'));
        $this->ob->unsetParameter('test_param');
        $this->assertFalse($this->ob->issetParameter('test_param'));
    }

    /**
     * @covers \Ms\Core\Entity\System\ApplicationParametersCollection::clearAllParameters
     */
    public function testClearAllParameters ()
    {
        $this->ob->addParameter('test1','test1');
        $this->ob->addParameter('test2','test2');
        $this->ob->clearAllParameters();
        $this->assertFalse($this->ob->issetParameter('test1'));
        $this->assertFalse($this->ob->issetParameter('test2'));
    }
}
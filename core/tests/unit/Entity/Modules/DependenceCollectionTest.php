<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Modules\DependenceCollection;

/**
 * Класс \DependenceCollectionTest
 * Тесты класса \Ms\Core\Entity\Modules\DependenceCollection
 */
class DependenceCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var DependenceCollection */
    protected $ob = null;

    protected function setUp ()
    {
        $this->ob = new DependenceCollection();
    }

    /**
     * @covers \Ms\Core\Entity\Modules\DependenceCollection::addDependence
     * @covers \Ms\Core\Entity\Modules\DependenceCollection::getDependence
     * @covers \Ms\Core\Entity\Modules\DependenceCollection::issetDependence
     */
    public function testAddDependence ()
    {
        $this->ob->addDependence(
            (new \Ms\Core\Entity\Modules\Dependence('core'))
                ->setNeedVersion('1.0.1')
        );
        $isSet = $this->ob->issetDependence('core');
        $this->assertTrue($isSet);
        if ($isSet)
        {
            $this->assertEquals(
                '1.0.1',
                $this->ob->getDependence('core')->getNeedVersion()
            );
        }
        else
        {
            $this->assertTrue(is_null($this->ob->getDependence('core')));
        }
    }

}
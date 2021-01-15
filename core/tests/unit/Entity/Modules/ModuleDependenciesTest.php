<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Modules\ModuleDependencies;

/**
 * Класс \ModuleDependenciesTest
 * Тесты класса \Ms\Core\Entity\Modules\ModuleDependencies
 */
class ModuleDependenciesTest extends \PHPUnit\Framework\TestCase
{
    /** @var ModuleDependencies */
    protected $ob = null;

    protected function setUp ()
    {
        $this->ob = new ModuleDependencies();
    }

    /**
     * @covers \Ms\Core\Entity\Modules\ModuleDependencies::setRequiredDependenciesCollection
     * @covers \Ms\Core\Entity\Modules\ModuleDependencies::getRequiredDependenciesCollection
     */
    public function testSetRequiredDependenciesCollection ()
    {
        $this->ob->setRequiredDependenciesCollection(
            (new \Ms\Core\Entity\Modules\DependenceCollection())
                ->addDependence(
                    (new \Ms\Core\Entity\Modules\Dependence('core'))
                        ->setNeedVersion('1.0.1')
                )
        );

        $this->assertEquals(
            '1.0.1',
            $this->ob
                ->getRequiredDependenciesCollection()
                    ->getDependence('core')
                        ->getNeedVersion()
        );
    }

    /**
     * @covers \Ms\Core\Entity\Modules\ModuleDependencies::setAdditionalDependenciesCollection
     * @covers \Ms\Core\Entity\Modules\ModuleDependencies::getAdditionalDependenciesCollection
     */
    public function testSetAdditionalDependenciesCollection ()
    {
        $this->ob->setAdditionalDependenciesCollection(
            (new \Ms\Core\Entity\Modules\DependenceCollection())
                ->addDependence(
                    (new \Ms\Core\Entity\Modules\Dependence('core'))
                        ->setNeedVersion('1.0.1')
                )
        );

        $this->assertEquals(
            '1.0.1',
            $this->ob
                ->getAdditionalDependenciesCollection()
                    ->getDependence('core')
                        ->getNeedVersion()
        );
    }
}
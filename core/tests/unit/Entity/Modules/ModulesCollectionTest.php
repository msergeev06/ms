<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Modules\ModulesCollection;

/**
 * Класс \ModulesCollectionTest
 * Тесты класса \Ms\Core\Entity\Modules\ModulesCollection
 */
class ModulesCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var ModulesCollection */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();

        $this->ob = new ModulesCollection();
    }

    /**
     * @covers \Ms\Core\Entity\Modules\ModulesCollection::addModule
     * @covers \Ms\Core\Entity\Modules\ModulesCollection::isExists
     * @covers \Ms\Core\Entity\Modules\ModulesCollection::getModule
     */
    public function testAddModule ()
    {
        $this->ob->addModule('ms.dobrozhil');
        $this->assertTrue($this->ob->isExists('ms.dobrozhil'));
        $this->assertInstanceOf(
            \Ms\Core\Entity\Modules\Module::class,
            $this->ob->getModule('ms.dobrozhil')
        );
    }
}
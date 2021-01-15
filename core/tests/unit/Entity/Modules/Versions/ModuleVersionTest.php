<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Modules\Versions\ModuleVersion;

/**
 * Класс \ModuleVersionTest
 * Тесты класса \Ms\Core\Entity\Modules\Versions\ModuleVersion
 */
class ModuleVersionTest extends \PHPUnit\Framework\TestCase
{
    /** @var ModuleVersion */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();

        $this->ob = new ModuleVersion('1.0.0', new \Ms\Core\Entity\Type\Date());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\ModuleVersion::setVersion
     * @covers \Ms\Core\Entity\Modules\Versions\ModuleVersion::getVersion
     * @covers \Ms\Core\Entity\Modules\Versions\ModuleVersion::getVersionNumber
     */
    public function testSetVersion ()
    {
        $this->ob->setVersion(new \Ms\Core\Entity\Modules\Versions\Version('1.0.1'));
        $version = $this->ob->getVersion();
        $this->assertInstanceOf(\Ms\Core\Entity\Modules\Versions\Version::class,$version);
        $this->assertEquals('1.0.1',$this->ob->getVersionNumber());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\ModuleVersion::setVersionDate
     * @covers \Ms\Core\Entity\Modules\Versions\ModuleVersion::getVersionDate
     */
    public function testSetVersionDate ()
    {
        $now = new \Ms\Core\Entity\Type\Date();
        $this->ob->setVersionDate($now);
        $this->assertEquals($now->getDateDB(),$this->ob->getVersionDate()->getDateDB());
    }
}
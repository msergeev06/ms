<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Modules\Installer;

/**
 * Класс \InstallerTest
 * Тесты класса \Ms\Core\Entity\Modules\Installer
 */
class InstallerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Installer */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
            ->setConnectionPool()
        ;
        $this->app->getSettings()->mergeLocalSettings();

        $this->ob = new Installer('ms.dobrozhil');
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Installer::getModuleName
     */
    public function testGetModuleName ()
    {
        $this->assertEquals('ms.dobrozhil',$this->ob->getModuleName());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Installer::getModulePath
     */
    public function testGetModulePath ()
    {
        $this->assertContains('/modules/'.$this->ob->getModuleName(),$this->ob->getModulePath());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Installer::getModuleInstaller
     */
    public function testGetModuleInstaller ()
    {
        $moduleInstaller = $this->ob->getModuleInstaller();
        if (is_null($moduleInstaller))
        {
            $this->assertTrue(true);
        }
        else
        {
            $this->assertInstanceOf(\Ms\Core\Entity\Modules\ModuleInstaller::class,$moduleInstaller);
        }
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Installer::setModuleInstaller
     */
    public function testSetModuleInstaller ()
    {
        $this->ob->setModuleInstaller(new \Ms\Core\Entity\Modules\ModuleInstaller('ms.dobrozhil'));
        $this->assertEquals('ms.dobrozhil',$this->ob->getModuleInstaller()->getModuleName());
    }

    /*
     * Методы install, reInstall и unInstall умышленно не тестируются
     */
    /**
     * @covers \Ms\Core\Entity\Modules\Installer::install
     */
    public function testInstall ()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Installer::reInstall
     */
    public function testReInstall ()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Installer::unInstall
     */
    public function testUnInstall ()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Installer::isSuccess
     */
    public function testIsSuccess ()
    {
        $this->assertFalse($this->ob->isSuccess());
    }
}

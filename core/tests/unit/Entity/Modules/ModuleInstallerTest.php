<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Modules\ModuleInstaller;

/**
 * Класс \ModuleInstallerTest
 * Тесты класса \Ms\Core\Entity\Modules\ModuleInstaller
 */
class ModuleInstallerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ModuleInstaller */
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

        $this->ob = new ModuleInstaller('ms.dobrozhil');
    }

    /**
     * @covers \Ms\Core\Entity\Modules\ModuleInstaller::doInstall
     */
    public function testDoInstall ()
    {
        $this->assertTrue($this->ob->doInstall());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\ModuleInstaller::doUnInstall
     */
    public function testDoUnInstall ()
    {
        $this->assertTrue($this->ob->doUnInstall());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\ModuleInstaller::getModuleName
     */
    public function testGetModuleName ()
    {
        $this->assertEquals('ms.dobrozhil',$this->ob->getModuleName());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\ModuleInstaller::getModulePath
     */
    public function testGetModulePath ()
    {
        $this->assertContains('modules/ms.dobrozhil',$this->ob->getModulePath());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\ModuleInstaller::startInstallWizard
     */
    public function testStartInstallWizard ()
    {
        $this->assertTrue($this->ob->startInstallWizard());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\ModuleInstaller::startUnInstallWizard
     */
    public function testStartUnInstallWizard ()
    {
        $this->assertTrue($this->ob->startUnInstallWizard());
    }
}
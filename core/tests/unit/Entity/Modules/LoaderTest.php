<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Modules\Loader;

/**
 * Класс \LoaderTest
 * Тесты класса \Ms\Core\Entity\Modules\Loader
 */
class LoaderTest extends \PHPUnit\Framework\TestCase
{
    /** @var Loader */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();

        $this->ob = Loader::getInstance();
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::checkModuleName
     */
    public function testCheckModuleName ()
    {
        $this->assertTrue($this->ob->checkModuleName('ms.dobrozhil'));
        $this->assertFalse($this->ob->checkModuleName('sdfsdf'));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::getArrayModulesVersions
     */
    public function testGetArrayModulesVersions ()
    {
        $arModulesVersions = $this->ob->getArrayModulesVersions();
        // print_r($arModulesVersions);
        $this->assertTrue(is_array($arModulesVersions));
        $this->assertArrayHasKey('ms.dobrozhil',$arModulesVersions);
        $this->assertArrayHasKey('VERSION',$arModulesVersions['ms.dobrozhil']);
        $this->assertArrayHasKey('VERSION_DATE',$arModulesVersions['ms.dobrozhil']);
        $this->assertInstanceOf(\Ms\Core\Entity\Type\Date::class,$arModulesVersions['ms.dobrozhil']['VERSION_DATE']);
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::getModuleInfo
     */
    public function testGetModuleInfo ()
    {
        $arModuleInfo = $this->ob->getModuleInfo('ms.dobrozhil');
        // print_r($arModuleInfo);
        $this->assertTrue(is_array($arModuleInfo));
        if (!empty($arModuleInfo))
        {
            $this->assertArrayHasKey('NAME',$arModuleInfo);
            $this->assertArrayHasKey('DESCRIPTION',$arModuleInfo);
            $this->assertArrayHasKey('URL',$arModuleInfo);
            $this->assertArrayHasKey('DOCS',$arModuleInfo);
            $this->assertArrayHasKey('AUTHOR',$arModuleInfo);
            $this->assertArrayHasKey('AUTHOR_EMAIL',$arModuleInfo);
        }
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::getModulePath
     */
    public function testGetModulePath ()
    {
        $this->assertContains('modules/ms.dobrozhil',$this->ob->getModulePath('ms.dobrozhil'));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::getModuleVersion
     */
    public function testGetModuleVersion ()
    {
        $moduleVersion = $this->ob->getModuleVersion('ms.dobrozhil');
        if (is_null($moduleVersion))
        {
            $this->assertTrue(true);
        }
        else
        {
            $this->assertInstanceOf(\Ms\Core\Entity\Modules\Versions\ModuleVersion::class,$moduleVersion);
        }
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::getModuleVersionDate
     */
    public function testGetModuleVersionDate ()
    {
        $moduleVersion = $this->ob->getModuleVersion('ms.dobrozhil');
        if (is_null($moduleVersion))
        {
            $this->assertTrue(true);
        }
        else
        {
            // $this->assertNotEquals('',$this->ob->getModuleVersionDate())
            $this->assertInstanceOf(
                \Ms\Core\Entity\Type\Date::class,
                $this->ob->getModuleVersionDate('ms.dobrozhil')
            );
        }
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::getModuleVersionNumber
     */
    public function testGetModuleVersionNumber ()
    {
        $moduleVersion = $this->ob->getModuleVersion('ms.dobrozhil');
        if (is_null($moduleVersion))
        {
            $this->assertTrue(true);
        }
        else
        {
            $this->assertNotEquals('',$this->ob->getModuleVersionNumber('ms.dobrozhil'));
        }
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::includeModule
     */
    public function testIncludeModule ()
    {
        $this->assertTrue(Loader::includeModule('ms.dobrozhil'));
        $this->assertTrue(function_exists('shutdown'));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::isIncludedModule
     */
    public function testIsIncludedModule ()
    {
        Loader::includeModule('ms.dobrozhil');
        $this->assertTrue($this->ob->isIncludedModule('ms.dobrozhil'));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::isInstalled
     */
    public function testIsInstalled ()
    {
        $this->assertTrue(Loader::isInstalled('ms.dobrozhil'));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Loader::issetModule
     */
    public function testIssetModule ()
    {
        $this->assertTrue(Loader::issetModule('ms.dobrozhil'));
    }
}
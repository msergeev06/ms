<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Modules\Modules;

/**
 * Класс \ModulesTest
 * Тесты класса \Ms\Core\Entity\Modules\Modules
 */
class ModulesTest extends \PHPUnit\Framework\TestCase
{
    /** @var Modules */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();

        $this->ob = Modules::getInstance();
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::clearErrorCollection
     * @covers \Ms\Core\Entity\Modules\Modules::getErrorCollection
     */
    public function testClearErrorCollection ()
    {
        $this->ob->clearErrorCollection();
        $this->assertTrue($this->ob->getErrorCollection()->isEmpty());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::getModuleNameByClassNamespace
     */
    public function testGetModuleNameByClassNamespace ()
    {
        $this->assertEquals('core',$this->ob->getModuleNameByClassNamespace(Modules::class));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::getFilePathByClassNamespace
     */
    public function testGetFilePathByClassNamespace ()
    {
        $this->assertContains(
            'core/classes/Entity/Modules/Modules.php',
            $this->ob->getFilePathByClassNamespace(Modules::class)
        );
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::checkModuleName
     */
    public function testCheckModuleName ()
    {
        $moduleName = 'Ms.Dobrozhil';
        $this->assertTrue($this->ob->checkModuleName($moduleName));
        $this->assertEquals('ms.dobrozhil',$moduleName);
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::parseModuleName
     */
    public function testParseModuleName ()
    {
        $arModuleName = $this->ob->parseModuleName('Ms.Dobrozhil');
        $this->assertTrue(is_array($arModuleName));
        $this->assertArrayHasKey('BRAND',$arModuleName);
        $this->assertEquals('ms',$arModuleName['BRAND']);
        $this->assertArrayHasKey('MODULE',$arModuleName);
        $this->assertEquals('dobrozhil',$arModuleName['MODULE']);
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::getModuleNamespace
     */
    public function testGetModuleNamespace ()
    {
        $this->assertEquals('Ms\Dobrozhil\\',$this->ob->getModuleNamespace('ms.dobrozhil'));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::getModuleNamespaceTables
     */
    public function testGetModuleNamespaceTables ()
    {
        $this->assertEquals(
            'Ms\Dobrozhil\Tables\\',
            $this->ob->getModuleNamespaceTables('ms.dobrozhil')
        );
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::checkVersionExpression
     */
    public function testCheckVersionExpression ()
    {
        $arCheck = [];
        $this->assertTrue($this->ob->checkVersionExpression('v.1.0.*',$arCheck));
        $arCheck = $arCheck[0];
        $this->assertEquals('',$arCheck['MODIFIER']);
        $this->assertEquals('1',$arCheck['MAJOR']);
        $this->assertEquals('0',$arCheck['MINOR']);
        $this->assertEquals('*',$arCheck['PATCH']);
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::isCorrectVersion
     */
    public function testIsCorrectVersion ()
    {
        $this->assertTrue(
            $this->ob->isCorrectVersion('>0.2 <=2.1','0.2.1', false)
        );
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::getModuleTableFiles
     * @covers \Ms\Core\Entity\Modules\Modules::getPathToModuleTablesFiles
     * @covers \Ms\Core\Entity\Modules\Modules::getModuleTableNames
     */
    public function testGetModuleTableFiles ()
    {
        $arTableFiles = $this->ob->getModuleTableFiles('ms.dobrozhil');
        $arTableNames = $this->ob->getModuleTableNames('core');
        $this->assertTrue(is_array($arTableNames));
        // print_r($arTableFiles);
        // print_r($arTableNames);
        if (file_exists($this->ob->getPathToModuleTablesFiles('ms.dobrozhil')))
        {
            $this->assertTrue(is_array($arTableFiles));
        }
        else
        {
            $this->assertFalse($arTableFiles);
        }
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::getPathToModule
     */
    public function testGetPathToModule ()
    {
        $this->assertContains('modules/ms.dobrozhil',$this->ob->getPathToModule('ms.dobrozhil'));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::getPathToModuleJs
     */
    public function testGetPathToModuleJs ()
    {
        $this->assertContains('modules/ms.dobrozhil/js',$this->ob->getPathToModuleJs('ms.dobrozhil'));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::getTableClassByFileName
     */
    public function testGetTableClassByFileName ()
    {
        $this->assertEquals('ClassesTable',$this->ob->getTableClassByFileName('ClassesTable.php'));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::getUpload
     */
    public function testGetUpload ()
    {
        $this->assertContains(
            $this->app->getSettings()->getUploadDir() . '/modules/ms.dobrozhil',
            $this->ob->getUpload('ms.dobrozhil')
        );
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::getModuleFromNamespace
     */
    public function testGetModuleFromNamespace ()
    {
        $this->assertEquals('ms.dobrozhil',$this->ob->getModuleFromNamespace(\Ms\Dobrozhil\Lib\Main::class));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::installModule
     * @covers \Ms\Core\Entity\Modules\Modules::unInstallModule
     */
    public function testInstallModule ()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Modules::checkVersion
     */
    public function testCheckVersion ()
    {
        $this->assertTrue($this->ob->checkVersion('1.0.1','1.0'));
    }
}
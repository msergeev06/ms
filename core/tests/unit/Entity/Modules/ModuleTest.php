<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Modules\Module;

/**
 * Класс \ModuleTest
 * Тесты класса \Ms\Core\Entity\Modules\Module
 */
class ModuleTest extends \PHPUnit\Framework\TestCase
{
    /** @var Module */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();

        $this->ob = new Module('ms.dobrozhil');
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Module::getModuleName
     */
    public function testGetModuleName ()
    {
        $this->assertEquals('ms.dobrozhil',$this->ob->getModuleName());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Module::setModulePath
     * @covers \Ms\Core\Entity\Modules\Module::getModulePath
     */
    public function testSetModulePath ()
    {
        $this->assertContains('modules/ms.dobrozhil',$this->ob->getModulePath());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Module::setModuleVersion
     * @covers \Ms\Core\Entity\Modules\Module::getModuleVersion
     * @covers \Ms\Core\Entity\Modules\Module::getModuleVersionNumber
     * @covers \Ms\Core\Entity\Modules\Module::getModuleVersionDate
     */
    public function testSetModuleVersion ()
    {
        $this->ob->setModuleVersion(
            new \Ms\Core\Entity\Modules\Versions\ModuleVersion(
                '1.0.0',
                new \Ms\Core\Entity\Type\Date()
            )
        );
        $this->assertInstanceOf(
            \Ms\Core\Entity\Modules\Versions\ModuleVersion::class,
            $this->ob->getModuleVersion()
        );
        $this->assertEquals('1.0.0',$this->ob->getModuleVersionNumber());
        $this->assertEquals(
            (new \Ms\Core\Entity\Type\Date())->getDateDB(),
            $this->ob->getModuleVersionDate()->getDateDB()
        );
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Module::setIncluded
     * @covers \Ms\Core\Entity\Modules\Module::isIncluded
     */
    public function testSetIncluded ()
    {
        $this->ob->setIncluded();
        $this->assertTrue($this->ob->isIncluded());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Module::setInstalled
     * @covers \Ms\Core\Entity\Modules\Module::isInstalled
     */
    public function testSetInstalled ()
    {
        $this->ob->setInstalled();
        $this->assertTrue($this->ob->isInstalled());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Module::getModuleInfo
     * @covers \Ms\Core\Entity\Modules\Module::getModuleInfoName
     * @covers \Ms\Core\Entity\Modules\Module::getModuleInfoDescription
     * @covers \Ms\Core\Entity\Modules\Module::getModuleInfoUrl
     * @covers \Ms\Core\Entity\Modules\Module::getModuleInfoUrlDocs
     * @covers \Ms\Core\Entity\Modules\Module::getModuleInfoAuthor
     * @covers \Ms\Core\Entity\Modules\Module::getModuleInfoAuthorEmail
     */
    public function testGetModuleInfo ()
    {
        $arModuleInfo = $this->ob->getModuleInfo();
        $this->assertTrue(is_array($arModuleInfo));
        if (!empty($arModuleInfo))
        {
            $this->assertArrayHasKey('NAME',$arModuleInfo);
            $this->assertEquals($arModuleInfo['NAME'],$this->ob->getModuleInfoName());
            $this->assertArrayHasKey('DESCRIPTION',$arModuleInfo);
            $this->assertEquals($arModuleInfo['DESCRIPTION'],$this->ob->getModuleInfoDescription());
            $this->assertArrayHasKey('URL',$arModuleInfo);
            $this->assertEquals($arModuleInfo['URL'], $this->ob->getModuleInfoUrl());
            $this->assertArrayHasKey('DOCS',$arModuleInfo);
            $this->assertEquals($arModuleInfo['DOCS'],$this->ob->getModuleInfoUrlDocs());
            $this->assertArrayHasKey('AUTHOR',$arModuleInfo);
            $this->assertEquals($arModuleInfo['AUTHOR'],$this->ob->getModuleInfoAuthor());
            $this->assertArrayHasKey('AUTHOR_EMAIL',$arModuleInfo);
            $this->assertEquals($arModuleInfo['AUTHOR_EMAIL'],$this->ob->getModuleInfoAuthorEmail());
        }
        else
        {
            $this->assertEquals('ms.dobrozhil',$this->ob->getModuleInfoName());
            $this->assertEquals('',$this->ob->getModuleInfoDescription());
            $this->assertEquals('', $this->ob->getModuleInfoUrl());
            $this->assertEquals('',$this->ob->getModuleInfoUrlDocs());
            $this->assertEquals('',$this->ob->getModuleInfoAuthor());
            $this->assertEquals('',$this->ob->getModuleInfoAuthorEmail());
        }
    }
}
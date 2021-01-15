<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Helpers\InstallHelper;

/**
 * Класс \InstallHelperTest
 * Тесты класса \Ms\Core\Entity\Helpers\InstallHelper
 */
class InstallHelperTest extends \PHPUnit\Framework\TestCase
{
    /** @var InstallHelper */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();

        $this->ob = InstallHelper::getInstance();
    }

    /**
     * Тут не тестируется. См. тест Lib\IO\FilesTest::testCopyDirFiles
     * @covers \Ms\Core\Entity\Helpers\InstallHelper::copyFiles
     */
    public function testCopyFiles ()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers \Ms\Core\Entity\Helpers\InstallHelper::createBackupDbForModule
     */
    public function testCreateBackupDbForModule ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Helpers\InstallHelper::createBackupDbForModule');
    }

    /**
     * Не тестируется
     * @covers \Ms\Core\Entity\Helpers\InstallHelper::createCoreTables
     */
    public function testCreateCoreTables ()
    {
        $this->assertTrue(true);
    }

    /**
     * Не тестируется
     * @covers \Ms\Core\Entity\Helpers\InstallHelper::createModuleTables
     */
    public function testCreateModuleTables ()
    {
        $this->assertTrue(true);
    }

    /**
     * Не тестируется
     * @covers \Ms\Core\Entity\Helpers\InstallHelper::dropModuleTables
     */
    public function testDropModuleTables ()
    {
        $this->assertTrue(true);
    }
}
<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Modules\Access;

/**
 * Класс \AccessTest
 * Тесты класса \Ms\Core\Entity\Modules\Access
 */
class AccessTest extends \PHPUnit\Framework\TestCase
{
    /** @var Access */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();
    }

    public function testConstants ()
    {
        $this->assertEquals('V',Access::LEVEL_MODULE_VIEW);
        $this->assertEquals('E',Access::LEVEL_MODULE_EDIT);
        $this->assertEquals('S',Access::LEVEL_MODULE_SETUP);
        $this->assertEquals('A',Access::LEVEL_MODULE_ALL);
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Access::canViewPersonal
     */
    public function testCanViewPersonal ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Modules\Access::canViewPersonal');
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Access::isSystemUser
     */
    public function testIsSystemUser ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Modules\Access::isSystemUser');
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Access::can
     */
    public function testCan ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Modules\Access::can');
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Access::canView
     */
    public function testCanView ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Modules\Access::canView');
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Access::canEdit
     */
    public function testCanEdit ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Modules\Access::canEdit');
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Access::canSetup
     */
    public function testCanSetup ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Modules\Access::canSetup');
    }
}
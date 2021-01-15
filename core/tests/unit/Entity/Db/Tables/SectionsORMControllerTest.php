<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Tables\SectionsORMController;

/**
 * Класс ${NAMESPACE}\SectionsORMControllerTest
 * Тесты класса \Ms\Core\Entity\Db\Tables\SectionsORMController
 */
class SectionsORMControllerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;
    /** @var SectionsORMController */
    protected $orm = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
           ->setSettings()
           ->setApplicationParametersCollection()
           ->setConnectionPool()
        ;
        $this->app->getSettings()->mergeLocalSettings();
        $this->app->getConnectionPool()->getConnection()->connect();

        // $this->orm = SectionsORMController::getInstance(new \Ms\Core\Tables\UsersTable());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\SectionsORMController::getTable
     */
    public function testGetTable ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\SectionsORMController::getTable');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\SectionsORMController::addSection
     */
    public function testAddSection ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\SectionsORMController::addSection');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\SectionsORMController::sortSection
     */
    public function testSortSection ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\SectionsORMController::sortSection');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\SectionsORMController::changeParent
     */
    public function testChangeParent ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\SectionsORMController::changeParent');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\SectionsORMController::activateSection
     */
    public function testActivateSection ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\SectionsORMController::activateSection');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\SectionsORMController::deactivateSection
     */
    public function testDeactivateSection ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\SectionsORMController::deactivateSection');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\SectionsORMController::deleteSection
     */
    public function testDeleteSection ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\SectionsORMController::deleteSection');
    }


}
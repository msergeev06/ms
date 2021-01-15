<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Tables\TreeORMController;

/**
 * Класс ${NAMESPACE}\TreeORMControllerTest
 * Тесты класса \Ms\Core\Entity\Db\Tables\TreeORMController
 */
class TreeORMControllerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;
    /** @var TreeORMController */
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

        // $this->orm = TreeORMController::getInstance(new \Ms\Core\Tables\UsersTable());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::changeParent
     */
    public function testChangeParent ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::changeParent');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::clearErrorCollection
     */
    public function testClearErrorCollection ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::clearErrorCollection');
        // $this->orm->clearErrorCollection();
        // $this->assertTrue($this->orm->getErrorCollection()->isEmpty());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getErrorCollection
     */
    public function testGetErrorCollection ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getErrorCollection');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getErrors
     */
    public function testGetErrors ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getErrors');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getTable
     */
    public function testGetTable ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getTable');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::activateNode
     */
    public function testActivateNode ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::activateNode');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::addNode
     */
    public function testAddNode ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::addNode');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::checkTable
     */
    public function testCheckTable ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::checkTable');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::checkUpdateFields
     */
    public function testCheckUpdateFields ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::checkUpdateFields');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::deactivateNode
     */
    public function testDeactivateNode ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::deactivateNode');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::deleteNode
     */
    public function testDeleteNode ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::deleteNode');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getBranch
     */
    public function testGetBranch ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getBranch');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getChildren
     */
    public function testGetChildren ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getChildren');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getNodesByDepthLevel
     */
    public function testGetNodesByDepthLevel ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getChildren');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getParentInfo
     */
    public function testGetParentInfo ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getParentInfo');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getParentLevel
     */
    public function testGetParentLevel ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getParentLevel');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getParentPrimary
     */
    public function testGetParentPrimary ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getParentPrimary');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getParents
     */
    public function testGetParents ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getParents');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::getTreeList
     */
    public function testGetTreeList ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::getTreeList');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeORMController::sortNode
     */
    public function testSortNode ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeORMController::sortNode');
    }
}
<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Tables\ORMController;
use \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract;

/**
 * Класс \ORMControllerTest
 * Тесты класса \Ms\Core\Entity\Db\Tables\ORMController
 */
class ORMControllerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;
    /** @var ORMController */
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

        $this->orm = ORMController::getInstance(new \Ms\Core\Tables\UsersTable());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getInstance
     */
    public function testGetInstance ()
    {
        $this->assertInstanceOf(ORMController::class,$this->orm);
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getMap
     */
    public function testGetMap ()
    {
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Tables\FieldsCollection::class,$this->orm->getMap());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getTable
     */
    public function testGetTable ()
    {
        $this->assertInstanceOf(\Ms\Core\Tables\UsersTable::class,$this->orm->getTable());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::setShowSql
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::isShowSql
     */
    public function testShowSql ()
    {
        $this->orm->setShowSql();
        $this->assertTrue($this->orm->isShowSql());
        $this->orm->setShowSql(false);
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::validateFields
     */
    public function testValidateFields ()
    {
        $mValue = '123';
        try
        {
            $this->orm->validateFields('ID', $mValue, ScalarFieldAbstract::DEFAULT_VALUE_TYPE_UPDATE);
        }
        catch (\Ms\Core\Exceptions\Db\ValidateException $e)
        {
            $this->assertTrue(false,$e->getMessage());
            return;
        }
        $this->assertEquals(123,$mValue);
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getSqlAddIndexes
     */
    public function testGetSqlAddIndexes ()
    {
        $this->assertContains('INDEX `INDEX_LOGIN` (`LOGIN`)',$this->orm->getSqlAddIndexes('LOGIN'));
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getSqlAddTrigger
     */
    public function testGetSqlAddTrigger ()
    {
        $triggerSql = $this->orm->getSqlAddTrigger(
            'SELECT `ID` FROM `ms_core_users` LIMIT 1',
            ORMController::TRIGGER_TIME_AFTER,
            ORMController::TRIGGER_EVENT_INSERT
        );
        $this->assertContains(
            $this->orm->getTable()->getTableName().'_'
            . ORMController::TRIGGER_TIME_AFTER . '_'
            . ORMController::TRIGGER_EVENT_INSERT
            ,
            $triggerSql
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getSqlAddUnique
     */
    public function testGetSqlAddUnique ()
    {
        $uniqueSql = $this->orm->getSqlAddUnique(
            'LOGIN',
            true
        );
        $this->assertEquals('UNIQUE INDEX `unique_index_login` (`LOGIN`)',$uniqueSql);
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getByID
     */
    public function testGetByID ()
    {
        try
        {
            $arRes = $this->orm->getByID(1, ['ID', 'LOGIN']);
        }
        catch (\Exception $e)
        {
            $this->assertTrue(false, $e->getMessage());
            return;
        }
        $this->assertTrue(is_array($arRes));
        $this->assertArrayHasKey('LOGIN',$arRes);
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getByPrimary
     */
    public function testGetByPrimary ()
    {
        try
        {
            $arRes = $this->orm->getByPrimary(1, ['ID', 'LOGIN']);
        }
        catch (\Exception $e)
        {
            $this->assertTrue(false, $e->getMessage());
            return;
        }
        $this->assertTrue(is_array($arRes));
        $this->assertArrayHasKey('LOGIN',$arRes);
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getList
     */
    public function testGetList ()
    {
        try
        {
            $arRes = $this->orm->getList(
                [
                    'select' => ['ID', 'LOGIN'],
                    'filter' => ['ID' => 1]
                ]
            );
        }
        catch (\Exception $e)
        {
            $this->assertTrue(false, $e->getMessage());
            return;
        }
        $this->assertTrue(is_array($arRes));
        $this->assertArrayHasKey(0,$arRes);
        $this->assertArrayHasKey('LOGIN',$arRes[0]);
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getMapArray
     */
    public function testGetMapArray ()
    {
        $arRes = $this->orm->getMapArray();
        $this->assertTrue(is_array($arRes));
        $this->assertArrayHasKey('LOGIN',$arRes);
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getOne
     */
    public function testGetOne ()
    {
        try
        {
            $arRes = $this->orm->getOne(
                [
                    'select' => ['ID', 'LOGIN'],
                    'filter' => ['ID' => 1]
                ]
            );
        }
        catch (\Exception $e)
        {
            $this->assertTrue(false,$e->getMessage());
            return;
        }
        $this->assertTrue(is_array($arRes));
        $this->assertArrayHasKey('LOGIN',$arRes);
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getPrimaryField
     */
    public function testGetPrimaryField ()
    {
        $field = $this->orm->getPrimaryField();
        $this->assertInstanceOf(\Ms\Core\Interfaces\Db\IField::class,$field);
        $this->assertEquals('ID',$field->getColumnName());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getPrimaryFieldName
     */
    public function testGetPrimaryFieldName ()
    {
        $this->assertEquals('ID',$this->orm->getPrimaryFieldName());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getSqlDeleteTrigger
     */
    public function testGetSqlDeleteTrigger ()
    {
        $sql = $this->orm->getSqlDeleteTrigger(
            ORMController::TRIGGER_TIME_AFTER,
            ORMController::TRIGGER_EVENT_INSERT
        );
        $this->assertEquals(
            'DROP TRIGGER IF EXISTS `'
            . $this->orm->getTableName() . '_'
            . ORMController::TRIGGER_TIME_AFTER . '_'
            . ORMController::TRIGGER_EVENT_INSERT  . '`;'
            ,
            $sql
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::issetTable
     */
    public function testIssetTable ()
    {
        try
        {
            $this->assertTrue($this->orm->issetTable((new \Ms\Core\Tables\UsersTable())->getTableName()));
        }
        catch (\Ms\Core\Exceptions\Db\SqlQueryException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::getTableName
     */
    public function testGetTableName ()
    {
        $this->assertEquals('ms_core_users',$this->orm->getTableName());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::addIndexes
     */
    public function testAddIndexes ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::addIndexes');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::addTriggerSql
     */
    public function testAddTriggerSql ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::addTriggerSql');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::addUnique
     */
    public function testAddUnique ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::addUnique');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::clearTable
     */
    public function testClearTable ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::clearTable');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::count
     */
    public function testCount ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::count');
        // $orm = $this->orm;
        // $this->assertEquals(1, $orm->count());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::createTable
     */
    public function testCreateTable ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::createTable');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::delete
     */
    public function testDelete ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::delete');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::deleteWhere
     */
    public function testDeleteWhere ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::deleteWhere');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::dropTable
     */
    public function testDropTable ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::dropTable');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::insert
     */
    public function testInsert ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::insert');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::insertDefaultRows
     */
    public function testInsertDefaultRows ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::insertDefaultRows');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::rename
     */
    public function testRename ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::rename');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::update
     */
    public function testUpdate ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::update');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::updateByPrimary
     */
    public function testUpdateByPrimary ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::updateByPrimary');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\ORMController::updateByWhere
     */
    public function testUpdateByWhere ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\ORMController::updateByWhere');
    }
}
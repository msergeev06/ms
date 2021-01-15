<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Tables\TableAbstract;

class TestAbstractTable extends TableAbstract
{
    public function getMap (): \Ms\Core\Entity\Db\Tables\FieldsCollection
    {
        // TODO: Implement getMap() method.
    }
}

/**
 * Класс ${NAMESPACE}\TableAbstractTest
 * Тесты класса \Ms\Core\Entity\Db\Tables\TableAbstract
 */
class TableAbstractTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {

    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::getAdditionalCreateSql
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::getAdditionalDeleteSql
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::getDefaultRowsArray
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::getInnerCreateSql
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::getTableTitle
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::onAfterCreateTable
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::onAfterDelete
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::onAfterDropTable
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::onAfterInsert
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::onAfterUpdate
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::onBeforeCreateTable
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::onBeforeDelete
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::onBeforeDropTable
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::onBeforeInsert
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::onBeforeUpdate
     */
    public function testExtendsMethods ()
    {
        $table = new TestAbstractTable();
        $this->assertTrue(empty($table->getAdditionalCreateSql()));
        $this->assertTrue(empty($table->getAdditionalDeleteSql()));
        $this->assertTrue(empty($table->getDefaultRowsArray()));
        $this->assertTrue(empty($table->getInnerCreateSql()));
        $this->assertTrue(empty($table->getTableTitle()));
        $this->assertTrue($table->onBeforeCreateTable());
        $this->assertTrue($table->onBeforeDelete(1,''));
        $this->assertTrue($table->onBeforeDropTable());
        $this->assertTrue($table->onBeforeInsert($arInsert = []));
        $this->assertTrue($table->onBeforeUpdate(1, $arUpdate = [],$sql = ''));
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::clearAdditionalName
     */
    public function testClearAdditionalName ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TableAbstract::clearAdditionalName');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::getAdditionalName
     */
    public function testGetAdditionalName ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TableAbstract::getAdditionalName');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::getClassName
     */
    public function testGetClassName ()
    {
        $table = new TestAbstractTable();
        $this->assertEquals(TestAbstractTable::class,$table->getClassName());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::getTableName
     */
    public function testGetTableName ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TableAbstract::getTableName');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TableAbstract::setAdditionalName
     */
    public function testSetAdditionalName ()
    {
        $table = new TestAbstractTable();
        $table->setAdditionalName('test');
        $this->assertEquals('test',$table->getAdditionalName());
    }
}
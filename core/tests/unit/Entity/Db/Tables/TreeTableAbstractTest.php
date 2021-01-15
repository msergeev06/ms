<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Tables\TreeTableAbstract;

class TestTreeAbstractTable extends TreeTableAbstract
{
    public function getMap (): \Ms\Core\Entity\Db\Tables\FieldsCollection
    {
        return parent::getMap();
    }
}

/**
 * Класс \TreeTableAbstractTest
 * Тесты класса \Ms\Core\Entity\Db\Tables\TreeTableAbstract
 */
class TreeTableAbstractTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {

    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeTableAbstract::getTableTitle
     */
    public function testGetTableTitle ()
    {
        $table = new TestTreeAbstractTable();
        $this->assertEquals('Дерево',$table->getTableTitle());
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeTableAbstract::getMap
     */
    public function testGetMap ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeTableAbstract::getMap');
        // $table = new TestTreeAbstractTable();
        // $this->assertInstanceOf(\Ms\Core\Entity\Db\Tables\FieldsCollection::class,$table->getMap());
        // $arFields = $table->getMap()->toArray();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeTableAbstract::onAfterCreateTable
     */
    public function testOnAfterCreateTable ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeTableAbstract::onAfterCreateTable');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeTableAbstract::onBeforeUpdate
     */
    public function testOnBeforeUpdate ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\TreeTableAbstract::onBeforeUpdate');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\TreeTableAbstract::getParentFieldName
     */
    public function testGetParentFieldName ()
    {
        $table = new TestTreeAbstractTable();
        $this->assertEquals('PARENT_ID',$table->getParentFieldName());
    }
}
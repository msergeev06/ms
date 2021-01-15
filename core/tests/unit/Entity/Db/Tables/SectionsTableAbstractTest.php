<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Tables\SectionsTableAbstract;


class TestSectionsAbstractTable extends SectionsTableAbstract
{
    public function getMap (): \Ms\Core\Entity\Db\Tables\FieldsCollection
    {
        return parent::getMap();
    }
}

/**
 * Класс \SectionsTableAbstractTest
 * Тесты класса \Ms\Core\Entity\Db\Tables\SectionsTableAbstract
 */
class SectionsTableAbstractTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {

    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\SectionsTableAbstract::getDefaultRowsArray
     */
    public function testGetDefaultRowsArray ()
    {
        $table = new TestSectionsAbstractTable();
        $this->assertTrue(is_array($table->getDefaultRowsArray()));
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\SectionsTableAbstract::getMap
     */
    public function testGetMap ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Tables\SectionsTableAbstract::getMap');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\SectionsTableAbstract::getTableTitle
     */
    public function testGetTableTitle ()
    {
        $table = new TestSectionsAbstractTable();
        $this->assertEquals('Разделы',$table->getTableTitle());
    }
}
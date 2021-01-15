<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Params\Order;

/**
 * Класс \OrderTest
 * Тесты класса \Ms\Core\Entity\Db\Params\Order
 */
class OrderTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {

    }

    /**
     * @covers \Ms\Core\Entity\Db\Params\Order::getTable
     * @covers \Ms\Core\Entity\Db\Params\Order::getField
     * @covers \Ms\Core\Entity\Db\Params\Order::getFieldName
     * @covers \Ms\Core\Entity\Db\Params\Order::getTableAlias
     * @covers \Ms\Core\Entity\Db\Params\Order::getDirection
     */
    public function testClassMethods ()
    {
        $ob = new Order(
            new \Ms\Core\Tables\UsersTable(),
            'a',
            'ID',
            new \Ms\Core\Entity\Db\Fields\IntegerField('ID'),
            \Ms\Core\Entity\Db\Params\OrderCollection::DIRECTION_DESC
        );
        $this->assertInstanceOf(\Ms\Core\Tables\UsersTable::class, $ob->getTable());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\IntegerField::class, $ob->getField());
        $this->assertEquals('ID',$ob->getFieldName());
        $this->assertEquals('a',$ob->getTableAlias());
        $this->assertEquals(\Ms\Core\Entity\Db\Params\OrderCollection::DIRECTION_DESC,$ob->getDirection());
    }
}
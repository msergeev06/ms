<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Params\Filter;

/**
 * Класс \FilterTest
 * Тесты класса \Ms\Core\Entity\Db\Params\Filter
 */
class FilterTest extends \PHPUnit\Framework\TestCase
{
    /** @var Filter */
    protected $obj = null;

    protected function setUp ()
    {
        $this->obj = new Filter(
            new \Ms\Core\Tables\UsersTable(),
            'a',
            'ID',
            new \Ms\Core\Entity\Db\Fields\IntegerField('ID'),
            1
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\Params\Filter::getTable
     * @covers \Ms\Core\Entity\Db\Params\Filter::getTableAlias
     * @covers \Ms\Core\Entity\Db\Params\Filter::getFieldName
     * @covers \Ms\Core\Entity\Db\Params\Filter::getField
     * @covers \Ms\Core\Entity\Db\Params\Filter::getValue
     * @covers \Ms\Core\Entity\Db\Params\Filter::getExpression
     */
    public function testClassMethods ()
    {
        $this->assertInstanceOf(\Ms\Core\Tables\UsersTable::class,$this->obj->getTable());
        $this->assertEquals('a',$this->obj->getTableAlias());
        $this->assertEquals('ID',$this->obj->getFieldName());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\IntegerField::class,$this->obj->getField());
        $this->assertEquals(1, $this->obj->getValue());
        $this->assertEquals('',$this->obj->getExpression());
    }
}
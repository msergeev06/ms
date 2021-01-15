<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Params\GetListParams;

/**
 * Класс \GetListParamsTest
 * Тесты класса \Ms\Core\Entity\Db\Params\GetListParams
 */
class GetListParamsTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {
        \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
    }

    /**
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::setLimit
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::getLimit
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::setOffset
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::getOffset
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::getORMController
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::getTable
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::getSelectFieldsCollection
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::getTableAliasCollection
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::getJoinFieldsCollection
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::getFilterCollection
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::getOrderCollection
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::setFilterFromArray
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::setOrderFromArray
     * @covers \Ms\Core\Entity\Db\Params\GetListParams::parseGetListSelect
     */
    public function testClassMethods ()
    {
        $ob = new GetListParams(
            \Ms\Core\Entity\Db\Tables\ORMController::getInstance(new \Ms\Core\Tables\UsersTable())
        );
        $ob->setLimit(10);
        $this->assertEquals(10,$ob->getLimit());
        $ob->setOffset(5);
        $this->assertEquals(5, $ob->getOffset());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Tables\ORMController::class,$ob->getORMController());
        $this->assertInstanceOf(\Ms\Core\Tables\UsersTable::class,$ob->getTable());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\SelectFieldsCollection::class, $ob->getSelectFieldsCollection());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\TableAliasCollection::class, $ob->getTableAliasCollection());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\JoinFieldsCollection::class, $ob->getJoinFieldsCollection());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\FilterCollection::class, $ob->getFilterCollection());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\OrderCollection::class, $ob->getOrderCollection());
        $ob->setFilterFromArray([]);
        $this->assertTrue($ob->getFilterCollection()->isEmpty());
        $ob->setOrderFromArray([]);
        $this->assertTrue($ob->getOrderCollection()->isEmpty());
        $ob->parseGetListSelect(
            [
                'ID' => 'USER_ID'
            ]
        );
        $this->assertFalse($ob->getSelectFieldsCollection()->isEmpty());
    }
}
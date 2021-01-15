<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Params\TableAliasCollection;

/**
 * Класс \TableAliasCollectionTest
 * Тесты класса \Ms\Core\Entity\Db\Params\TableAliasCollection
 */
class TableAliasCollectionTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {

    }

    /**
     * @covers \Ms\Core\Entity\Db\Params\TableAliasCollection::addAlias
     * @covers \Ms\Core\Entity\Db\Params\TableAliasCollection::isAliasExists
     * @covers \Ms\Core\Entity\Db\Params\TableAliasCollection::isAliasExistsByTableName
     * @covers \Ms\Core\Entity\Db\Params\TableAliasCollection::getAlias
     * @covers \Ms\Core\Entity\Db\Params\TableAliasCollection::getAliasByTableName
     */
    public function testClassMethods ()
    {
        $ob = new TableAliasCollection();
        $ob->addAlias(new \Ms\Core\Tables\UsersTable());
        $this->assertTrue($ob->isAliasExists(new \Ms\Core\Tables\UsersTable()));
        $this->assertTrue($ob->isAliasExistsByTableName((new \Ms\Core\Tables\UsersTable())->getTableName()));
        $this->assertEquals('mcu',$ob->getAlias(new \Ms\Core\Tables\UsersTable()));
        $this->assertEquals('mcu', $ob->getAliasByTableName((new \Ms\Core\Tables\UsersTable())->getTableName()));
    }
}
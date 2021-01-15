<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Params\JoinField;

/**
 * Класс \JoinFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Params\JoinField
 */
class JoinFieldTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {
        \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
    }

    /**
     * @covers \Ms\Core\Entity\Db\Params\JoinField::getBaseFieldName
     * @covers \Ms\Core\Entity\Db\Params\JoinField::getBaseTable
     * @covers \Ms\Core\Entity\Db\Params\JoinField::getBaseTableAlias
     * @covers \Ms\Core\Entity\Db\Params\JoinField::getRefFieldName
     * @covers \Ms\Core\Entity\Db\Params\JoinField::getRefTable
     * @covers \Ms\Core\Entity\Db\Params\JoinField::getRefTableAlias
     */
    public function testClassMethods ()
    {
        $ob = new JoinField(
            'GROUP_ID',
            new \Ms\Core\Tables\UserToGroupTable(),
            'a',
            'ID', new \Ms\Core\Tables\UserGroupsTable(),
            'b'
        );
        $this->assertEquals('GROUP_ID',$ob->getBaseFieldName());
        $this->assertInstanceOf(\Ms\Core\Tables\UserToGroupTable::class,$ob->getBaseTable());
        $this->assertEquals('a',$ob->getBaseTableAlias());
        $this->assertEquals('ID',$ob->getRefFieldName());
        $this->assertInstanceOf(\Ms\Core\Tables\UserGroupsTable::class,$ob->getRefTable());
        $this->assertEquals('b',$ob->getRefTableAlias());
    }
}
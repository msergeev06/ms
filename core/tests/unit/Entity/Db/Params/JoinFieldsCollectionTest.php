<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Params\JoinFieldsCollection;

/**
 * Класс \JoinFieldsCollectionTest
 * Тесты класса \Ms\Core\Entity\Db\Params\JoinFieldsCollection
 */
class JoinFieldsCollectionTest extends \PHPUnit\Framework\TestCase
{
    protected $getListParams = null;

    protected function setUp ()
    {
        \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;

        $this->getListParams = new \Ms\Core\Entity\Db\Params\GetListParams(
            \Ms\Core\Entity\Db\Tables\ORMController::getInstance(new \Ms\Core\Tables\UserToGroupTable())
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\Params\JoinFieldsCollection::getParams
     * @covers \Ms\Core\Entity\Db\Params\JoinFieldsCollection::addJoin
     * @covers \Ms\Core\Entity\Db\Params\JoinFieldsCollection::getJoinByTableName
     * @covers \Ms\Core\Entity\Db\Params\JoinFieldsCollection::getJoin
     * @covers \Ms\Core\Entity\Db\Params\JoinFieldsCollection::isExists
     */
    public function testClassMethods ()
    {
        $ob = new JoinFieldsCollection($this->getListParams);
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\GetListParams::class,$ob->getParams());
        $ob->addJoin(
            'ID',
            new \Ms\Core\Tables\UserGroupsTable(),
            'GROUP_ID',
            new \Ms\Core\Tables\UserToGroupTable()
        );
        $this->assertInstanceOf(
            \Ms\Core\Entity\Db\Params\JoinField::class,
            $ob->getJoinByTableName((new \Ms\Core\Tables\UserGroupsTable())->getTableName())
        );
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\JoinField::class, $ob->getJoin(new \Ms\Core\Tables\UserGroupsTable()));
        $this->assertTrue($ob->isExists(new \Ms\Core\Tables\UserGroupsTable()));
    }
}
<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Query\QueryBase;

/**
 * Класс \QueryBaseTest
 * Тесты класса \Ms\Core\Entity\Db\Query\QueryBase
 */
class QueryBaseTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {
        $app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
            ->setConnectionPool()
            ->setApplicationParametersCollection()
        ;
        $app->getSettings()->mergeLocalSettings();
        $app->getConnectionPool()->getConnection()->connect();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Query\QueryBase::setTable
     * @covers \Ms\Core\Entity\Db\Query\QueryBase::getTable
     * @covers \Ms\Core\Entity\Db\Query\QueryBase::getClassName
     * @covers \Ms\Core\Entity\Db\Query\QueryBase::getFieldsCollection
     * @covers \Ms\Core\Entity\Db\Query\QueryBase::setSql
     * @covers \Ms\Core\Entity\Db\Query\QueryBase::getSql
     * @covers \Ms\Core\Entity\Db\Query\QueryBase::isReturnSql
     * @covers \Ms\Core\Entity\Db\Query\QueryBase::exec
     */
    public function testClassMethods ()
    {
        $ob = new QueryBase();
        $ob->setTable(new \Ms\Core\Tables\UsersTable());
        $this->assertInstanceOf(\Ms\Core\Tables\UsersTable::class,$ob->getTable());
        $this->assertEquals(QueryBase::class,$ob->getClassName());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Tables\FieldsCollection::class,$ob->getFieldsCollection());
        $ob->setSql('SELECT * FROM `ms_core_users`');
        $this->assertEquals('SELECT * FROM `ms_core_users`',$ob->getSql());
        $this->assertFalse($ob->isReturnSql());
        $res = $ob->exec();
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Result\DBResult::class,$res);

        \Ms\Core\Entity\System\Application::getInstance()->setAppParams('no_query_exec',true);
        $res = $ob->exec();
        $this->assertEquals('SELECT * FROM `ms_core_users`',$res);

        \Ms\Core\Entity\System\Application::getInstance()->setAppParams('no_query_exec',false);
        $this->assertFalse($ob->isReturnSql());
        $res = $ob->exec(true);
        $this->assertEquals('SELECT * FROM `ms_core_users`',$res);
    }
}
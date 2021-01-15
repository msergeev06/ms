<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Query\SqlQueryBuilder;

/**
 * Класс \SqlQueryBuilderTest
 * Тесты класса \Ms\Core\Entity\Db\Query\SqlQueryBuilder
 */
class SqlQueryBuilderTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Ms\Core\Entity\Db\Params\GetListParams */
    protected $getListParams = null;

    protected function setUp ()
    {
        $app = (\Ms\Core\Entity\System\Application::getInstance())
            ->setSettings()
            ->setApplicationParametersCollection()
            ->setConnectionPool()
        ;
        $app->getSettings()->mergeLocalSettings();
        $app->getConnectionPool()->getConnection()->connect();

        $this->getListParams = new \Ms\Core\Entity\Db\Params\GetListParams(
            \Ms\Core\Entity\Db\Tables\ORMController::getInstance(
                new \Ms\Core\Tables\UsersTable()
            )
        );
        $this->getListParams->parseGetListSelect(
            [
                'ID' => 'USER_ID',
                'LOGIN',
                'EMAIL'
            ]
        );
        $this->getListParams->setOrderFromArray(
            [
                'ID' => 'DESC'
            ]
        );
        $this->getListParams->setFilterFromArray(
            [
                '>ID' => 0
            ]
        );
        $this->getListParams->setLimit(1);
    }

    /**
     * @covers \Ms\Core\Entity\Db\Query\SqlQueryBuilder::createSelect
     * @covers \Ms\Core\Entity\Db\Query\SqlQueryBuilder::createFrom
     * @covers \Ms\Core\Entity\Db\Query\SqlQueryBuilder::createSqlJoin
     * @covers \Ms\Core\Entity\Db\Query\SqlQueryBuilder::createSqlWhere
     * @covers \Ms\Core\Entity\Db\Query\SqlQueryBuilder::createSqlOrder
     * @covers \Ms\Core\Entity\Db\Query\SqlQueryBuilder::createSqlLimit
     */
    public function testClassMethods ()
    {
        $builder = SqlQueryBuilder::getInstance();
        $selectSql = $builder->createSelect($this->getListParams);
        $this->assertContains('SELECT',$selectSql);
        $this->assertContains('`mcu`.`ID` as `USER_ID`',$selectSql);
        $this->assertContains('`mcu`.`LOGIN`',$selectSql);
        $this->assertContains('`mcu`.`EMAIL`',$selectSql);
        $fromSql = $builder->createFrom($this->getListParams);
        $this->assertContains('FROM',$fromSql);
        $this->assertContains($this->getListParams->getTable()->getTableName(),$fromSql);
        $joinSql = $builder->createSqlJoin($this->getListParams);
        $this->assertEquals('',$joinSql);
        $whereSql = $builder->createSqlWhere($this->getListParams);
        $this->assertContains('WHERE',$whereSql);
        $this->assertContains('`mcu`.`ID` > 0',$whereSql);
        $orderSql = $builder->createSqlOrder($this->getListParams);
        $this->assertContains('ORDER BY',$orderSql);
        $this->assertContains('`mcu`.`ID` DESC',$orderSql);
        $limitSql = $builder->createSqlLimit($this->getListParams);
        $this->assertContains('LIMIT 0, 1',$limitSql);
    }
}
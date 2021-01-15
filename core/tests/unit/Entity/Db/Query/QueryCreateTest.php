<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Query\QueryCreate;

/**
 * Класс \QueryCreateTest
 * Тесты класса \Ms\Core\Entity\Db\Query\QueryCreate
 */
class QueryCreateTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {
        $app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
            ->setApplicationParametersCollection()
            ->setConnectionPool()
        ;
        $app->getSettings()->mergeLocalSettings();
        $app->getConnectionPool()->getConnection()->connect();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Query\QueryCreate::__construct
     */
    public function testClassMethods ()
    {
        $ob = new QueryCreate(new \Ms\Core\Tables\UsersTable());
        $this->assertInstanceOf(\Ms\Core\Tables\UsersTable::class,$ob->getTable());
        $this->assertContains('CREATE TABLE IF NOT EXISTS '.$ob->getTable()->getTableName(),$ob->getSql());
    }
}
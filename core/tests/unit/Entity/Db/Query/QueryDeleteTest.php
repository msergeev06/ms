<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Query\QueryDelete;

/**
 * Класс \QueryDeleteTest
 * Тесты класса \Ms\Core\Entity\Db\Query\QueryDelete
 */
class QueryDeleteTest extends \PHPUnit\Framework\TestCase
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
     * @covers \Ms\Core\Entity\Db\Query\QueryDelete::setSqlWhere
     * @covers \Ms\Core\Entity\Db\Query\QueryDelete::getSqlWhere
     * @covers \Ms\Core\Entity\Db\Query\QueryDelete::setDeletePrimary
     * @covers \Ms\Core\Entity\Db\Query\QueryDelete::getDeletePrimary
     */
    public function testClassMethods ()
    {
        try
        {
            $ob = new QueryDelete(2, new \Ms\Core\Tables\UsersTable());
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentNullException $e)
        {
            $this->assertTrue(false, $e->getMessage());
            return;
        }
        $ob->setSqlWhere('`NAME` = "Goblin"');
        $this->assertEquals('`NAME` = "Goblin"',$ob->getSqlWhere());
        $ob->setDeletePrimary(3);
        $this->assertEquals(3,$ob->getDeletePrimary());
        $this->assertContains('DELETE FROM',$ob->getSql());
        $this->assertContains($ob->getTable()->getTableName(),$ob->getSql());
    }
}
<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Query\QueryUpdate;

/**
 * Класс \QueryUpdateTest
 * Тесты класса \Ms\Core\Entity\Db\Query\QueryUpdate
 */
class QueryUpdateTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {
        $app = (\Ms\Core\Entity\System\Application::getInstance())
            ->setSettings()
            ->setApplicationParametersCollection()
            ->setConnectionPool()
        ;
        $app->getSettings()->mergeLocalSettings();
        $app->getConnectionPool()->getConnection()->connect();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Query\QueryUpdate::__construct
     * @covers \Ms\Core\Entity\Db\Query\QueryUpdate::getSqlWhere
     */
    public function testClassMethods ()
    {
        $arUpdate = [
            'NAME' => 'Admin'
        ];
        try
        {
            $ob = new QueryUpdate(1, $arUpdate, new \Ms\Core\Tables\UsersTable(), 'test where');
        }
        catch (\Exception $e)
        {
            $this->assertTrue(false, $e->getMessage());
            return;
        }
        $this->assertContains('UPDATE',$ob->getSql());
        $this->assertContains($ob->getTable()->getTableName(),$ob->getSql());
        $this->assertContains($ob->getSqlWhere(),$ob->getSql());
    }
}
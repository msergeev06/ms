<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Query\QueryInsert;

/**
 * Класс \QueryInsertTest
 * Тесты класса \Ms\Core\Entity\Db\Query\QueryInsert
 */
class QueryInsertTest extends \PHPUnit\Framework\TestCase
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
     * @covers \Ms\Core\Entity\Db\Query\QueryInsert::__construct
     */
    public function testClassMethods ()
    {
        try
        {
            $arInsert = [
                'LOGIN' => 'test',
                'PASSWORD' => 'test',
                'EMAIL' => 'test'
            ];
            $ob = new QueryInsert($arInsert, new \Ms\Core\Tables\UsersTable());
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentNullException $e)
        {
            $this->assertTrue(false,$e->getMessage());
            return;
        }
        $this->assertContains('INSERT INTO `'.$ob->getTable()->getTableName().'` (',$ob->getSql());
    }
}
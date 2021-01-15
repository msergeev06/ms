<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Query\QueryDrop;

/**
 * Класс \QueryDropTest
 * Тесты класса \Ms\Core\Entity\Db\Query\QueryDrop
 */
class QueryDropTest extends \PHPUnit\Framework\TestCase
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
     * @covers \Ms\Core\Entity\Db\Query\QueryDrop::setBIgnoreForeignKeys
     * @covers \Ms\Core\Entity\Db\Query\QueryDrop::isIgnoreForeignKeys
     */
    public function testClassMethods ()
    {
        $ob = new QueryDrop(new \Ms\Core\Tables\UsersTable());
        $this->assertFalse($ob->isIgnoreForeignKeys());
    }
}
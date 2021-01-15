<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Query\QuerySelect;

/**
 * Класс \QuerySelectTest
 * Тесты класса \Ms\Core\Entity\Db\Query\QuerySelect
 */
class QuerySelectTest extends \PHPUnit\Framework\TestCase
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
    }

    /**
     * @covers \Ms\Core\Entity\Db\Query\QuerySelect::getGetListParams
     */
    public function testClassMethods ()
    {
        $ob = new QuerySelect($this->getListParams->getTable(),$this->getListParams);
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\GetListParams::class,$ob->getGetListParams());
        $this->assertContains('SELECT',$ob->getSql());
        $this->assertContains($ob->getTable()->getTableName(),$ob->getSql());
    }
}
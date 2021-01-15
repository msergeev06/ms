<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Result\DBResult;

/**
 * Класс \DBResultTest
 * Тесты класса \Ms\Core\Entity\Db\Result\DBResult
 */
class DBResultTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
            ->setApplicationParametersCollection()
            ->setConnectionPool()
        ;
        $this->app->getSettings()->mergeLocalSettings();
        $this->app->getConnectionPool()->getConnection()->connect();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Result\DBResult::fetch
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getAffectedRows
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getDriver
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getInsertId
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getLastRes
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getLastResult
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getNumFields
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getNumRows
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getObQuery
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getResult
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getResultErrorNumber
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getResultErrorText
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getSql
     * @covers \Ms\Core\Entity\Db\Result\DBResult::isSuccess
     * @covers \Ms\Core\Entity\Db\Result\DBResult::getTable
     */
    public function testClassMethods ()
    {
        $sql = 'SELECT * FROM `ms_core_users` ORDER BY `ID` ASC LIMIT 1';
        $query = new \Ms\Core\Entity\Db\Query\QueryBase($sql);
        $query->setTable(new \Ms\Core\Tables\UsersTable());
        try
        {
            /** @var DBResult $result */
            $result = $query->exec();
            if (is_string($result))
            {
                $this->assertTrue(false,$result);
            }
        }
        catch (\Ms\Core\Exceptions\Db\SqlQueryException $e)
        {
            $this->assertTrue(false,$e->getMessage());
            return;
        }
        $arRes = $result->fetch();
        $this->assertTrue(is_array($arRes));
        $this->assertArrayHasKey('ID',$arRes);
        $this->assertEquals(1, $result->getAffectedRows());
        $this->assertInstanceOf(\Ms\Core\Interfaces\ISqlDriver::class,$result->getDriver());
        $this->assertEquals(0,$result->getInsertId());
        $lastRes = $result->getLastRes();
        $this->assertTrue($lastRes['ACTIVE'] == 'Y');
        $lastResult = $result->getLastResult();
        $this->assertTrue($lastResult['ACTIVE']);
        $this->assertEquals(12,$result->getNumFields());
        $this->assertEquals(1,$result->getNumRows());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Query\QueryBase::class,$result->getObQuery());
        $this->assertTrue(is_object($result->getResult()));
        $this->assertEquals(0,$result->getResultErrorNumber());
        $this->assertEquals('',$result->getResultErrorText());
        $this->assertEquals('SELECT * FROM `ms_core_users` ORDER BY `ID` ASC LIMIT 1',$result->getSql());
        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(\Ms\Core\Tables\UsersTable::class,$result->getTable());
    }
}
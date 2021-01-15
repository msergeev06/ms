<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

/**
 * Класс \MySqliDriverTest
 * Тесты класса \Ms\Core\Entity\Db\Drivers\MySqliDriver
 */
class MySqliDriverTest extends \PHPUnit\Framework\TestCase
{
    protected $app = null;
    /** @var null|\Ms\Core\Entity\Db\Drivers\MySqliDriver */
    protected $driver = null;
    /** @var null|\Ms\Core\Entity\Db\Connection */
    protected $connection = null;
    protected $arConnect = [
        'user' => null,
        'pass' => null,
        'base' => null,
        'host' => null
    ];

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance();
        $this->app
            ->setSettings()
            ->setConnectionPool()
        ;
        $this->connection = $this->app->getConnectionPool()->getConnection();
        $settings = $this->app
            ->getSettings()
            ->mergeLocalSettings()
        ;
        $this->arConnect['user'] = $settings->getDbUser();
        $this->arConnect['pass'] = $settings->getDbPass();
        $this->arConnect['base'] = $settings->getDbName();
        $this->arConnect['host'] = $settings->getDbHost();
        $this->connection->connect(
            $this->arConnect['host'],
            $this->arConnect['base'],
            $this->arConnect['user'],
            $this->arConnect['pass'],
            \Ms\Core\Entity\Db\Drivers\MySqliDriver::class
        );
        $this->driver = $this->connection->getDriver();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::getConnection
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::getError
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::getErrorNo
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::getResult
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::getNumRows
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::getNumFields
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::getInsertId
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::getAffectedRows
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::getSql
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::isSuccess
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::startTransaction
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::rollbackTransaction
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::commitTransaction
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::close
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::connect
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::reconnect
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::setCharset
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::query
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::fetchArray
     * @covers \Ms\Core\Entity\Db\Drivers\MySqliDriver::ping
     */
    public function testGetConnection ()
    {
        $this->assertTrue(!is_null($this->driver->getConnection()));
        $this->assertTrue(is_null($this->driver->getResult()));
        $this->assertEquals(0,$this->driver->getInsertId());
        $this->assertTrue($this->driver->isSuccess());
        $this->assertTrue($this->driver->startTransaction());
        $this->assertTrue($this->driver->rollbackTransaction());
        $this->driver->startTransaction();
        $this->assertTrue($this->driver->commitTransaction());
        $this->assertTrue($this->driver->close());
        try
        {
            $this->assertTrue(
                !is_null($this->driver->connect(
                    $this->arConnect['host'],
                    $this->arConnect['base'],
                    $this->arConnect['user'],
                    $this->arConnect['pass']
                ))
            );
        }
        catch (\Ms\Core\Exceptions\Db\ConnectionException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
        try
        {
            $this->assertTrue($this->driver->reconnect());
        }
        catch (\Ms\Core\Exceptions\Db\ConnectionException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
        try
        {
            $this->assertEquals('string', $this->driver->getRealEscapeString('string'));
        }
        catch (\Ms\Core\Exceptions\Db\DbException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
        $this->assertTrue($this->driver->setCharset('utf8'));
        try
        {
            $this->driver->query('SELECT `ID`, `EMAIL` FROM `ms_core_users` WHERE `ID` = 1;');
        }
        catch (\Ms\Core\Exceptions\Db\SqlQueryException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
        $this->assertEquals('',$this->driver->getError());
        $this->assertEquals(0,$this->driver->getErrorNo());
        $this->assertEquals(1,$this->driver->getNumRows());
        $this->assertEquals(2,$this->driver->getNumFields());
        $this->assertEquals(1,$this->driver->getAffectedRows());
        $this->assertEquals('SELECT `ID`, `EMAIL` FROM `ms_core_users` WHERE `ID` = 1;',$this->driver->getSql());
        $this->assertTrue(is_array($this->driver->fetchArray()));
        $this->assertTrue($this->driver->ping());
        try
        {
            $this->assertEquals('string', $this->driver->getRealEscapeString('string'));
        }
        catch (\Ms\Core\Exceptions\Db\DbException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
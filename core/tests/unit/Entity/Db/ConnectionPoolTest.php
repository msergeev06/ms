<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

/**
 * Класс \ConnectionPoolTest
 * Тесты класса \Ms\Core\Entity\Db\ConnectionPool
 */
class ConnectionPoolTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;
    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
            ->setConnectionPool()
            ->setApplicationParametersCollection()
        ;
        $this->app->getSettings()->mergeLocalSettings();
        $this->app->getConnectionPool()->getConnection()->connect();

    }

    /**
     * @covers \Ms\Core\Entity\Db\ConnectionPool::getConnection
     */
    public function testGetConnection ()
    {
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Connection::class,$this->app->getConnectionPool()->getConnection());
    }
}
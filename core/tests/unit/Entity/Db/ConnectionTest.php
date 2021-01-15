<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Db\Connection;

/**
 * Класс \ConnectionTest
 * Тесты класса \Ms\Core\Entity\Db\Connection
 */
class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var Connection */
    protected $ob = null;

    protected function setUp ()
    {
        // $this->ob = new Connection();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::close
     */
    public function testClose ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::close');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::commitTransaction
     */
    public function testCommitTransaction ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::commitTransaction');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::connect
     */
    public function testConnect ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::connect');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::fetchArray
     */
    public function testFetchArray ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::fetchArray');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getAffectedRows
     */
    public function testGetAffectedRows ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getAffectedRows');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getConnection
     */
    public function testGetConnection ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getConnection');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getConnectionName
     */
    public function testGetConnectionName ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getConnectionName');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getDriver
     */
    public function testGetDriver ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getDriver');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getDumpCommand
     */
    public function testGetDumpCommand ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getDumpCommand');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getError
     */
    public function testGetError ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getError');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getErrorNo
     */
    public function testGetErrorNo ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getErrorNo');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getInsertId
     */
    public function testGetInsertId ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getInsertId');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getNumFields
     */
    public function testGetNumFields ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getNumFields');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getNumRows
     */
    public function testGetNumRows ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getNumRows');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getQueryStatistics
     */
    public function testGetQueryStatistics ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getQueryStatistics');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getRealEscapeString
     */
    public function testGetRealEscapeString ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getQueryStatistics');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::getResult
     */
    public function testGetResult ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::getResult');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::isStatisticsUsage
     */
    public function testIsStatisticsUsage ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::isStatisticsUsage');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::isSuccess
     */
    public function testIsSuccess ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::isSuccess');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::ping
     */
    public function testPing ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::ping');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::query
     */
    public function testQuery ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::query');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::querySQL
     */
    public function testQuerySQL ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::querySQL');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::reconnect
     */
    public function testReconnect ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::reconnect');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::restoreDB
     */
    public function testRestoreDB ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::restoreDB');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::rollbackTransaction
     */
    public function testRollbackTransaction ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::rollbackTransaction');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::setCharset
     */
    public function testSetCharset ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::setCharset');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::setConnectionName
     */
    public function testSetConnectionName ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::setConnectionName');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::setDriver
     */
    public function testSetDriver ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::setDriver');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Connection::startTransaction
     */
    public function testStartTransaction ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\Db\Connection::startTransaction');
    }
}
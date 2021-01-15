<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Db\QueryInfo;

/**
 * Класс \QueryInfoTest
 * Тесты класса \Ms\Core\Entity\Db\QueryInfo
 */
class QueryInfoTest extends \PHPUnit\Framework\TestCase
{
    /** @var QueryInfo */
    protected $ob = null;

    protected function setUp ()
    {
        $this->ob = new QueryInfo('');
    }

    /**
     * @covers \Ms\Core\Entity\Db\QueryInfo::setQuerySql
     * @covers \Ms\Core\Entity\Db\QueryInfo::getQuerySql
     */
    public function testSetQuery ()
    {
        $this->ob->setQuerySql('SELECT * FROM `ms_core_users` `mcu` WHERE `mcu`.`ID` = 1');
        $this->assertEquals('SELECT * FROM `ms_core_users` `mcu` WHERE `mcu`.`ID` = 1',$this->ob->getQuerySql());
    }

    /**
     * @covers \Ms\Core\Entity\Db\QueryInfo::getUniqueId
     * @covers \Ms\Core\Entity\Db\QueryInfo::generateUniqueId
     */
    public function testGenerateUniqueId ()
    {
        $lastUniqueID = $this->ob->getUniqueId();
        $this->ob->generateUniqueId();
        $nowUniqueID = $this->ob->getUniqueId();
        $this->assertTrue(($lastUniqueID != $nowUniqueID));
    }

    /**
     * @covers \Ms\Core\Entity\Db\QueryInfo::start
     * @covers \Ms\Core\Entity\Db\QueryInfo::getQueryStart
     * @covers \Ms\Core\Entity\Db\QueryInfo::stop
     * @covers \Ms\Core\Entity\Db\QueryInfo::getQueryStop
     * @covers \Ms\Core\Entity\Db\QueryInfo::getQueryTime
     */
    public function testQueryTime ()
    {
        $this->ob->start();
        $start = $this->ob->getQueryStart();
        $this->ob->stop();
        $stop = $this->ob->getQueryStop();
        $this->assertEquals(($stop - $start),$this->ob->getQueryTime());
    }
}
<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Db\QueryStatistics;

/**
 * Класс \QueryStatisticsTest
 * Тесты класса \Ms\Core\Entity\Db\QueryStatistics
 */
class QueryStatisticsTest extends \PHPUnit\Framework\TestCase
{
    /** @var QueryStatistics */
    protected $ob = null;

    protected function setUp ()
    {
        $this->ob = new QueryStatistics();
    }

    /**
     * @covers \Ms\Core\Entity\Db\QueryStatistics::addQueryInfo
     * @covers \Ms\Core\Entity\Db\QueryStatistics::getQueryInfo
     */
    public function testAddQueryInfo ()
    {
        $qi = new \Ms\Core\Entity\Db\QueryInfo('SELECT * FROM ms_core_users');
        $uniqueID = $qi->getUniqueId();
        $this->ob->addQueryInfo($qi);
        $this->assertTrue($this->ob->offsetExists($uniqueID));
        $getQI = $this->ob->getQueryInfo($uniqueID);
        $this->assertTrue(($getQI->getUniqueId() == $uniqueID));
    }

    /**
     * @covers \Ms\Core\Entity\Db\QueryStatistics::getQueryCount
     */
    public function testGetQueryCount ()
    {
        $this->ob->addQueryInfo(new \Ms\Core\Entity\Db\QueryInfo(''));
        $this->ob->addQueryInfo(new \Ms\Core\Entity\Db\QueryInfo(''));
        $this->ob->addQueryInfo(new \Ms\Core\Entity\Db\QueryInfo(''));
        $this->assertEquals(3,$this->ob->getQueryCount());
    }

    /**
     * @covers \Ms\Core\Entity\Db\QueryStatistics::getAllQueryTime
     */
    public function testGetAllQueryTime ()
    {
        $q1 = new \Ms\Core\Entity\Db\QueryInfo('');
        $q2 = new \Ms\Core\Entity\Db\QueryInfo('');
        $q1->start();
        $q2->start();
        usleep(10000);
        $q1->stop();
        $q2->stop();
        $this->ob->addQueryInfo($q1);
        $this->ob->addQueryInfo($q2);
        $this->assertEquals((float)round($q1->getQueryTime() + $q2->getQueryTime(),5), $this->ob->getAllQueryTime());
    }
}
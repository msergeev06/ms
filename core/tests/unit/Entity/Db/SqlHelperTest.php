<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Db\SqlHelper;

/**
 * Класс \SqlHelperTest
 * Тесты класса \Ms\Core\Entity\Db\SqlHelper
 */
class SqlHelperTest extends \PHPUnit\Framework\TestCase
{
    /** @var SqlHelper */
    protected $helper = null;

    protected function setUp ()
    {
        $this->helper = new SqlHelper('ms_core_users');
    }

    /**
     * @covers \Ms\Core\Entity\Db\SqlHelper::getQuote
     * @covers \Ms\Core\Entity\Db\SqlHelper::wrapQuotes
     */
    public function testWrapQuotes ()
    {
        $this->assertEquals(
            $this->helper->getQuote() . "INFO" . $this->helper->getQuote(),
            $this->helper->wrapQuotes('INFO')
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\SqlHelper::getTableName
     * @covers \Ms\Core\Entity\Db\SqlHelper::wrapFieldQuotes
     */
    public function testWrapFieldQuotes ()
    {
        if (!empty($this->helper->getTableName()))
        {
            $this->assertEquals(
                $this->helper->getQuote() . $this->helper->getTableName() . $this->helper->getQuote() . '.'
                . $this->helper->getQuote() . 'FIELD' . $this->helper->getQuote()
                ,
                $this->helper->wrapFieldQuotes('FIELD')
            );
        }
        else
        {
            $this->assertEquals(
                $this->helper->getQuote() . 'FIELD' . $this->helper->getQuote(),
                $this->helper->wrapFieldQuotes('FIELD')
            );
        }
    }

    /**
     * @covers \Ms\Core\Entity\Db\SqlHelper::wrapTableQuotes
     */
    public function testWrapTableQuotes ()
    {
        if (empty($this->helper->getTableName()))
        {
            $this->assertTrue(empty($this->helper->wrapTableQuotes()));
        }
        else
        {
            $this->assertEquals(
                $this->helper->getQuote() . $this->helper->getTableName() . $this->helper->getQuote(),
                $this->helper->wrapTableQuotes()
            );
        }
    }

    /**
     * @covers \Ms\Core\Entity\Db\SqlHelper::getCountFunction
     */
    public function testGetCountFunction ()
    {
        $q = $this->helper->getQuote();
        $this->assertEquals('COUNT(*) '.$q.'COUNT'.$q,$this->helper->getCountFunction());
    }

    /**
     * @covers \Ms\Core\Entity\Db\SqlHelper::getMaxFunction
     */
    public function testGetMaxFunction ()
    {
        $q = $this->helper->getQuote();
        $t = $this->helper->getTableName();
        $this->assertEquals(
            'MAX(' . $q . $t . $q . '.' . $q . 'ID' . $q .') ' . $q . 'MAX_ID' . $q,
            $this->helper->getMaxFunction('ID')
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\SqlHelper::getMinFunction
     */
    public function testGetMinFunction ()
    {
        $q = $this->helper->getQuote();
        $t = $this->helper->getTableName();
        $this->assertEquals(
            'MIN(' . $q . $t . $q . '.' . $q . 'ID' . $q .') ' . $q . 'MIN_ID' . $q,
            $this->helper->getMinFunction('ID')
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\SqlHelper::getSumFunction
     */
    public function testGetSumFunction ()
    {
        $q = $this->helper->getQuote();
        $t = $this->helper->getTableName();
        $this->assertEquals(
            'SUM(' . $q . $t . $q . '.' . $q . 'ID' . $q .') ' . $q . 'SUM_ID' . $q,
            $this->helper->getSumFunction('ID')
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\SqlHelper::getAvgFunction
     */
    public function testGetAvgFunction ()
    {
        $q = $this->helper->getQuote();
        $t = $this->helper->getTableName();
        $this->assertEquals(
            'AVG(' . $q . $t . $q . '.' . $q . 'ID' . $q .') ' . $q . 'AVG_ID' . $q,
            $this->helper->getAvgFunction('ID')
        );
    }

}
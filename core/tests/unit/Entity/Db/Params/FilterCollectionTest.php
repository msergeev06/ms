<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Params\FilterCollection;

/**
 * Класс \FilterCollectionTest
 * Тесты класса \Ms\Core\Entity\Db\Params\FilterCollection
 */
class FilterCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Ms\Core\Entity\Db\Params\GetListParams */
    protected $getListParams = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = (\Ms\Core\Entity\System\Application::getInstance())
            ->setSettings()
        ;
        $this->getListParams = new \Ms\Core\Entity\Db\Params\GetListParams(
            \Ms\Core\Entity\Db\Tables\ORMController::getInstance(new \Ms\Core\Tables\UsersTable())
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\Params\FilterCollection::setLogicOr
     * @covers \Ms\Core\Entity\Db\Params\FilterCollection::setLogicAnd
     * @covers \Ms\Core\Entity\Db\Params\FilterCollection::getLogic
     * @covers \Ms\Core\Entity\Db\Params\FilterCollection::getParams
     * @covers \Ms\Core\Entity\Db\Params\FilterCollection::setFromArray
     * @covers \Ms\Core\Entity\Db\Params\FilterCollection::addFilter
     */
    public function testClassMethods ()
    {
        $ob = new FilterCollection($this->getListParams);
        $ob->setLogicOr();
        $this->assertEquals(FilterCollection::FILTER_LOGIC_OR, $ob->getLogic());
        $ob->setLogicAnd();
        $this->assertEquals(FilterCollection::FILTER_LOGIC_AND, $ob->getLogic());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\GetListParams::class,$ob->getParams());
        $ob->setFromArray(
            [
                '>ID' => 1
            ]
        );
        $this->assertEquals('ID',$ob->getFirst()->getFieldName());
        $ob->addFilter('NAME','Миша');
        $this->assertEquals('NAME',$ob->toArray()[1]->getFieldName());
    }
}
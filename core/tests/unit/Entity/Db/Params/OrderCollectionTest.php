<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Params\OrderCollection;

/**
 * Класс \OrderCollectionTest
 * Тесты класса \Ms\Core\Entity\Db\Params\OrderCollection
 */
class OrderCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Ms\Core\Entity\Db\Params\GetListParams */
    protected $getListParams = null;

    protected function setUp ()
    {
        \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;

        $this->getListParams = new \Ms\Core\Entity\Db\Params\GetListParams(
            \Ms\Core\Entity\Db\Tables\ORMController::getInstance(
                new \Ms\Core\Tables\UsersTable ()
            )
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\Params\OrderCollection::getParams
     * @covers \Ms\Core\Entity\Db\Params\OrderCollection::setFromArray
     * @covers \Ms\Core\Entity\Db\Params\OrderCollection::getOrder
     * @covers \Ms\Core\Entity\Db\Params\OrderCollection::addOrder
     * @covers \Ms\Core\Entity\Db\Params\OrderCollection::isExists
     */
    public function testClassMethods ()
    {
        $ob = new OrderCollection($this->getListParams);
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\GetListParams::class,$ob->getParams());
        $ob->setFromArray(
            [
                'ID' => OrderCollection::DIRECTION_DESC
            ]
        );
        $this->assertEquals(OrderCollection::DIRECTION_DESC, $ob->getOrder('ID')->getDirection());
        $ob->addOrder('LOGIN',OrderCollection::DIRECTION_DESC);
        $this->assertEquals(OrderCollection::DIRECTION_DESC,$ob->getOrder('LOGIN')->getDirection());
        $this->assertTrue($ob->isExists('ID'));
    }
}
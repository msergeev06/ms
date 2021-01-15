<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Params\SelectFieldsCollection;

/**
 * Класс \SelectFieldsCollectionTest
 * Тесты класса \Ms\Core\Entity\Db\Params\SelectFieldsCollection
 */
class SelectFieldsCollectionTest extends \PHPUnit\Framework\TestCase
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
                new \Ms\Core\Tables\UsersTable()
            )
        );
    }

    /**
     * @covers \Ms\Core\Entity\Db\Params\SelectFieldsCollection::getParams
     * @covers \Ms\Core\Entity\Db\Params\SelectFieldsCollection::addField
     * @covers \Ms\Core\Entity\Db\Params\SelectFieldsCollection::getField
     */
    public function testClassMethods ()
    {
        $ob = new SelectFieldsCollection($this->getListParams);
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\GetListParams::class,$ob->getParams());
        try
        {
            $ob->addField('ID', 'ID', new \Ms\Core\Tables\UsersTable());
        }
        catch (\Ms\Core\Exceptions\SystemException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Params\SelectField::class,$ob->getField('ID'));
    }
}
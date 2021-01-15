<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Tables\FieldsCollection;
use \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract;

/**
 * Класс \FieldsCollectionTest
 * Тесты класса \Ms\Core\Entity\Db\Tables\FieldsCollection
 */
class FieldsCollectionTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {

    }

    /**
     * @covers \Ms\Core\Entity\Db\Tables\FieldsCollection::addField
     * @covers \Ms\Core\Entity\Db\Tables\FieldsCollection::getField
     * @covers \Ms\Core\Entity\Db\Tables\FieldsCollection::deleteField
     * @covers \Ms\Core\Entity\Db\Tables\FieldsCollection::getFieldsWithDefaultValues
     * @covers \Ms\Core\Entity\Db\Tables\FieldsCollection::getList
     * @covers \Ms\Core\Entity\Db\Tables\FieldsCollection::getPrimaryField
     * @covers \Ms\Core\Entity\Db\Tables\FieldsCollection::isExists
     * @covers \Ms\Core\Entity\Db\Tables\FieldsCollection::merge
     */
    public function testClassMethods ()
    {
        $map = new FieldsCollection();
        $map->addField(
            (new \Ms\Core\Entity\Db\Fields\IntegerField('ID'))
                ->setDefaultCreate(1)
                ->setPrimary()
        );
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\IntegerField::class,$map->getField('ID'));
        $c = $map->getFieldsWithDefaultValues(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_CREATE);
        $this->assertInstanceOf(FieldsCollection::class,$c);
        $this->assertTrue($c->isExists('ID'));
        $this->assertTrue(in_array('ID',$map->getList()));
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\IntegerField::class,$map->getPrimaryField());

        $map2 = (new FieldsCollection())
            ->addField(
                new \Ms\Core\Entity\Db\Fields\StringField('NAME')
            )
        ;

        $map->deleteField('ID');
        $this->assertTrue(is_null($map->getField('ID')));
        $map->merge($map2);
        $this->assertTrue(in_array('NAME',$map->getList()));
    }
}
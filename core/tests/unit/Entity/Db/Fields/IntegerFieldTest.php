<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\IntegerField;

/**
 * Класс \IntegerFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\IntegerField
 */
class IntegerFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var null|IntegerField */
    protected $field = null;

    protected function setUp ()
    {
        $this->field = new IntegerField('ID');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Fields\IntegerField::fetchDataModification
     * @covers \Ms\Core\Entity\Db\Fields\IntegerField::saveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\IntegerField::setSize
     * @covers \Ms\Core\Entity\Db\Fields\IntegerField::getSize
     * @covers \Ms\Core\Entity\Db\Fields\IntegerField::getSqlValue
     */
    public function testClassMethods ()
    {
        $this->assertEquals('int',$this->field->getDataType());
        $this->assertEquals('integer',$this->field->getFieldType());
        $this->assertEquals(1,$this->field->fetchDataModification(1));
        $this->assertEquals(1, $this->field->saveDataModification(1.2));
        $this->field->setSize(12);
        $this->assertEquals(12, $this->field->getSize());
        $this->assertEquals('1',$this->field->getSqlValue(1));
    }
}
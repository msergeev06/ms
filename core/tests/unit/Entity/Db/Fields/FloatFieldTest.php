<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\FloatField;

/**
 * Класс \FloatFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\FloatField
 */
class FloatFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var null|FloatField */
    protected $field = null;

    protected function setUp ()
    {
        $this->field = new FloatField('SUM');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Fields\FloatField::getScale
     * @covers \Ms\Core\Entity\Db\Fields\FloatField::setScale
     * @covers \Ms\Core\Entity\Db\Fields\FloatField::saveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\FloatField::fetchDataModification
     * @covers \Ms\Core\Entity\Db\Fields\FloatField::getSqlValue
     */
    public function testClassMethods ()
    {
        $this->assertEquals('float',$this->field->getDataType());
        $this->assertEquals('float',$this->field->getFieldType());
        $this->field->setScale(1);
        $this->assertEquals(1,$this->field->getScale());
        $this->assertEquals(1.3,$this->field->fetchDataModification(1.32345));
        $this->assertEquals(1.3,$this->field->saveDataModification(1.32345));
        $this->assertEquals('1.3',$this->field->getSqlValue(1.3));
    }
}
<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\BooleanField;
use \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract;

/**
 * Класс \BooleanFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\BooleanField
 */
class BooleanFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var null|BooleanField */
    protected $field = null;

    protected function setUp ()
    {
        $this->field = new BooleanField('ACTIVE');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::fetchDataModification
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::saveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::getDefaultValueDB
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::getSize
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::getSqlValue
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::getValues
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::normalizeValue
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::setDefaultCreate
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::setDefaultInsert
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::setDefaultUpdate
     * @covers \Ms\Core\Entity\Db\Fields\BooleanField::setDefaultValue
     */
    public function testClassMethods ()
    {
        $this->assertEquals('varchar',$this->field->getDataType());
        $this->assertEquals('boolean',$this->field->getFieldType());
        $this->assertEquals(1,$this->field->getSize());
        $this->assertTrue(
            is_array($this->field->getValues())
            && $this->field->getValues()[0] === false
            && $this->field->getValues()[1] === true
        );

        $this->assertTrue($this->field->fetchDataModification('Y'));
        $this->assertEquals('N',$this->field->saveDataModification(false));
        try
        {
            $this->field->setDefaultCreate(true);
            $this->field->setDefaultInsert(true);
            $this->field->setDefaultUpdate(true);
            $this->field->setDefaultValue(true);
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentTypeException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
        $this->assertEquals('Y',$this->field->getDefaultValueDB());
        $this->assertEquals("'Y'",$this->field->getSqlValue(true));
        $this->assertFalse($this->field->normalizeValue('0'));
        $this->assertTrue($this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_CREATE));
        $this->assertTrue($this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_INSERT));
        $this->assertTrue($this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_UPDATE));
    }
}
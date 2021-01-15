<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use Ms\Core\Entity\Db\Fields\ScalarFieldAbstract;

class TestScalarField extends ScalarFieldAbstract
{
    public function __construct (string $name)
    {
        parent::__construct($name);
        $this->dataType = 'varchar';
        $this->fieldType = 'string';
    }

    public function getValues ()
    {
        return $this->values;
    }
}

/**
 * Класс \ScalarFieldAbstractTest
 * Тесты класса Ms\Core\Entity\Db\Fields\ScalarFieldAbstract
 */
class ScalarFieldAbstractTest extends \PHPUnit\Framework\TestCase
{
    /** @var null|TestScalarField */
    protected $field = null;

    protected function setUp ()
    {
        $this->field = new TestScalarField('TEST');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::fetchDataModification
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::saveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setAllowedValues
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::getAllowedValues
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::getAllowedValuesRange
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setAllowedValuesRange
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::getColumnName
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setColumnName
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setDefaultValue
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::getDefaultValue
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setArRun
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::getRun
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::getSqlValue
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setAutocomplete
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::isAutocomplete
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setDefaultValueSql
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::isDefaultSql
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setPrimary
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::isPrimary
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setRequired
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::isRequired
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setRequiredNull
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::isRequiredNull
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setUnique
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::isUnique
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setDefaultCreate
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setDefaultCreateSql
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setDefaultInsert
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setDefaultInsertSql
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setDefaultUpdate
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setDefaultUpdateSql
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::setValues
     * @covers \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract::__toString
     */
    public function testClassMethods ()
    {
        $this->assertEquals('string',$this->field->fetchDataModification('string'));
        $this->assertEquals('string',$this->field->saveDataModification('string'));
        $this->field->setAllowedValues(['yes','no']);
        $this->assertTrue(
            is_array($this->field->getAllowedValues())
                && $this->field->getAllowedValues()[0] == 'yes'
                && $this->field->getAllowedValues()[1] == 'no'
        );
        $this->field->setAllowedValuesRange(0.35, 0.70);
        $this->assertTrue(is_array($this->field->getAllowedValuesRange()));
        $this->assertArrayHasKey('min',$this->field->getAllowedValuesRange());
        $this->assertArrayHasKey('max',$this->field->getAllowedValuesRange());
        $this->assertTrue(
            $this->field->getAllowedValuesRange()['min'] == 0.35
            && $this->field->getAllowedValuesRange()['max'] == 0.70
        );
        $this->field->setColumnName('TEST_COLUMN');
        $this->assertEquals('TEST_COLUMN',$this->field->getColumnName());
        $this->field->setDefaultValue('default_value');
        $this->assertEquals('default_value',$this->field->getDefaultValue());
        $this->field->setArRun([]);
        $this->assertTrue(is_array($this->field->getRun()));
        $this->assertEquals("'string'",$this->field->getSqlValue('string'));
        $this->field->setAutocomplete();
        $this->assertTrue($this->field->isAutocomplete());
        $this->field->setDefaultValueSql();
        $this->assertTrue($this->field->isDefaultSql());
        $this->field->setPrimary();
        $this->assertTrue($this->field->isPrimary());
        $this->field->setRequired();
        $this->assertTrue($this->field->isRequired());
        $this->field->setRequiredNull();
        $this->assertTrue($this->field->isRequiredNull());
        $this->field->setUnique();
        $this->assertTrue($this->field->isUnique());
        $this->field->setDefaultCreate('default_create');
        $this->field->setDefaultCreateSql();
        $this->assertEquals(
            'default_create',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_CREATE)
        );
        $this->assertTrue($this->field->isDefaultSql(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_CREATE));
        $this->field->setDefaultInsert('default_insert');
        $this->field->setDefaultInsertSql();
        $this->assertEquals(
            'default_insert',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_INSERT)
        );
        $this->assertTrue($this->field->isDefaultSql(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_INSERT));
        $this->field->setDefaultUpdate('default_update');
        $this->field->setDefaultUpdateSql();
        $this->assertEquals(
            'default_update',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_UPDATE)
        );
        $this->assertTrue($this->field->isDefaultSql(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_UPDATE));
        $this->field->setValues([]);
        $this->assertTrue(is_array($this->field->getValues()));
        $this->assertEquals('default_value',$this->field->__toString());
    }
}
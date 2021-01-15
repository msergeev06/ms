<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\StringField;
use \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract;

/**
 * Класс \StringFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\StringField
 */
class StringFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var StringField */
    protected $field = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->field = new StringField('NAME');
        $this->app = (\Ms\Core\Entity\System\Application::getInstance())
            ->setSettings()
            ->setConnectionPool()
        ;
        $this->app->getSettings()->mergeLocalSettings();
        $this->app->getConnectionPool()->getConnection()->connect();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Fields\StringField::setSize
     * @covers \Ms\Core\Entity\Db\Fields\StringField::getSize
     * @covers \Ms\Core\Entity\Db\Fields\StringField::fetchDataModification
     * @covers \Ms\Core\Entity\Db\Fields\StringField::saveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\StringField::setDefaultCreate
     * @covers \Ms\Core\Entity\Db\Fields\StringField::setDefaultInsert
     * @covers \Ms\Core\Entity\Db\Fields\StringField::setDefaultUpdate
     * @covers \Ms\Core\Entity\Db\Fields\StringField::setDefaultValue
     */
    public function testClassMethods ()
    {
        $this->assertEquals('varchar',$this->field->getDataType());
        $this->assertEquals('string',$this->field->getFieldType());
        $this->field->setSize (300);
        $this->assertEquals(255,$this->field->getSize());
        $this->assertEquals('string',$this->field->fetchDataModification('string'));
        $this->assertEquals('123',$this->field->saveDataModification(123));
        try
        {
            $this->field->setDefaultCreate('string_create');
            $this->field->setDefaultInsert('string_insert');
            $this->field->setDefaultUpdate('string_update');
            $this->field->setDefaultValue('string_value');
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentTypeException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
        $this->assertEquals(
            'string_create',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_CREATE)
        );
        $this->assertEquals(
            'string_insert',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_INSERT)
        );
        $this->assertEquals(
            'string_update',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_UPDATE)
        );
        $this->assertEquals(
            'string_value',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_VALUE)
        );
    }
}
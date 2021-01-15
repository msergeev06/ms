<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use Ms\Core\Entity\Db\Fields\BigIntField;

/**
 * Класс \BigIntFieldTest
 * Тесты класса Ms\Core\Entity\Db\Fields\BigIntField
 */
class BigIntFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var null|BigIntField */
    protected $field = null;

    protected function setUp ()
    {
        $this->field = new BigIntField('TIMESTAMP');
    }

    public function testClassMethods ()
    {
        $this->assertEquals('bigint',$this->field->getDataType());
        $this->assertEquals('integer',$this->field->getFieldType());
        $this->assertEquals(20,$this->field->getSize());
    }
}
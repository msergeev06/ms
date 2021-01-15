<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\LongtextField;

/**
 * Класс \LongtextFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\LongtextField
 */
class LongtextFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var LongtextField */
    protected $field = null;

    protected function setUp ()
    {
        $this->field = new LongtextField('DESCRIPTION');
    }

    public function testClassMethods ()
    {
        $this->assertEquals('longtext',$this->field->getDataType());
        $this->assertEquals('text',$this->field->getFieldType());
    }
}
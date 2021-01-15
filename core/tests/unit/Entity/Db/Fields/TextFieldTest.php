<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\TextField;

/**
 * Класс \TextFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\TextField
 */
class TextFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var TextField */
    protected $field = null;

    protected function setUp ()
    {
        $this->field = new TextField('DESCRIPTION');
    }

    public function testClassMethods ()
    {
        $this->assertEquals('text',$this->field->getDataType());
        $this->assertEquals('text',$this->field->getFieldType());
    }
}
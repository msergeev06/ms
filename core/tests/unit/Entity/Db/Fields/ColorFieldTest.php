<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\ColorField;

/**
 * Класс \ColorFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\ColorField
 */
class ColorFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var null|ColorField */
    protected $field = null;
    protected $color = null;

    protected function setUp ()
    {
        $this->field = new ColorField('COLOR');
        $this->color = new \Ms\Core\Entity\Type\Color('#4CAE4C');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Fields\ColorField::saveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\ColorField::fetchDataModification
     */
    public function testClassMethods ()
    {
        $this->assertEquals('int',$this->field->getDataType());
        $this->assertEquals(\Ms\Core\Entity\Type\Color::class,$this->field->getFieldType());
        try
        {
            $colorInt = $this->field->saveDataModification($this->color);
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentTypeException $e)
        {
            $this->assertTrue(false,$e->getMessage());
            return;
        }
        $this->assertEquals(5025356,$colorInt);
        /** @var \Ms\Core\Entity\Type\Color $color */
        $color = $this->field->fetchDataModification($colorInt);
        $this->assertEquals('#4CAE4C',$color->getFormatHexString());
    }
}
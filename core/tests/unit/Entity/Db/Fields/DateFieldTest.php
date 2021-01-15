<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\DateField;

/**
 * Класс \DateFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\DateField
 */
class DateFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var null|DateField */
    protected $field = null;
    /** @var null|\Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->field = new DateField('DATE');
        $this->app = (\Ms\Core\Entity\System\Application::getInstance())->setSettings();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Fields\DateField::saveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\DateField::fetchDataModification
     * @covers \Ms\Core\Entity\Db\Fields\DateField::getSqlValue
     */
    public function testClassMethods ()
    {
        $this->assertEquals('date',$this->field->getDataType());
        $this->assertEquals(\Ms\Core\Entity\Type\Date::class,$this->field->getFieldType());
        $date = $this->field->fetchDataModification('2010-05-07');
        $this->assertInstanceOf(\Ms\Core\Entity\Type\Date::class, $date);
        $this->assertEquals('07.05.2010', $date->getDateSite());
        try
        {
            $this->assertEquals('2010-05-07', $this->field->saveDataModification($date));
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentTypeException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
        $this->assertEquals("'2010-05-07'",$this->field->getSqlValue($date));
    }
}
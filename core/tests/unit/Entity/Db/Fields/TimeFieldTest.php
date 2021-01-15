<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\TimeField;

/**
 * Класс \TimeFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\TimeField
 */
class TimeFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var null|TimeField */
    protected $field = null;
    /** @var null|\Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->field = new TimeField('TIME');
        $this->app = \Ms\Core\Entity\System\Application::getInstance();
        $this->app
            ->setSettings()
        ;
    }

    /**
     * @covers \Ms\Core\Entity\Db\Fields\TimeField::fetchDataModification
     * @covers \Ms\Core\Entity\Db\Fields\TimeField::saveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\TimeField::getSqlValue
     */
    public function testClassMethods ()
    {
        $this->assertEquals('time',$this->field->getDataType());
        $this->assertEquals(\Ms\Core\Entity\Type\Date::class,$this->field->getFieldType());
        /** @var \Ms\Core\Entity\Type\Date $time */
        $time = $this->field->fetchDataModification('12:56:45');
        $this->assertInstanceOf(\Ms\Core\Entity\Type\Date::class,$time);
        $this->assertEquals('12:56:45',$time->getTimeSite());
        $this->assertEquals('12:56:45',$this->field->saveDataModification($time));
        $this->assertEquals("'12:56:45'",$this->field->getSqlValue($time));
    }
}
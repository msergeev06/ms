<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\DateTimeField;
use \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract;

/**
 * Класс \DateTimeFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\DateTimeField
 */
class DateTimeFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var null|DateTimeField */
    protected $field = null;
    /** @var null|\Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->field = new DateTimeField('DATE_TIME');
        $this->app = (\Ms\Core\Entity\System\Application::getInstance())->setSettings();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Fields\DateTimeField::fetchDataModification
     * @covers \Ms\Core\Entity\Db\Fields\DateTimeField::saveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\DateTimeField::getSqlValue
     * @covers \Ms\Core\Entity\Db\Fields\DateTimeField::setDefaultValue
     * @covers \Ms\Core\Entity\Db\Fields\DateTimeField::setDefaultCreate
     * @covers \Ms\Core\Entity\Db\Fields\DateTimeField::setDefaultInsert
     * @covers \Ms\Core\Entity\Db\Fields\DateTimeField::setDefaultUpdate
     */
    public function testClassMethods ()
    {
        $this->assertEquals('datetime',$this->field->getDataType());
        $this->assertEquals(\Ms\Core\Entity\Type\Date::class,$this->field->getFieldType());
        $dateTime = $this->field->fetchDataModification('2010-07-05 12:34:56');
        $this->assertInstanceOf(\Ms\Core\Entity\Type\Date::class, $dateTime);
        $this->assertEquals('05.07.2010 12:34:56',$dateTime->getDateTimeSite());
        $this->assertEquals('2010-07-05 12:34:56',$this->field->saveDataModification($dateTime));
        $this->assertEquals("'2010-07-05 12:34:56'", $this->field->getSqlValue($dateTime));
        try
        {
            $this->field->setDefaultValue($dateTime);
            $this->field->setDefaultCreate($dateTime);
            $this->field->setDefaultInsert($dateTime);
            $this->field->setDefaultUpdate($dateTime);
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentTypeException $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }
        $this->assertEquals(
            '05.07.2010 12:34:56',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_VALUE)->getDateTimeSite()
        );
        $this->assertEquals(
            '05.07.2010 12:34:56',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_CREATE)->getDateTimeSite()
        );
        $this->assertEquals(
            '05.07.2010 12:34:56',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_INSERT)->getDateTimeSite()
        );
        $this->assertEquals(
            '05.07.2010 12:34:56',
            $this->field->getDefaultValue(ScalarFieldAbstract::DEFAULT_VALUE_TYPE_UPDATE)->getDateTimeSite()
        );
    }
}
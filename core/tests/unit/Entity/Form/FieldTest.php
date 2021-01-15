<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Form\Field;

class TestField extends Field
{
    public function __construct (
        $type = null,
        $title = null,
        $help = null,
        $name = null,
        $default_value = null,
        $requiredValue = false,
        $functionCheck = null
    ) {
        parent::__construct($type, $title, $help, $name, $default_value, $requiredValue, $functionCheck);
    }

    public function showField ($value = null)
    {
        return 'TestField';
    }
}

/**
 * Класс \FieldTest
 * Тесты класса \Ms\Core\Entity\Form\Field
 */
class FieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var TestField */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
            ->setConnectionPool()
            ->setApplicationParametersCollection()
        ;
        $this->app->getSettings()->mergeLocalSettings();
        $this->app->setConnectionDefault();

        $this->ob = new TestField(
            'test',
            'Test field',
            'This is test field',
            'test_field',
            null,
            true,
            [self::class,'checkField']
        );
    }

    public function checkField ()
    {

    }

    /**
     * @covers \Ms\Core\Entity\Form\Field::check
     */
    public function testCheck ()
    {
        $arCheck = $this->ob->check();
        $this->assertTrue(
            is_array($arCheck)
            && count($arCheck) == 2
            && $arCheck[0] == self::class
            && $arCheck[1] == 'checkField'
        );
    }

    /**
     * @covers \Ms\Core\Entity\Form\Field::getDefaultValue
     */
    public function testGetDefaultValue ()
    {
        $this->assertTrue(is_null($this->ob->getDefaultValue()));
    }

    /**
     * @covers \Ms\Core\Entity\Form\Field::getFunctionCheck
     */
    public function testGetFunctionCheck ()
    {
        $arCheck = $this->ob->getFunctionCheck();
        $this->assertTrue(
            is_array($arCheck)
            && count($arCheck) == 2
            && $arCheck[0] == self::class
            && $arCheck[1] == 'checkField'
        );
    }

    /**
     * @covers \Ms\Core\Entity\Form\Field::getName
     */
    public function testGetName ()
    {
        $this->assertEquals('test_field',$this->ob->getName());
    }

    /**
     * @covers \Ms\Core\Entity\Form\Field::getTitle
     */
    public function testGetTitle ()
    {
        $this->assertEquals('Test field',$this->ob->getTitle());
    }

    /**
     * @covers \Ms\Core\Entity\Form\Field::getType
     */
    public function testGetType ()
    {
        $this->assertEquals('test',$this->ob->getType());
    }
}
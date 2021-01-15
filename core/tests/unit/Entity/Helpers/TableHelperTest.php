<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use Ms\Core\Entity\Db\Links\ForeignKey;
use Ms\Core\Entity\Db\Links\LinkedField;
use \Ms\Core\Entity\Helpers\TableHelper;
use \Ms\Core\Entity\Db\Fields\ScalarFieldAbstract as Type;
use Ms\Core\Entity\System\Application;

/**
 * Класс \TableHelperTest
 * Тесты класса \Ms\Core\Entity\Helpers\TableHelper
 */
class TableHelperTest extends \PHPUnit\Framework\TestCase
{
    /** @var Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();
    }

    /**
     * @covers \Ms\Core\Entity\Helpers\TableHelper::primaryField
     */
    public function testPrimaryField ()
    {
        $field = TableHelper::getInstance()->primaryField();
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\IntegerField::class,$field);
        $this->assertEquals('ID',$field->getColumnName());
        $this->assertTrue($field->isPrimary());
        $this->assertTrue($field->isAutocomplete());
        $this->assertEquals('Ключ',$field->getTitle());
    }

    /**
     * @covers \Ms\Core\Entity\Helpers\TableHelper::activeField
     */
    public function testActiveField ()
    {
        $field = TableHelper::getInstance()->activeField();
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\BooleanField::class,$field);
        $this->assertEquals('ACTIVE',$field->getColumnName());
        $this->assertTrue($field->isRequired());
        $this->assertEquals('Активность',$field->getTitle());
        $this->assertTrue($field->getDefaultValue(Type::DEFAULT_VALUE_TYPE_CREATE));
        $this->assertTrue($field->getDefaultValue(Type::DEFAULT_VALUE_TYPE_INSERT));
    }

    /**
     * @covers \Ms\Core\Entity\Helpers\TableHelper::sortField
     */
    public function testSortField ()
    {
        $field = TableHelper::getInstance()->sortField();
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\IntegerField::class,$field);
        $this->assertEquals('SORT',$field->getColumnName());
        $this->assertTrue($field->isRequired());
        $this->assertEquals(500,$field->getDefaultValue(Type::DEFAULT_VALUE_TYPE_CREATE));
        $this->assertEquals(500,$field->getDefaultValue(Type::DEFAULT_VALUE_TYPE_INSERT));
        $this->assertEquals('Сортировка',$field->getTitle());
    }

    /**
     * @covers \Ms\Core\Entity\Helpers\TableHelper::createdByField
     */
    public function testCreatedByField ()
    {
        $field = TableHelper::getInstance()->createdByField();
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\IntegerField::class,$field);
        $this->assertEquals('CREATED_BY',$field->getColumnName());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->isRequiredNull());
        $user = Application::getInstance()->getUser();
        if ($user)
        {
            $userID = $user->getID();
        }
        else
        {
            $userID = 0;
        }
        $this->assertEquals(
            $userID,
            $field->getDefaultValue(Type::DEFAULT_VALUE_TYPE_INSERT)
        );
        $link = $field->getLink();
        $this->assertInstanceOf(LinkedField::class, $link);
        $this->assertEquals(\Ms\Core\Tables\UsersTable::class, $link->getTable()->getClassName());
        $this->assertEquals('ID',$link->getFieldName());
        $foreign = $link->getForeignKeySetup();
        $this->assertInstanceOf(ForeignKey::class,$foreign);
        $this->assertEquals(ForeignKey::FOREIGN_CASCADE,$foreign->getOnUpdate());
        $this->assertEquals(ForeignKey::FOREIGN_SET_NULL,$foreign->getOnDelete());
        $this->assertEquals('ID пользователя кем создан',$field->getTitle());
    }

    /**
     * @covers \Ms\Core\Entity\Helpers\TableHelper::createdDateField
     */
    public function testCreatedDateField ()
    {
        $field = TableHelper::getInstance()->createdDateField();
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\DateTimeField::class,$field);
        $this->assertEquals('CREATED_DATE',$field->getColumnName());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->isRequiredNull());
        $this->assertEquals('Дата создания',$field->getTitle());
        $this->assertInstanceOf(
            \Ms\Core\Entity\Type\Date::class,
            $field->getDefaultValue(Type::DEFAULT_VALUE_TYPE_INSERT)
        );
    }

    /**
     * @covers \Ms\Core\Entity\Helpers\TableHelper::updatedByField
     */
    public function testUpdatedByField ()
    {
        $field = TableHelper::getInstance()->updatedByField();
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\IntegerField::class,$field);
        $this->assertEquals('UPDATED_BY',$field->getColumnName());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->isRequiredNull());
        $user = Application::getInstance()->getUser();
        if ($user)
        {
            $userID = $user->getID();
        }
        else
        {
            $userID = 0;
        }
        $this->assertEquals(
            $userID,
            $field->getDefaultValue(Type::DEFAULT_VALUE_TYPE_INSERT)
        );
        $link = $field->getLink();
        $this->assertInstanceOf(LinkedField::class, $link);
        $this->assertEquals(\Ms\Core\Tables\UsersTable::class, $link->getTable()->getClassName());
        $this->assertEquals('ID',$link->getFieldName());
        $foreign = $link->getForeignKeySetup();
        $this->assertInstanceOf(ForeignKey::class,$foreign);
        $this->assertEquals(ForeignKey::FOREIGN_CASCADE,$foreign->getOnUpdate());
        $this->assertEquals(ForeignKey::FOREIGN_SET_NULL,$foreign->getOnDelete());
        $this->assertEquals('ID пользователя, кем изменен',$field->getTitle());
    }

    /**
     * @covers \Ms\Core\Entity\Helpers\TableHelper::updatedDateField
     */
    public function testUpdatedDateField ()
    {
        $field = TableHelper::getInstance()->updatedDateField();
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\DateTimeField::class,$field);
        $this->assertEquals('UPDATED_DATE',$field->getColumnName());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->isRequiredNull());
        $this->assertEquals('Дата изменения',$field->getTitle());
        $this->assertInstanceOf(
            \Ms\Core\Entity\Type\Date::class,
            $field->getDefaultValue(Type::DEFAULT_VALUE_TYPE_UPDATE)
        );
    }
}

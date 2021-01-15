<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Params\SelectField;

/**
 * Класс \SelectFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Params\SelectField
 */
class SelectFieldTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp ()
    {
        \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
        ;
    }

    /**
     * @covers \Ms\Core\Entity\Db\Params\SelectField::getTable
     * @covers \Ms\Core\Entity\Db\Params\SelectField::getTableAlias
     * @covers \Ms\Core\Entity\Db\Params\SelectField::getField
     * @covers \Ms\Core\Entity\Db\Params\SelectField::getFieldColumnName
     * @covers \Ms\Core\Entity\Db\Params\SelectField::getFieldAlias
     */
    public function testClassMethods ()
    {
        try
        {
            $ob = new SelectField(
                new \Ms\Core\Tables\UsersTable(),
                'a',
                'ID',
                'ID'
            );
        }
        catch (\Ms\Core\Exceptions\SystemException $e)
        {
            $this->assertTrue(false, $e->getMessage());
            return;
        }
        $this->assertInstanceOf(\Ms\Core\Tables\UsersTable::class,$ob->getTable());
        $this->assertEquals('a',$ob->getTableAlias());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Fields\IntegerField::class,$ob->getField());
        $this->assertEquals('ID',$ob->getFieldColumnName());
        $this->assertEquals('ID',$ob->getFieldAlias());
    }
}
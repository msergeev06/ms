<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Links\LinkedField;

/**
 * Класс \LinkedFieldTest
 * Тесты класса \Ms\Core\Entity\Db\Links\LinkedField
 */
class LinkedFieldTest extends \PHPUnit\Framework\TestCase
{
    /** @var LinkedField */
    protected $obj = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = (\Ms\Core\Entity\System\Application::getInstance())
            ->setSettings()
        ;
        $this->obj = new LinkedField(new \Ms\Core\Tables\UsersTable(),'ID');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Links\LinkedField::setUseForeign
     * @covers \Ms\Core\Entity\Db\Links\LinkedField::isUseForeign
     * @covers \Ms\Core\Entity\Db\Links\LinkedField::setForeignKeySetup
     * @covers \Ms\Core\Entity\Db\Links\LinkedField::getForeignKeySetup
     * @covers \Ms\Core\Entity\Db\Links\LinkedField::setTable
     * @covers \Ms\Core\Entity\Db\Links\LinkedField::getTable
     * @covers \Ms\Core\Entity\Db\Links\LinkedField::setFieldName
     * @covers \Ms\Core\Entity\Db\Links\LinkedField::getFieldName
     */
    public function testClassMethods ()
    {
        $this->assertTrue($this->obj->isUseForeign());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\Links\ForeignKey::class,$this->obj->getForeignKeySetup());
        $this->assertInstanceOf(\Ms\Core\Tables\UsersTable::class,$this->obj->getTable());
        $this->assertEquals('ID',$this->obj->getFieldName());
    }
}
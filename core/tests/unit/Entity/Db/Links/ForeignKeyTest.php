<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Links\ForeignKey;

/**
 * Класс \ForeignKeyTest
 * Тесты класса \Ms\Core\Entity\Db\Links\ForeignKey
 */
class ForeignKeyTest extends \PHPUnit\Framework\TestCase
{
    /** @var ForeignKey */
    protected $obj = null;

    protected function setUp ()
    {
        $this->obj = new ForeignKey();
    }

    /**
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::setOnUpdateCascade
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::setOnDeleteCascade
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::getOnUpdate
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::getOnDelete
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::setOnUpdateSetNull
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::setOnUpdateNoAction
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::setOnUpdateRestrict
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::setOnUpdateSetDefault
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::setOnDeleteSetNull
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::setOnDeleteNoAction
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::setOnDeleteRestrict
     * @covers \Ms\Core\Entity\Db\Links\ForeignKey::setOnDeleteSetDefault
     */
    public function testClassMethods ()
    {
        $this->assertEquals(ForeignKey::FOREIGN_CASCADE,$this->obj->getOnUpdate());
        $this->assertEquals(ForeignKey::FOREIGN_CASCADE,$this->obj->getOnDelete());
        $this->obj->setOnUpdateSetNull();
        $this->assertEquals(ForeignKey::FOREIGN_SET_NULL,$this->obj->getOnUpdate());
        $this->obj->setOnUpdateNoAction();
        $this->assertEquals(ForeignKey::FOREIGN_NO_ACTION,$this->obj->getOnUpdate());
        $this->obj->setOnUpdateRestrict();
        $this->assertEquals(ForeignKey::FOREIGN_RESTRICT,$this->obj->getOnUpdate());
        $this->obj->setOnUpdateSetDefault();
        $this->assertEquals(ForeignKey::FOREIGN_SET_DEFAULT,$this->obj->getOnUpdate());
        $this->obj->setOnDeleteSetNull();
        $this->assertEquals(ForeignKey::FOREIGN_SET_NULL,$this->obj->getOnDelete());
        $this->obj->setOnDeleteNoAction();
        $this->assertEquals(ForeignKey::FOREIGN_NO_ACTION,$this->obj->getOnDelete());
        $this->obj->setOnDeleteRestrict();
        $this->assertEquals(ForeignKey::FOREIGN_RESTRICT,$this->obj->getOnDelete());
        $this->obj->setOnDeleteSetDefault();
        $this->assertEquals(ForeignKey::FOREIGN_SET_DEFAULT,$this->obj->getOnDelete());
    }
}
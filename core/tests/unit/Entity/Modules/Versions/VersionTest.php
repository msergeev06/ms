<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Modules\Versions\Version;

/**
 * Класс \VersionTest
 * Тесты класса \Ms\Core\Entity\Modules\Versions\Version
 */
class VersionTest extends \PHPUnit\Framework\TestCase
{
    /** @var Version */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->ob = new Version('>V.1.0.*');
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\Version::getRawVersion
     */
    public function testGetRawVersion ()
    {
        $this->assertEquals('>v.1.0.*',$this->ob->getRawVersion());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\Version::getOperator
     */
    public function testGetOperator ()
    {
        $this->assertEquals('>',$this->ob->getOperator());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\Version::getMajor
     */
    public function testGetMajor ()
    {
        $this->assertEquals(1,$this->ob->getMajor());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\Version::getMinor
     */
    public function testGetMinor ()
    {
        $this->assertEquals(0,$this->ob->getMinor());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\Version::getPatch
     */
    public function testGetPatch ()
    {
        $this->assertTrue(is_null($this->ob->getPatch()));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\Version::getOther
     */
    public function testGetOther ()
    {
        $this->assertTrue(is_null($this->ob->getOther()));
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\Version::getModuleVersion
     */
    public function testGetModuleVersion ()
    {
        $this->assertEquals('1.0.0',$this->ob->getModuleVersion());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\Version::getClearVersion
     */
    public function testGetClearVersion ()
    {
        $this->assertEquals('1.0.*',$this->ob->getClearVersion());
    }
}
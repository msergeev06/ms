<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Modules\Versions\VersionComparator;

/**
 * Класс \VersionComparatorTest
 * Тесты класса \Ms\Core\Entity\Modules\Versions\VersionComparator
 */
class VersionComparatorTest extends \PHPUnit\Framework\TestCase
{
    /** @var VersionComparator */
    protected $ob = null;
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->ob = VersionComparator::getInstance();
    }

    public function compareProvider ()
    {
        //TODO: расширить проверяемые условия
        return [
            ['1.2.3', '=', '1.2.3', true],
            ['1.2.3', '!=', '1.2.3', false],
            ['1.2.3', '!', '1.2.4', true],
            ['1.2.3', '<', '1.2.4', true],
            ['1.2.3', '<=', '1.2.4', true],
            ['1.2.5', '>', '1.2.4', true],
            ['1.2.5', '>=', '1.2.4', true],
            ['1.2.5', '~', '1.2', true],
            ['1.3.5', '^', '1.2', true]
        ];
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Versions\VersionComparator::compare
     *
     * @dataProvider compareProvider
     */
    public function testCompare ($checkedVersion, $operator, $compareVersion, $true)
    {
        try
        {
            if ($true)
            {
                $this->assertTrue($this->ob->compare($checkedVersion, $operator, $compareVersion));
            }
            else
            {
                $this->assertFalse($this->ob->compare($checkedVersion, $operator, $compareVersion));
            }
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentException $e)
        {
            $this->markTestSkipped($e->getMessage());
        }
    }
}
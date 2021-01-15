<?php
require_once(dirname(__FILE__) . '/../../autoloader.php');

use PHPUnit\Framework\TestCase;
use \Ms\Core\Lib\Tools;

class ToolsTest extends TestCase
{
    protected $app = null;

    /**
     * @covers \Ms\Core\Lib\Tools::boolToStr
     */
    public function testBoolToStr ()
    {
        $this->assertEquals('Y', Tools::boolToStr(true));
        $this->assertEquals('N', Tools::boolToStr(false));
    }

    /**
     * @covers \Ms\Core\Lib\Tools::isSerialized
     */
    public function testIsSerialized ()
    {
        $this->assertTrue(Tools::isSerialized(serialize(['field' => 'value'])));
        $this->assertFalse(Tools::isSerialized('{erw:wer}'));
    }

    /**
     * @covers \Ms\Core\Lib\Tools::convertRusToLat
     */
    public function testConvertRusToLat ()
    {
        $char = Tools::convertRusToLat('Ё');
        $this->assertEquals('E', $char);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::cropString
     */
    public function testCropString ()
    {
        $string = 'Длинная предлинная строка, которую нужно обрезать до 30 символов и поставить три точки';
        $res = Tools::cropString($string, 30);
        $this->assertEquals('Длинная предлинная строка, ...', $res);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::generateCode
     */
    public function testGenerateCode ()
    {
        $code = Tools::generateCode('Данный (тест) успешно пройден!');
        $this->assertEquals('dannii_test_uspeshno_proiden', $code);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::getClassNameByTableName
     */
    public function testGetClassNameByTableName ()
    {
        $res = Tools::getClassNameByTableName('ms_core_users');
        $this->assertEquals('Ms\\Core\\Tables\\UsersTable', $res);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::getCurDir
     */
    public function testGetCurDir ()
    {
        $this->assertEquals('/index.php/test1', Tools::getCurDir());
    }

    /**
     * @covers \Ms\Core\Lib\Tools::getCurDir
     */
    public function testGetCurPath ()
    {
        $this->assertEquals('/index.php/test1/test2', Tools::getCurPath());
    }

    /**
     * @covers \Ms\Core\Lib\Tools::getFileByTableName
     */
    public function testGetFileByTableName ()
    {
        $res = Tools::getFileByTableName('ms_core_users');
        $this->assertContains('/ms/core/classes/Tables/UsersTable.php', $res);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::getFirstChar
     */
    public function testGetFirstChar ()
    {
        try
        {
            $res = Tools::getFirstChar('Хутор');
        }
        catch (\Exception $e)
        {
            $res = '';
        }
        $this->assertEquals('Х', $res);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::HTMLToTxt
     */
    public function testHTMLToTxt ()
    {
        $res = Tools::HTMLToTxt(
            '<h1>Title</h1><p>New paragraph</p><br>'
            . '<div>List:<ul><li>item1</li><li>item2</li></ul></div>'
            . '<hr>'
            .'<a href="https://cloud.dobrozhil.ru">Home</a>'
        );
        $this->assertEquals(
            "Title\r\n\r\nNew paragraph\r\n"
            ."List:\r\n\r\n- item1\r\n- item2\r\n"
            ."----------------------\r\n"
            ."Home [ https://cloud.dobrozhil.ru ]"
            ,
            $res
        );
    }

    /**
     * @covers \Ms\Core\Lib\Tools::isBetween
     */
    public function testIsBetween ()
    {
        $this->assertTrue(Tools::isBetween(14.352, 14.01, 15, false));
    }

    /**
     * @covers \Ms\Core\Lib\Tools::multiplication
     */
    public function testMultiplication ()
    {
        $data = [1, 2, 3];
        $res = Tools::multiplication($data);
        $this->assertEquals(6, $res);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::normalizeUserID
     */
    public function testNormalizeUserID ()
    {
        $this->assertEquals(1, Tools::normalizeUserID());
        $this->assertEquals(123, Tools::normalizeUserID('123 User Name'));
    }

    /**
     * @covers \Ms\Core\Lib\Tools::roundEx
     */
    public function testRoundEx ()
    {
        $number = 123.98543;
        $res = Tools::roundEx($number, 2);
        $this->assertEquals(123.99, $res);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::sayRusRight
     */
    public function testSayRusRight ()
    {
        $this->assertEquals('365 дней', '365 ' . Tools::sayRusRight(365));
        $this->assertEquals('2 часа', '2 ' . Tools::sayRusRight(2, 'час', 'часа', 'часов'));
    }

    /**
     * @covers \Ms\Core\Lib\Tools::setFirstCharToBig
     */
    public function testSetFirstCharToBig ()
    {
        $res = Tools::setFirstCharToBig('приветик!');
        $this->assertEquals('Приветик!', $res);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::strReplace
     */
    public function testStrReplace ()
    {
        $message = 'this is "#FUNCTION_NAME#" message and #NAME#';
        $res = Tools::strReplace(['FUNCTION_NAME' => 'testStrReplace', 'NAME' => 'ТОТ'], $message);
        $this->assertEquals('this is "testStrReplace" message and ТОТ', $res);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::strToBool
     */
    public function testStrToBool ()
    {
        $this->assertTrue(Tools::strToBool('Y'));
        $this->assertFalse(Tools::strToBool('abrakadabra'));
    }

    /**
     * @covers \Ms\Core\Lib\Tools::transliterate
     */
    public function testTransliterate ()
    {
        $res = Tools::transliterate('Данный (тест) успешно пройден!');
        $this->assertEquals('Dannii__test__uspeshno_proiden_', $res);
    }

    /**
     * @covers \Ms\Core\Lib\Tools::validateBoolVal
     */
    public function testValidateBoolVal ()
    {
        $this->assertTrue(Tools::validateBoolVal('1'));
        $this->assertTrue(Tools::validateBoolVal('true'));
        $this->assertTrue(Tools::validateBoolVal('Y'));
        $this->assertTrue(Tools::validateBoolVal('asdad'));
        $this->assertTrue(Tools::validateBoolVal(1));
        $this->assertTrue(Tools::validateBoolVal(true));
        $this->assertFalse(Tools::validateBoolVal('0'));
        $this->assertFalse(Tools::validateBoolVal('false'));
        $this->assertFalse(Tools::validateBoolVal('N'));
        $this->assertFalse(Tools::validateBoolVal(0));
        $this->assertFalse(Tools::validateBoolVal(false));
    }

    /**
     * @covers \Ms\Core\Lib\Tools::validateDateVal
     */
    public function testValidateDateVal ()
    {
        $res1 = Tools::validateDateVal('1982-07-28');
        $res2 = Tools::validateDateVal('28.07.1982');
        $this->assertInstanceOf('\Ms\Core\Entity\Type\Date', $res1);
        $this->assertInstanceOf('\Ms\Core\Entity\Type\Date', $res2);
        try
        {
            $this->assertInstanceOf(
                '\Ms\Core\Entity\Type\Date',
                Tools::validateDateVal(new \Ms\Core\Entity\Type\Date())
            );
        }
        catch (\Exception $e)
        {
        }
        $this->assertEquals('1982-07-28', $res1->getDate());
        $this->assertEquals('1982-07-28', $res2->getDate());
        $this->assertFalse(Tools::validateDateVal('06.31.85'));
    }

    /**
     * @covers \Ms\Core\Lib\Tools::validateFloatVal
     */
    public function testValidateFloatVal ()
    {
        $this->assertEquals(1586.34, Tools::validateFloatVal('1 586,34 '));
    }

    /**
     * @covers \Ms\Core\Lib\Tools::validateIntVal
     */
    public function testValidateIntVal ()
    {
        $this->assertEquals(1, Tools::validateIntVal('1,64'));
    }

    /**
     * @covers \Ms\Core\Lib\Tools::validateStringVal
     */
    public function testValidateStringVal ()
    {
        $this->assertEquals('123&amp;nbsp;', Tools::validateStringVal(123 . '&nbsp;'));
    }

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance();
        $this->app->setSettings();
        $_SERVER['REQUEST_URI'] = '/index.php/test1/test2?login=yes&back_url_admin=';
        $_SERVER['SCRIPT_NAME'] = '/index.php/test1/test2';

        $this->app->setServer($_SERVER);
        $this->app->setUser();
        $user = $this->app->getUser();
        $user->setID(1);
    }
}

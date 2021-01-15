<?php
require_once(dirname(__FILE__) . '/../../../autoloader.php');

use \Ms\Core\Entity\Localization\Loc;
use \Ms\Core\Entity\System\Application;

class LocTest extends \PHPUnit\Framework\TestCase
{
    protected $app = null;

    protected function setUp ()
    {
        $this->app = Application::getInstance();
        $this->app->setSettings();
    }

    /**
     * @covers \Ms\Core\Entity\Localization\Loc::includeLocalizationFile
     */
    public function testIncludeLocalizationFile ()
    {
        $res = Loc::getInstance()
                  ->includeLocalizationFile(
                      dirname(__FILE__)
                      . '/../../../../loc/ru/tests/unit/Entity/Localization/LocTest.php'
                  )
        ;
        $this->assertTrue($res);
    }

    /**
     * @covers  \Ms\Core\Entity\Localization\Loc::includeLocalizationForThisFile
     *
     * @depends testIncludeLocalizationFile
     */
    public function testIncludeLocalizationForThisFile ()
    {
        $res = \Ms\Core\Entity\Localization\Loc::getInstance()->includeLocalizationForThisFile(__FILE__, '');
        $this->assertTrue($res);
    }

    /**
     * @covers \Ms\Core\Entity\Localization\Loc::showAllMessagesModule
     */
    public function testShowAllMessagesModule ()
    {
        \IncludeLangFile(__FILE__);
        $res = \Ms\Core\Entity\Localization\Loc::getInstance()->showAllMessagesModule('core','');
        $this->assertArrayHasKey('ms_core_message', $res, key($res));
        $this->assertArrayHasKey('ms_core_message_two', $res, key($res));
        $this->assertEquals('message',$res['ms_core_message']);
    }

    /**
     * @covers \Ms\Core\Entity\Localization\Loc::getModuleMessage
     */
    public function testGetModuleMessage ()
    {
        \IncludeLangFile(__FILE__);
        $res = \Ms\Core\Entity\Localization\Loc::getInstance()->getModuleMessage(
            'core',
            'message_two',
            ['TEXT'=>'Message']
        );
        $this->assertEquals('this is Message two', $res);
    }

    /**
     * @covers \Ms\Core\Entity\Localization\Loc::getMessage
     */
    public function testGetMessage ()
    {
        \IncludeLangFile(__FILE__);
        $res = Loc::getInstance()->getMessage('ms_core_message_two', ['TEXT'=>'GetMessage']);
        $this->assertEquals('this is GetMessage two', $res);
    }

    /**
     * @covers \Ms\Core\Entity\Localization\Loc::getCoreMessage
     */
    public function testGetCoreMessage ()
    {
        \IncludeLangFile(__FILE__);
        $res = Loc::getInstance()->getCoreMessage('message_two',['TEXT'=>'GetCoreMessage']);
        $this->assertEquals('this is GetCoreMessage two',$res);
    }

    /**
     * @covers \Ms\Core\Entity\Localization\Loc::getMessagesCollection
     */
    public function testGetMessagesCollection ()
    {
        $res = \Ms\Core\Entity\Localization\Loc::getInstance()->getMessagesCollection();
        $this->assertInstanceOf('\Ms\Core\Entity\Localization\MessagesCollection',$res);
    }

    /**
     * @covers \Ms\Core\Entity\Localization\Loc::getComponentMessage
     */
    public function testGetComponentMessage ()
    {
        \IncludeLangFile(__FILE__);
        $componentName = 'ms:core.menu';
        $res = Loc::getInstance()->getComponentMessage($componentName,'message',['COMPONENT_NAME'=>$componentName]);
        $this->assertEquals('this is component ms:core.menu message',$res);
    }
}
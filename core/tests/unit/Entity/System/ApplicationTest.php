<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\System\Application;

/**
 * Класс \ApplicationTest
 * Тесты класса \Ms\Core\Entity\System\Application
 */
class ApplicationTest extends \PHPUnit\Framework\TestCase
{
    /** @var Application */
    protected $ob = null;

    protected function setUp ()
    {
        $this->ob = Application::getInstance()
            ->setSettings()
            ->setApplicationParametersCollection()
        ;
        $this->ob->getSettings()->mergeLocalSettings();
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setSettings
     * @covers \Ms\Core\Entity\System\Application::getSettings
     */
    public function testSetSettings ()
    {
        $this->ob->setSettings();
        $this->assertInstanceOf(\Ms\Core\Entity\System\Settings::class,$this->ob->getSettings());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::getDocumentRoot
     */
    public function testGetDocumentRoot ()
    {
        $this->assertNotEmpty($this->ob->getDocumentRoot());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::convertPascalCaseToSnakeCase
     */
    public function testConvertPascalCaseToSnakeCase ()
    {
        $this->assertEquals('snake_case',$this->ob->convertPascalCaseToSnakeCase('SnakeCase'));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::convertSnakeCaseToPascalCase
     */
    public function testConvertSnakeCaseToPascalCase ()
    {
        $this->assertEquals('PascalCase',$this->ob->convertSnakeCaseToPascalCase('pascal_case'));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::getAppParam
     * @covers \Ms\Core\Entity\System\Application::setAppParams
     * @covers \Ms\Core\Entity\System\Application::unsetAppParam
     */
    public function testGetAppParam ()
    {
        $this->ob->setAppParams('tests_parameter','param_value');
        $this->assertEquals('param_value',$this->ob->getAppParam('tests_parameter'));
        $this->ob->unsetAppParam('tests_parameter');
        $this->assertTrue(is_null($this->ob->getAppParam('test')));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::getServer
     * @covers \Ms\Core\Entity\System\Application::setServer
     */
    public function testSetServer ()
    {
        $this->ob->setServer($_SERVER);
        $this->assertInstanceOf(\Ms\Core\Entity\System\Server::class,$this->ob->getServer());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setUser
     * @covers \Ms\Core\Entity\System\Application::getUser
     */
    public function testSetUser ()
    {
        $this->ob->setUser();
        $this->assertInstanceOf(\Ms\Core\Entity\User\User::class,$this->ob->getUser());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setApplicationParametersCollection
     */
    public function testSetApplicationParametersCollection ()
    {
        $this->ob->setAppParams('test','test');
        $this->ob->setApplicationParametersCollection();
        $this->assertTrue(is_null($this->ob->getAppParam('test')));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::getConnectionPool
     * @covers \Ms\Core\Entity\System\Application::setConnectionPool
     */
    public function testSetConnectionPool ()
    {
        $this->assertTrue(is_null($this->ob->getConnectionPool()));
        $this->ob->setConnectionPool();
        $this->assertInstanceOf(\Ms\Core\Entity\Db\ConnectionPool::class,$this->ob->getConnectionPool());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setConnectionDefault
     */
    public function testSetConnectionDefault ()
    {
        $this->ob->setConnectionPool();
        $this->ob->setConnectionDefault();
        $this->assertTrue($this->ob->getConnectionPool()->getConnection('default')->isSuccess());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setTimes
     * @covers \Ms\Core\Entity\System\Application::getTimes
     */
    public function testSetTimes ()
    {
        $time = time();
        $this->ob->setTimes('test_time',$time);
        $this->assertEquals($time, $this->ob->getTimes('test_time'));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setState
     * @covers \Ms\Core\Entity\System\Application::getState
     */
    public function testSetState ()
    {
        $this->ob->setState('TEST_STATE');
        $this->assertEquals('TEST_STATE',$this->ob->getState());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::isUtfMode
     */
    public function testIsUtfMode ()
    {
        $this->assertTrue(Application::isUtfMode());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::addBufferContent
     */
    public function testAddBufferContent ()
    {
        $this->ob->addBufferContent('test_view','test_content',false);
        $this->ob->addBufferContent('test_view','test_content2');
        $this->assertEquals(
            'test_contenttest_content2',
            \Ms\Core\Entity\System\Buffer::getInstance()->getContent('test_view')
        );
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::getSitePath
     */
    public function testGetSitePath ()
    {
        $docRoot = $this->ob->getDocumentRoot();
        $this->assertEquals(str_replace($docRoot,'',__FILE__),$this->ob->getSitePath(__FILE__));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::addCSS
     */
    public function testAddCSS ()
    {
        $this->ob->addCSS(__FILE__);
        $this->assertTrue(!is_null(\Ms\Core\Entity\System\Buffer::getInstance()->getContent('head_css')));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::addJS
     */
    public function testAddJS ()
    {
        $this->ob->addJS(__FILE__);
        $this->assertTrue(!is_null(\Ms\Core\Entity\System\Buffer::getInstance()->getContent('head_js')));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::addJsToDownPage
     */
    public function testAddJsToDownPage ()
    {
        $this->ob->addJsToDownPage('console.log("test_script");');
        $this->assertContains('console.log("test_script");',\Ms\Core\Entity\System\Buffer::getInstance()->getContent('down_js'));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::cleanBufferContent
     */
    public function testCleanBufferContent ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\System\Application::cleanBufferContent');
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::convertCharset
     */
    public function testConvertCharset ()
    {
        $string = 'Тестовое сообщение';
        $this->assertEquals(
            iconv ('utf-8','windows-1251', $string),
            $this->ob->convertCharset($string,'utf-8','windows-1251')
        );
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::endBufferContent
     */
    public function testEndBufferContent ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\System\Application::endBufferContent');
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::endBufferPage
     */
    public function testEndBufferPage ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\System\Application::endBufferPage');
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setBreadcrumbs
     * @covers \Ms\Core\Entity\System\Application::getBreadcrumbs
     */
    public function testGetBreadcrumbs ()
    {
        $this->ob->setBreadcrumbs();
        $this->assertInstanceOf(\Ms\Core\Entity\System\Breadcrumbs::class,$this->ob->getBreadcrumbs());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::getSession
     * @covers \Ms\Core\Entity\System\Application::setSession
     */
    public function testSetSession ()
    {
        $this->ob->setSession();
        $this->assertInstanceOf(\Ms\Core\Entity\System\Session::class,$this->ob->getSession());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setCookieController
     * @covers \Ms\Core\Entity\System\Application::getCookieController
     */
    public function testSetCookieController ()
    {
        $this->ob->setCookieController();
        $this->assertInstanceOf(\Ms\Core\Entity\System\CookieController::class, $this->ob->getCookieController());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setUserGroupsCollection
     */
    public function testSetUserGroupsCollection ()
    {
        $this->assertInstanceOf(Application::class,$this->ob->setUserGroupsCollection());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setAuthorizer
     * @covers \Ms\Core\Entity\System\Application::getAuthorizer
     */
    public function testSetAuthorizer ()
    {
        $this->ob
            ->setUser()
            ->setCookieController()
            ->setAuthorizer()
        ;
        $this->assertInstanceOf(\Ms\Core\Entity\User\Authorizer::class,$this->ob->getAuthorizer());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::init
     */
    public function testInit ()
    {
        $this->ob->init();
        $this->assertInstanceOf(\Ms\Core\Entity\System\Server::class,$this->ob->getServer());
        $this->assertInstanceOf(\Ms\Core\Entity\System\Settings::class, $this->ob->getSettings());
        $this->assertInstanceOf(\Ms\Core\Entity\Db\ConnectionPool::class, $this->ob->getConnectionPool());
        $this->assertInstanceOf(\Ms\Core\Entity\System\Breadcrumbs::class, $this->ob->getBreadcrumbs());
        $this->assertInstanceOf(\Ms\Core\Entity\System\Session::class,$this->ob->getSession());
        $this->assertInstanceOf(\Ms\Core\Entity\System\CookieController::class, $this->ob->getCookieController());
        $this->assertInstanceOf(\Ms\Core\Entity\User\User::class, $this->ob->getUser());
        $this->assertInstanceOf(\Ms\Core\Entity\User\Authorizer::class, $this->ob->getAuthorizer());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::getNearestFile
     */
    public function testGetNearestFile ()
    {
        $this->markTestIncomplete('Не реализован: \Ms\Core\Entity\System\Application::getNearestFile');
/*        $_SERVER['DOCUMENT_ROOT'] = '/var/www/html';
        $_SERVER['REQUEST_URI'] = __FILE__;
        $this->ob->setServer($_SERVER);
        print_r($_SERVER);
        $this->assertEquals(
            '',
            $this->ob->getNearestFile('autoloader.php')
        );*/
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::getRequestUrl
     */
    public function testGetRequestUrl ()
    {
        $this->ob->setServer($_SERVER);
        $this->assertEquals($_SERVER['REQUEST_URI'],$this->ob->getRequestUrl());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::getSiteTemplate
     * @covers \Ms\Core\Entity\System\Application::setSiteTemplate
     */
    public function testGetSiteTemplate ()
    {
        $this->ob->setServer($_SERVER);
        $this->ob->setSiteTemplate('test_template');
        $this->assertEquals('test_template',$this->ob->getSiteTemplate());
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::includeComponent
     */
    public function testIncludeComponent ()
    {
        try
        {
            $comp = $this->ob->includeComponent('ms.core:test.component');
            if (!is_string($comp))
            {
                $this->assertInstanceOf(\Ms\Core\Entity\Components\Component::class,$comp);
            }
            else
            {
                $this->assertTrue(true);
            }
        }
        catch (Exception $e)
        {
            $this->assertTrue(true);
        }
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::includePlugin
     */
    public function testIncludePlugin ()
    {
        $this->assertFalse($this->ob->includePlugin('NotExistsPlugin'));
        // $this->assertTrue($this->ob->includePlugin('jquery'));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setRefresh
     */
    public function testSetRefresh ()
    {
        $this->markTestSkipped('Нет возможности проверить: \Ms\Core\Entity\System\Application::setRefresh');
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::setTitle
     */
    public function testSetTitle ()
    {
        $this->ob->setTitle('Test Title');
        $this->assertContains('Test Title',\Ms\Core\Entity\System\Buffer::getInstance()->getContent('page_title'));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::showBufferContent
     */
    public function testShowBufferContent ()
    {
        $this->markTestSkipped('Нет возможности проверить: \Ms\Core\Entity\System\Application::showBufferContent');
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::showDownJs
     */
    public function testShowDownJs ()
    {
        $this->markTestSkipped('Нет возможности проверить: \Ms\Core\Entity\System\Application::showDownJs');
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::showMeta
     */
    public function testShowMeta ()
    {
        $this->markTestSkipped('Нет возможности проверить: \Ms\Core\Entity\System\Application::showMeta');
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::showTitle
     */
    public function testShowTitle ()
    {
        $this->markTestSkipped('Нет возможности проверить: \Ms\Core\Entity\System\Application::showTitle');
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::startBufferContent
     */
    public function testStartBufferContent ()
    {
        $this->markTestSkipped('Нет возможности проверить: \Ms\Core\Entity\System\Application::startBufferContent');
        // $this->ob->startBufferContent('test');
        // $this->assertTrue(!is_null(\Ms\Core\Entity\System\Buffer::getInstance()->getContent('test')));
    }

    /**
     * @covers \Ms\Core\Entity\System\Application::startBufferPage
     */
    public function testStartBufferPage ()
    {
        $this->markTestSkipped('Нет возможности проверить: \Ms\Core\Entity\System\Application::startBufferPage');
    }
}
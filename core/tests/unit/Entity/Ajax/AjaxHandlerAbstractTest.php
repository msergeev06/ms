<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use PHPUnit\Framework\TestCase;

class AjaxHandler extends \Ms\Core\Entity\Ajax\AjaxHandlerAbstract
{
    public function method1 ($subject)
    {
        return $subject;
    }

    protected function method2 ()
    {

    }

    private function method3 ()
    {

    }

    public function method5 ($text)
    {
        throw new Exception("Some exception: ".$text);
    }
}

class AjaxHandlerAbstractTest extends TestCase
{
    /** @var AjaxHandler */
    protected $handler = null;

    protected function setUp ()
    {
        $this->handler = AjaxHandler::getInstance();
    }

    protected function tearDown ()
    {
        unset($this->handler);
    }

    public function providerActionSuccess ()
    {
        return [
            "method exists" => ['method1']
        ];
    }

    public function providerActionFail ()
    {
        return [
            "protected method" => ['method2'],
            "private method" => ['method3']
        ];
    }

    public function providerActionNotFound ()
    {
        return [
            "method not found" => ['method4'],
            "empty action" => ['']
        ];
    }

    public function providerRequestFail ()
    {
        return [
            "empty action" => [['action' => '']],
            "method not found" => [['action' => 'method4']]
        ];
    }

    /**
     * @covers \Ms\Core\Entity\Ajax\AjaxHandlerAbstract::getInstance
     */
    public function testGetInstance ()
    {
        $this->assertInstanceOf('\Ms\Core\Entity\Ajax\AjaxHandlerAbstract',$this->handler);
    }

    /**
     * @dataProvider providerActionSuccess
     * @covers \Ms\Core\Entity\Ajax\AjaxHandlerAbstract::methodExists
     */
    public function testMethodExists ($action)
    {
        $this->assertTrue($this->handler->methodExists($action));
    }

    /**
     * @dataProvider providerActionFail
     * @covers \Ms\Core\Entity\Ajax\AjaxHandlerAbstract::methodExists
     */
    public function testMethodExistsNotPublic ($action)
    {
        $this->assertFalse($this->handler->methodExists($action));
    }

    /**
     * @dataProvider providerActionNotFound
     * @covers \Ms\Core\Entity\Ajax\AjaxHandlerAbstract::methodExists
     */
    public function testMethodExistsNotFound ($action)
    {
        $this->assertFalse($this->handler->methodExists($action));
    }

    /**
     * @dataProvider providerRequestFail
     * @covers \Ms\Core\Entity\Ajax\AjaxHandlerAbstract::processRequest
     */
    public function testProcessRequestError ($arPost)
    {
        $result = $this->handler->processRequest($arPost);
        $this->assertArrayHasKey('success',$result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('data',$result);
        $this->assertArrayHasKey('message',$result['data']);
    }

    /**
     * @covers \Ms\Core\Entity\Ajax\AjaxHandlerAbstract::processRequest
     */
    public function testProcessRequestErrorThrowSomeException ()
    {
        $arPost = [
            'action' => 'method5',
            'data' => 'some data'
        ];
        $result = $this->handler->processRequest($arPost);
        $this->assertArrayHasKey('success',$result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('data',$result);
        $this->assertArrayHasKey('message',$result['data']);
        $this->assertEquals('Some exception: some data', $result['data']['message']);
    }

    /**
     * @covers \Ms\Core\Entity\Ajax\AjaxHandlerAbstract::processRequest
     */
    public function testProcessRequestSuccess ()
    {
        $arPost = [
            'action' => 'method1',
            'data' => 'Hello'
        ];
        $result = $this->handler->processRequest($arPost);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('Hello',$result['data']);
    }
}
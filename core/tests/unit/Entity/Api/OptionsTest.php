<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use Ms\Core\Api\Options;

class OptionsTest extends \PHPUnit\Framework\TestCase
{
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance();
        $this->app
            ->setSettings()
            ->setConnectionPool()
            ->setApplicationParametersCollection()
        ;
        $this->app->getSettings()->mergeLocalSettings();
        $this->app->getConnectionPool()->getConnection()->connect('localhost','dobro','root','hKjpTg3VCg');
    }

	public function testGetOptionString ()
	{
		$res = Options::getInstance()->getOptionString ('core', 'test', 'string123');
		$this->assertEquals ('string123', $res);
		$this->assertTrue (is_string($res));
	}

	public function testGetOptionInt ()
	{
		$res = Options::getInstance()->getOptionInt ('core', 'test', 123);
		$this->assertEquals (123, $res);
		$this->assertTrue (is_int($res));
	}

	public function testGetOptionFloat ()
	{
		$res = Options::getInstance()->getOptionFloat ('core', 'test', 123.456);
		$this->assertEquals (123.456, $res);
		$this->assertTrue (is_float($res));
	}

	public function testGetOptionBool ()
	{
		$res = Options::getInstance()->getOptionBool ('core', 'test2', true);
		$this->assertTrue($res);
		$this->assertTrue (is_bool($res));
	}

	public function testGetOptionFullName ()
	{
		$res = Options::getInstance()->getOptionFullName ('core', 'test');
		$this->assertEquals ('ms.core:test', $res);
	}
}


<?php
require_once (dirname(__FILE__).'/../../autoloader.php');

use PHPUnit\Framework\TestCase;
use \Ms\Core\Lib\Errors;

class ErrorsTest extends TestCase
{
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance();
        $this->app->setSettings();
    }

    /**
     * @covers \Ms\Core\Lib\Errors::getError
     */
	public function testGetError ()
	{
		$res = Errors::getError (Errors::ERROR_CLASS_WRONG_NAME,['CLASS_NAME'=>'Ms\Core\Lib\Error']);
		$this->assertEquals ('[110] Неверное имя класса "Ms\Core\Lib\Error"',$res);
	}

    /**
     * @covers \Ms\Core\Lib\Errors::getErrorTextByCode
     */
	public function testGetErrorTextByCode ()
	{
		$res = Errors::getErrorTextByCode (Errors::ERROR_MODULE_NAME_TO_LONG, ['MAX_LENGTH'=>100]);
		$this->assertEquals ('Имя модуля слишком длинное. Допустимая длина 100 символов',$res);
	}

}

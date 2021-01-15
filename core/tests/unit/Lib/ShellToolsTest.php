<?php
require_once (dirname(__FILE__).'/../../autoloader.php');

use PHPUnit\Framework\TestCase;
use \Ms\Core\Lib\ShellTools;

class ShellToolsTest extends TestCase
{
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance();
        $this->app->setSettings();
    }

    /**
     * @covers \Ms\Core\Lib\ShellTools::getColoredString
     */
	public function testGetColoredString ()
	{
		$res = ShellTools::getColoredString (
		    'Error: Red text on light gray background',
            ShellTools::COLOR_TEXT_RED,
            ShellTools::COLOR_BACKGROUND_LIGHT_GRAY
        );
		$this->assertEquals ("\033[0;31m\033[47mError: Red text on light gray background\033[0m", $res);
	}

    /**
     * @covers \Ms\Core\Lib\ShellTools::getTextColors
     */
	public function testGetTextColors ()
	{
		$res = ShellTools::getTextColors ();
		$this->assertTrue (in_array('cyan',$res));
	}

    /**
     * @covers \Ms\Core\Lib\ShellTools::getBackgroundColors
     */
	public function testGetBackgroundColors ()
	{
		$res = ShellTools::getBackgroundColors ();
		$this->assertTrue (in_array('magenta',$res));
	}

}


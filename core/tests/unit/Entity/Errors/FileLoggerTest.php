<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Errors\FileLogger;
use Ms\Core\Entity\System\Application;

/**
 * Класс \FileLoggerTest
 * Тесты класса
 */
class FileLoggerTest extends \PHPUnit\Framework\TestCase
{
    /** @var FileLogger */
    protected $logger = null;
    /** @var Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = Application::getInstance()
            ->setSettings()
        ;
        $this->app->getSettings()->mergeLocalSettings();

        $this->logger = new FileLogger('core',FileLogger::TYPE_ERROR);
    }

    /**
     * @covers \Ms\Core\Entity\Errors\FileLogger::addMessage
     */
    public function testAddMessage ()
    {
        $this->logger
            ->setLogsDir(__DIR__)
            ->setTypeDebug()
            ->setPeriodDaily()
        ;
        $strDate = date('d.m.Y H:i');
        $message = 'This is test message in '.$strDate;
        $this->logger->addMessage('This is test message in #DATE_TIME#',['DATE_TIME'=>$strDate]);
        $path = __DIR__ . '/'.$this->logger->getPeriod().'/'.$this->logger->getLogFileName();
        if (!file_exists($path))
        {
            $this->assertTrue(false,'Log file not exists in '.$path);
        }
        else
        {
            $fileData = file_get_contents($path);
            $this->assertContains($message,$fileData);
            unlink($path);
        }
    }

    /**
     * @covers \Ms\Core\Entity\Errors\FileLogger::addMessageOtherType
     */
    public function testAddMessageOtherType ()
    {
        $this->logger
            ->setLogsDir(__DIR__)
            ->setTypeDebug()
            ->setPeriodDaily()
        ;
        $strDate = date('d.m.Y H:i');
        $message = 'This is test message in '.$strDate;
        $this->logger->addMessageOtherType(FileLogger::TYPE_ERROR,'This is test message in #DATE_TIME#',['DATE_TIME'=>$strDate]);
        $filename = str_replace(FileLogger::TYPE_DEBUG,FileLogger::TYPE_ERROR, $this->logger->getLogFileName());

        $path = __DIR__ . '/' . $this->logger->getPeriod() . '/' . $filename;
        if (!file_exists($path))
        {
            $this->assertTrue(false,'Log file not exists in '.$path);
        }
        else
        {
            $fileData = file_get_contents($path);
            $this->assertContains($message,$fileData);
            unlink($path);
        }
   }

    /**
     * @covers \Ms\Core\Entity\Errors\FileLogger::setLogFileName
     * @covers \Ms\Core\Entity\Errors\FileLogger::getLogFileName
     */
    public function testGetLogFileName ()
    {
        $this->logger->setLogFileName('test');
        $this->assertEquals('test_error_'.date('Y-m-d').'.txt',$this->logger->getLogFileName());
    }

    /**
     * @covers \Ms\Core\Entity\Errors\FileLogger::setLogsDir
     * @covers \Ms\Core\Entity\Errors\FileLogger::getLogsDir
     */
    public function testGetLogsDir ()
    {
        $this->logger->setLogsDir(__DIR__);
        $this->assertEquals(__DIR__,$this->logger->getLogsDir());
    }

    /**
     * @covers \Ms\Core\Entity\Errors\FileLogger::setModule
     * @covers \Ms\Core\Entity\Errors\FileLogger::getModule
     */
    public function testGetModule ()
    {
        try
        {
            $this->logger->setModule('ms.core');
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentException $e)
        {
            $this->assertTrue(false,$e->getMessage());
        }
        $this->assertEquals('ms.core',$this->logger->getModule());
    }

    /**
     * @covers \Ms\Core\Entity\Errors\FileLogger::setPeriod
     * @covers \Ms\Core\Entity\Errors\FileLogger::getPeriod
     * @covers \Ms\Core\Entity\Errors\FileLogger::setPeriodDaily
     * @covers \Ms\Core\Entity\Errors\FileLogger::setPeriodMonthly
     */
    public function testGetPeriod ()
    {
        try
        {
            $this->logger->setPeriod('asd');
            $this->assertTrue(false,'Не сработало исключение. Неверный период "asd"');
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException $e)
        {
            $this->assertTrue(true);
        }
        try
        {
            $this->logger->setPeriod(FileLogger::PERIOD_DAILY);
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException $e)
        {
            $this->assertTrue(false, $e->getMessage());
            return;
        }
        $this->assertEquals(FileLogger::PERIOD_DAILY,$this->logger->getPeriod());
        $this->logger->setPeriodDaily();
        $this->assertEquals(FileLogger::PERIOD_DAILY,$this->logger->getPeriod());
        $this->logger->setPeriodMonthly();
        $this->assertEquals(FileLogger::PERIOD_MONTHLY,$this->logger->getPeriod());
    }

    /**
     * @covers \Ms\Core\Entity\Errors\FileLogger::setType
     * @covers \Ms\Core\Entity\Errors\FileLogger::getType
     * @covers \Ms\Core\Entity\Errors\FileLogger::setTypeDebug
     * @covers \Ms\Core\Entity\Errors\FileLogger::setTypeError
     * @covers \Ms\Core\Entity\Errors\FileLogger::setTypeNotice
     */
    public function testGetType ()
    {
        try
        {
            $this->logger->setType('sdf');
            $this->assertTrue(false, 'Не сработало исключение. Неверный тип "sdf"');
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException $e)
        {
            $this->assertTrue(true);
        }
        try
        {
            $this->logger->setType(FileLogger::TYPE_DEBUG);
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException $e)
        {
            $this->assertTrue(false, $e->getMessage());
            return;
        }
        $this->assertEquals(FileLogger::TYPE_DEBUG,$this->logger->getType());
        $this->logger->setTypeDebug();
        $this->assertEquals(FileLogger::TYPE_DEBUG,$this->logger->getType());
        $this->logger->setTypeError();
        $this->assertEquals(FileLogger::TYPE_ERROR,$this->logger->getType());
        $this->logger->setTypeNotice();
        $this->assertEquals(FileLogger::TYPE_NOTICE,$this->logger->getType());
    }
}

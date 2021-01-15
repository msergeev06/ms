<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Events\EventController;

/**
 * Класс \EventControllerTest
 * Тесты класса \Ms\Core\Entity\Events\EventController
 */
class EventControllerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Ms\Core\Entity\System\Application */
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance()
            ->setSettings()
            ->setConnectionPool()
            ->setApplicationParametersCollection()
        ;
        $this->app->getSettings()->mergeLocalSettings();
        $this->app->setConnectionDefault();



    }

    /**
     * @covers \Ms\Core\Entity\Events\EventController::getHandlersCollection
     */
    public function testGetHandlersCollection ()
    {
        $this->assertInstanceOf(
            \Ms\Core\Entity\Events\EventHandlersCollection::class,
            EventController::getInstance()->getHandlersCollection()
        );
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventController::setLogger
     * @covers \Ms\Core\Entity\Events\EventController::getLogger
     */
    public function testSetLogger ()
    {
        $logger = new \Ms\Core\Entity\Errors\FileLogger(
            'core',
            \Ms\Core\Entity\Errors\FileLogger::TYPE_DEBUG
        );
        EventController::getInstance()->setLogger($logger);
        $this->assertEquals(
            \Ms\Core\Entity\Errors\FileLogger::TYPE_DEBUG,
            EventController::getInstance()->getLogger()->getType()
        );
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventController::getModuleEventsInfo
     */
    public function testGetModuleEventsInfo ()
    {
        $this->assertInstanceOf(\Ms\Core\Entity\Events\Info\Collection::class,EventController::getModuleEventsInfo());
    }
}
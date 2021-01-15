<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Events\EventRegistrar;

/**
 * Класс \EventRegistrarTest
 * Тесты класса \Ms\Core\Entity\Events\EventRegistrar
 */
class EventRegistrarTest extends \PHPUnit\Framework\TestCase
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
     * @covers \Ms\Core\Entity\Events\EventRegistrar::addModuleEventHandler
     */
    public function testAddModuleEventHandler ()
    {
        $this->markTestSkipped('No test \Ms\Core\Entity\Events\EventRegistrar::addModuleEventHandler');
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventRegistrar::deleteModuleEventHandler
     */
    public function testDeleteModuleEventHandler ()
    {
        $this->markTestSkipped('No test \Ms\Core\Entity\Events\EventRegistrar::deleteModuleEventHandler');
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventRegistrar::getEventHandlersCollection
     */
    public function testGetEventHandlersCollection ()
    {
        $this->assertInstanceOf(
            \Ms\Core\Entity\Events\EventHandlersCollection::class,
            EventRegistrar::getInstance()->getEventHandlersCollection()
        );
    }

    /**
     * @covers \Ms\Core\Entity\Events\EventRegistrar::getLogger
     * @covers \Ms\Core\Entity\Events\EventRegistrar::setLogger
     */
    public function testGetLogger ()
    {
        EventRegistrar::getInstance()->setLogger(
            new \Ms\Core\Entity\Errors\FileLogger('core',\Ms\Core\Entity\Errors\FileLogger::TYPE_DEBUG)
        );
        $logger = EventRegistrar::getInstance()->getLogger();
        $this->assertEquals(\Ms\Core\Entity\Errors\FileLogger::TYPE_DEBUG,$logger->getType());
    }
}
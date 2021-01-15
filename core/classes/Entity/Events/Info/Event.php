<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Events\Info;

use Ms\Core\Entity\Events\Info\Parameters;

/**
 * Класс Ms\Core\Entity\Events\Info\Event
 * Описывает вызываемое модулем событие
 */
class Event
{
    /** @var string */
    protected $eventID = null;
    /** @var null|string */
    protected $moduleName = null;
    /** @var null|string */
    protected $description = null;
    /** @var Parameters\Collection */
    protected $parameters = null;
    /** @var bool  */
    protected $stopped = false;

    public function __construct (string $eventID)
    {
        $this->setEventID($eventID);
        $this->parameters = new Parameters\Collection();
    }

    public function isStopped ()
    {
        return $this->stopped;
    }

    public function setStopped (bool $isStopped = true)
    {
        $this->stopped = $isStopped;

        return $this;
    }

    public function getEventID ()
    {
        return $this->eventID;
    }

    public function setEventID (string $eventID)
    {
        $this->eventID = $eventID;

        return $this;
    }

    public function getModuleName ()
    {
        return $this->moduleName;
    }

    public function setModuleName (string $moduleName)
    {
        $this->moduleName = strtolower($moduleName);
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription (string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function getParametersCollection ()
    {
        return $this->parameters;
    }

    public function addParameter (Parameters\Parameter $parameter)
    {
        $this->parameters->addParameter($parameter);

        return $this;
    }
}
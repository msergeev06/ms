<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Events\Info;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Events\EventInfoCollection
 * Коллекция объектов, описывающих события, генерируемые модулем
 */
class Collection extends Dictionary
{
    /** @var null|string */
    protected $moduleName = null;

    public function getModuleName ()
    {
        return $this->moduleName;
    }

    public function setModuleName (string $moduleName)
    {
        $moduleName = strtolower($moduleName);

        $this->moduleName = $moduleName;

        return $this;
    }

    public function addEventInfo (Event $info)
    {
        if (is_null($info->getModuleName()) && !is_null($this->moduleName))
        {
            $info->setModuleName($this->moduleName);
        }
        $this->offsetSet($info->getEventID(),$info);

        return $this;
    }

    public function isset(string $eventID)
    {
        return $this->offsetExists($eventID);
    }

    public function getEventInfo (string $eventID)
    {
        if (!$this->isset($eventID))
        {
            return null;
        }

        return $this->offsetGet($eventID);
    }
}
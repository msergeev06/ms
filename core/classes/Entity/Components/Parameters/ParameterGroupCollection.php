<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core\Entity\Components\Parameters
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Components\Parameters;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Components\Parameters\ParameterGroupCollection
 * Коллекция групп с параметрами компонента
 */
class ParameterGroupCollection extends Dictionary
{
    public function addGroup (ParameterGroup $group)
    {
        $this->offsetSet($group->getCode(),$group);

        return $this;
    }

    public function getGroup (string $groupCode)
    {
        if (!$this->isset(strtoupper($groupCode)))
        {
            return null;
        }

        return $this->offsetGet(strtoupper($groupCode));
    }

    public function isset(string $groupCode)
    {
        return $this->offsetExists(strtoupper($groupCode));
    }
}
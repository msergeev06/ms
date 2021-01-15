<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Events\Info\Parameters;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Events\Info\Parameters\Collection
 * Коллекция параметров, передаваемых в обработчик события
 */
class Collection extends Dictionary
{
    public function addParameter (Parameter $parameter)
    {
        $this->offsetSet($parameter->getName(),$parameter);

        return $this;
    }

    public function getParameter (string $name)
    {
        if (!$this->isset($name))
        {
            return null;
        }

        return $this->offsetGet($name);
    }

    public function isset(string $name)
    {
        return $this->offsetExists($name);
    }
}
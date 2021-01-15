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
 * Класс Ms\Core\Entity\Components\Parameters\ParameterCollection
 * Коллекция параметров компонента
 */
class ParameterCollection extends Dictionary
{
    public function addParameter (Parameter $parameter)
    {
        $this->offsetSet($parameter->getCode(), $parameter);

        return $this;
    }

    public function isset(string $parameterCode)
    {
        $parameterCode = strtoupper($parameterCode);

        return $this->offsetExists($parameterCode);
    }

    public function getParameter (string $parameterCode)
    {
        $parameterCode = strtoupper($parameterCode);
        if (!$this->isset($parameterCode))
        {
            return null;
        }

        return $this->offsetGet($parameterCode);
    }
}
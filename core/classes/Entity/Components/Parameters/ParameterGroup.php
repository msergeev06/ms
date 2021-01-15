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
 * Класс Ms\Core\Entity\Components\Parameters\ParameterGroup
 * Группа параметров компонента
 */
class ParameterGroup extends Dictionary
{
    protected $code = null;
    protected $name = null;

    public function __construct (string $code = 'GENERAL')
    {
        parent::__construct(null);
        $this->setCode($code);
    }

    public function getName ()
    {
        return $this->name;
    }

    public function setName (string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getCode ()
    {
        return $this->code;
    }

    public function setCode (string $code)
    {
        $this->code = strtoupper($code);

        return $this;
    }

    public function addParameter (Parameter $parameter)
    {
        $this->offsetSet($parameter->getCode(),$parameter);

        return $this;
    }

    /**
     * Возвращает объект указанного параметра
     *
     * @param string $parameterCode
     *
     * @return \Ms\Core\Entity\Components\Parameters\Parameter|null
     */
    public function getParameter (string $parameterCode)
    {
        if (!$this->isset(strtoupper($parameterCode)))
        {
            return null;
        }

        return $this->offsetGet(strtoupper($parameterCode));
    }

    public function isset(string $parameterCode)
    {
        return $this->offsetExists(strtoupper($parameterCode));
    }
}
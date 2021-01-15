<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Events\Info\Parameters;

use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;

/**
 * Класс Ms\Core\Entity\Events\Info\Parameters\BoolParameter
 * Тип параметра bool
 */
class BoolParameter extends Parameter
{
    public function __construct (string $name)
    {
        parent::__construct($name);
        try
        {
            $this->setType(Parameter::TYPE_BOOL);
        }
        catch (ArgumentOutOfRangeException $e)
        {
        }
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core\Entity\Components\Parameters
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Components\Parameters;


/**
 * Класс Ms\Core\Entity\Components\Parameters\CheckboxParameter
 * Параметр компонента типа CHECKBOX
 */
class CheckboxParameter extends Parameter
{
    public function __construct (string $code)
    {
        parent::__construct($code);
        try
        {
            $this->setType('CHECKBOX');
        }
        catch (\Exception $e)
        {
        }
    }

    public function getValue ()
    {
        return (bool)parent::getValue();
    }

}
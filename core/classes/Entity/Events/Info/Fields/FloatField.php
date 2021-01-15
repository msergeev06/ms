<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Events\Info\Fields;

use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;

/**
 * Класс Ms\Core\Entity\Events\Info\Fields\FloatField
 * Поле типа float
 */
class FloatField extends Field
{
    public function __construct (string $name)
    {
        parent::__construct($name);
        try
        {
            $this->setType(Field::TYPE_FLOAT);
        }
        catch (ArgumentOutOfRangeException $e)
        {
        }
    }
}
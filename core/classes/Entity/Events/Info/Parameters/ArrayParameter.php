<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Events\Info\Parameters;

use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;
use Ms\Core\Entity\Events\Info\Fields;

/**
 * Класс Ms\Core\Entity\Events\Info\Parameters\ArrayParameter
 * Тип параметра array
 */
class ArrayParameter extends Parameter
{
    /** @var Fields\Collection */
    protected $fields = null;

    public function __construct (string $name)
    {
        parent::__construct($name);
        try
        {
            $this->setType(Parameter::TYPE_ARRAY);
        }
        catch (ArgumentOutOfRangeException $e)
        {
        }
        $this->fields = new Fields\Collection();
    }

    public function getFieldsCollection ()
    {
        return $this->fields;
    }

    public function addField (Fields\Field $field)
    {
        $this->fields->addField($field);

        return $this;
    }
}
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
 * Класс Ms\Core\Entity\Events\Info\Fields\ArrayField
 * Поле типа array
 */
class ArrayField extends Field
{
    /** @var Collection */
    protected $fields = null;

    public function __construct (string $name)
    {
        parent::__construct($name);
        try
        {
            $this->setType(Field::TYPE_ARRAY);
        }
        catch (ArgumentOutOfRangeException $e)
        {
        }
        $this->fields = new Collection();
    }

    public function getFieldsCollection ()
    {
        return $this->fields;
    }

    public function addField (Field $field)
    {
        $this->fields->addField($field);

        return $this;
    }
}
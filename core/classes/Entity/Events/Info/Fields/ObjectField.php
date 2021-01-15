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
 * Класс Ms\Core\Entity\Events\Info\Fields\ObjectField
 * Поле типа Object
 */
class ObjectField extends Field
{
    /** @var null|string */
    protected $className = null;

    public function __construct (string $name)
    {
        parent::__construct($name);
        try
        {
            $this->setType(Field::TYPE_OBJECT);
        }
        catch (ArgumentOutOfRangeException $e)
        {
        }
    }

    public function getClassName ()
    {
        return $this->className;
    }

    public function setClassName (string $className)
    {
        $this->className = $className;

        return $this;
    }
}
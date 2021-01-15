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
 * Класс Ms\Core\Entity\Events\Info\Fields\FieldAbstract
 * Описывает поле массива, передаваемого в качестве параметра в обработчик события
 */
class Field
{
    const TYPE_ARRAY = 'array';
    const TYPE_STRING = 'string';
    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'float';
    const TYPE_DATE = 'date';
    const TYPE_BOOL = 'bool';
    const TYPE_OBJECT = 'object';

    const TYPES_LIST = [
        self::TYPE_ARRAY,
        self::TYPE_STRING,
        self::TYPE_INT,
        self::TYPE_FLOAT,
        self::TYPE_DATE,
        self::TYPE_BOOL,
        self::TYPE_OBJECT
    ];

    /** @var string */
    protected $name = null;
    /** @var null|string */
    protected $title = null;
    /** @var null|string */
    protected $type = null;
    /** @var bool */
    protected $required = false;
    /** @var null|string */
    protected $description = null;

    public function __construct (string $name)
    {
        $this->setName($name);
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

    public function getTitle ()
    {
        return $this->title;
    }

    public function setTitle (string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function getType ()
    {
        return $this->type;
    }

    public function setType (string $type)
    {
        $type = strtolower($type);
        if (!in_array($type,self::TYPES_LIST))
        {
            throw new ArgumentOutOfRangeException('type',self::TYPES_LIST);
        }

        $this->type = $type;

        return $this;
    }

    public function isRequired ()
    {
        return $this->required;
    }

    public function setRequired (bool $required = true)
    {
        $this->required = $required;

        return $this;
    }

    public function getDescription ()
    {
        return $this->description;
    }

    public function setDescription (string $description)
    {
        $this->description = $description;

        return $this;
    }

}
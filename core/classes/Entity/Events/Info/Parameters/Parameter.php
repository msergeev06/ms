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
 * Класс Ms\Core\Entity\Events\Info\Parameters\Parameter
 * Описывает передаваемый параметр в обработчик события модуля
 */
class Parameter
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
    /** @var bool  */
    protected $modified = false;
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
        $lowerType = strtolower($type);
        if (!in_array($lowerType,self::TYPES_LIST))
        {
            if (!class_exists($type))
            {
                throw new ArgumentOutOfRangeException('type',self::TYPES_LIST);
            }
            else
            {
                $this->type = $type;
            }
        }
        else
        {
            $this->type = $lowerType;
        }

        return $this;
    }

    public function isModified ()
    {
        return $this->modified;
    }

    public function setModified (bool $modified = true)
    {
        $this->modified = $modified;

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
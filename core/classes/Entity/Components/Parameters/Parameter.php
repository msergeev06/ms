<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core\Entity\Components\Parameters
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Components\Parameters;

use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;

/**
 * Класс Ms\Core\Entity\Components\Parameters\Parameter
 * Описывает пераметр компонента
 */
class Parameter
{
    const TYPE_STRING = 'STRING';
    const TYPE_LIST = 'LIST';
    const TYPE_CHECKBOX = 'CHECKBOX';
    const TYPE_CUSTOM = 'CUSTOM';
    const TYPE_FILE = 'FILE';

    const TYPES_LIST = [
        self::TYPE_STRING,
        self::TYPE_LIST,
        self::TYPE_CHECKBOX,
        self::TYPE_CUSTOM,
        self::TYPE_FILE
    ];


    /** @var string  */
    protected $code = null;
    /** @var string */
    protected $name = null;
    /** @var string */
    protected $type = null;
    /** @var bool  */
    protected $refresh = false;
    /** @var bool  */
    protected $multiply = false;
    /** @var bool  */
    protected $showAdditionalValue = false;
    /** @var mixed */
    protected $defaultValue = null;
    /** @var mixed */
    protected $value = null;

    public function __construct(string $code)
    {
        $this->setCode($code);
        $this->type = 'CUSTOM';
    }

    public function getCode ()
    {
        return $this->code;
    }

    public function setCode (string $code)
    {
        $code = strtoupper($code);
        $this->code = $code;

        return $this;
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

    public function getType ()
    {
        return $this->type;
    }

    public function setType (string $type)
    {
        $type = strtoupper($type);
        if (!in_array($type, self::TYPES_LIST))
        {
            new ArgumentOutOfRangeException('type',self::TYPES_LIST);
        }
        $this->type = $type;

        return $this;
    }

    public function isRefresh ()
    {
        return $this->refresh;
    }

    public function setRefresh (bool $refresh = true)
    {
        $this->refresh = $refresh;

        return $this;
    }

    public function isMultiply ()
    {
        return $this->multiply;
    }

    public function setMultiply (bool $multiply = true)
    {
        $this->multiply = $multiply;

        return $this;
    }

    public function isShowAdditionalValue ()
    {
        return $this->showAdditionalValue;
    }

    public function setShowAdditionalValue (bool $showAdditionalValue = true)
    {
        $this->showAdditionalValue = $showAdditionalValue;

        return $this;
    }

    public function getDefaultValue ()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue ($defaultValue = null)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function setValue ($value = null)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue ()
    {
        if (!is_null($this->value))
        {
            return $this->value;
        }
        elseif (!is_null($this->defaultValue))
        {
            return $this->defaultValue;
        }
        else
        {
            return null;
        }
    }
}
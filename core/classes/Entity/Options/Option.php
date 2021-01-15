<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Options;

use Ms\Core\Lib\Tools;

/**
 * Класс Ms\Core\Entity\Options\Option
 * Описывает одну известную в данной сессии опцию
 */
class Option
{
    /** @var string */
    protected $moduleName = null;
    /** @var string */
    protected $optionName = null;
    /** @var null|mixed */
    protected $optionValue = null;

    /**
     * Конструктор класса Option
     *
     * @param string     $moduleName  Имя модуля, который установил опцию
     * @param string     $optionName  Имя опции
     * @param mixed|null $optionValue Значение опции, либо NULL
     */
    public function __construct (string $moduleName, string $optionName, $optionValue = null)
    {
        $this->setOptionFullName($moduleName, $optionName);
        $this->setOptionValue($optionValue);
    }

    /**
     * Возвращает имя модуля, который установил опцию
     *
     * @return string
     * @unittest
     */
    public function getModuleName (): string
    {
        return $this->moduleName;
    }

    /**
     * Возвращает имя опции
     *
     * @return string
     * @unittest
     */
    public function getOptionName (): string
    {
        return $this->optionName;
    }

    /**
     * Устанавливает устанавливает полное название опии
     *
     * @param string $moduleName Имя модуля, который установил опцию
     * @param string $optionName Имя опции
     *
     * @return $this
     * @unittest
     */
    public function setOptionFullName (string $moduleName, string $optionName)
    {
        $this->moduleName = $moduleName;
        $this->optionName = $optionName;

        return $this;
    }

    /**
     * Возвращает полное название опции вида БРЕНД_ИМЯМОДУЛЯ_ИМЯ_ОПЦИИ
     *
     * @return string
     * @unittest
     */
    public function getOptionFullName ()
    {
        return Options::getInstance()->getOptionFullName($this->moduleName, $this->optionName);
    }

    /**
     * Возвращает значение опции
     *
     * @return mixed|null
     * @unittest
     */
    public function getOptionValue ()
    {
        return $this->optionValue;
    }

    /**
     * Устанавливает значение опции
     *
     * @param mixed|null $optionValue Значение опции
     *
     * @return $this
     * @unittest
     */
    public function setOptionValue ($optionValue = null)
    {
        $this->optionValue = $optionValue;

        return $this;
    }

    /**
     * Возвращает значение опции, приводя ее к типу int
     *
     * @return int|null
     * @unittest
     */
    public function getValueInt ()
    {
        return (is_null($this->optionValue) ? null : (int)$this->optionValue);
    }

    /**
     * Возвращает значение опции, приводя ее к типу string
     *
     * @return string|null
     * @unittest
     */
    public function getValueString ()
    {
        return (is_null($this->optionValue) ? null : (string)$this->optionValue);
    }

    /**
     * Возвращает значение опции, приводя ее к типу float
     *
     * @return float|null
     * @unittest
     */
    public function getValueFloat ()
    {
        return (is_null($this->optionValue) ? null : (float)$this->optionValue);
    }

    /**
     * Возвращает значение опции, приводя ее к типу bool
     *
     * @return bool|null
     * @unittest
     */
    public function getValueBool ()
    {
        return (is_null($this->optionValue) ? null : (bool)Tools::validateBoolVal($this->optionValue));
    }
}
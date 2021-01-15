<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Grid\Fields;

/**
 * Класс Ms\Core\Grid\Fields\SelectField
 * Поле select грида
 */
class SelectField extends Field
{
    /** @var string  */
    protected $align = "center";
    /** @var bool  */
    protected $autosearch = true;
    /** @var array  */
    protected $items = [];
    /** @var string  */
    protected $valueField = "";
    /** @var string  */
    protected $textField = "";
    /** @var int  */
    protected $selectedIndex = -1;
    /** @var string  */
    protected $valueType = "string";
    /** @var bool  */
    protected $readOnly = false;

    public function __construct (string $name, string $title = null)
    {
        parent::__construct($name, 'select', $title);
    }

    /**
     * Возвращает значение свойства autosearch
     *
     * @return bool
     */
    public function isAutosearch (): bool
    {
        return $this->autosearch;
    }

    /**
     * Устанавливает значение свойства autosearch
     *
     * @param bool $autosearch
     *
     * @return $this
     */
    public function setAutosearch (bool $autosearch)
    {
        $this->autosearch = $autosearch;

        return $this;
    }

    /**
     * Возвращает значение свойства items
     *
     * @return array
     */
    public function getItems (): array
    {
        return $this->items;
    }

    /**
     * Устанавливает значение свойства items
     *
     * @param array $items
     *
     * @return $this
     */
    public function setItems (array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Возвращает значение свойства valueField
     *
     * @return string
     */
    public function getValueField (): string
    {
        return $this->valueField;
    }

    /**
     * Устанавливает значение свойства valueField
     *
     * @param string $valueField
     *
     * @return $this
     */
    public function setValueField (string $valueField)
    {
        $this->valueField = $valueField;

        return $this;
    }

    /**
     * Возвращает значение свойства textField
     *
     * @return string
     */
    public function getTextField (): string
    {
        return $this->textField;
    }

    /**
     * Устанавливает значение свойства textField
     *
     * @param string $textField
     *
     * @return $this
     */
    public function setTextField (string $textField)
    {
        $this->textField = $textField;

        return $this;
    }

    /**
     * Возвращает значение свойства selectedIndex
     *
     * @return int
     */
    public function getSelectedIndex (): int
    {
        return $this->selectedIndex;
    }

    /**
     * Устанавливает значение свойства selectedIndex
     *
     * @param int $selectedIndex
     *
     * @return $this
     */
    public function setSelectedIndex (int $selectedIndex)
    {
        $this->selectedIndex = $selectedIndex;

        return $this;
    }

    /**
     * Возвращает значение свойства valueType
     *
     * @return string
     */
    public function getValueType (): string
    {
        return $this->valueType;
    }

    /**
     * Устанавливает значение свойства valueType
     *
     * @param string $valueType
     *
     * @return $this
     */
    protected function setValueType (string $valueType)
    {
        $this->valueType = $valueType;

        return $this;
    }

    /**
     * Устанавливает значение свойства valueType
     *
     * @return $this
     */
    public function setValueTypeString ()
    {
        return $this->setValueType('string');
    }

    /**
     * Устанавливает значение свойства valueType
     *
     * @return $this
     */
    public function setValueTypeNumber ()
    {
        return $this->setValueType('number');
    }

    /**
     * Возвращает значение свойства readOnly
     *
     * @return bool
     */
    public function isReadOnly (): bool
    {
        return $this->readOnly;
    }

    /**
     * Устанавливает значение свойства readOnly
     *
     * @param bool $readOnly
     *
     * @return $this
     */
    public function setReadOnly (bool $readOnly)
    {
        $this->readOnly = $readOnly;

        return $this;
    }


}
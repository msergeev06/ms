<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Grid\Fields;

/**
 * Класс Ms\Core\Grid\Fields\NumberField
 * Числовое поле грида
 */
class NumberField extends Field
{
    /** @var string  */
    protected $sorter = "number";
    /** @var string  */
    protected $align = "right";
    /** @var bool  */
    protected $readOnly = false;

    public function __construct (string $name, string $title = null)
    {
        parent::__construct($name, 'number', $title);
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
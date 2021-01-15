<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Grid\Fields;

/**
 * Класс Ms\Core\Grid\Fields\CheckboxField
 * Checkbox поле грида
 */
class CheckboxField extends Field
{
    /** @var string  */
    protected $sorter = 'number';
    /** @var string  */
    protected $align = 'center';
    /** @var bool  */
    protected $autosearch = true;

    public function __construct (string $name, string $title = null)
    {
        parent::__construct($name, 'checkbox', $title);
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


}
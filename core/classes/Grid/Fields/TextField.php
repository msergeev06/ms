<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Grid\Fields;

/**
 * Класс Ms\Core\Grid\Fields\TextField
 * Текстовое поле грида
 */
class TextField extends Field
{
    /** @var bool  */
    protected $autosearch = true;
    /** @var bool  */
    protected $readOnly = false;

    public function __construct (string $name, string $title = null)
    {
        parent::__construct($name, 'text', $title);
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
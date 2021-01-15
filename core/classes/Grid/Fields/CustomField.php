<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Grid\Fields;

/**
 * Класс Ms\Core\Grid\Fields\CustomField
 * Кастомное поле грида
 */
class CustomField extends Field
{
    protected $arCustomProperties = [];

    public function __construct (string $name, string $title = null, string $type = "custom")
    {
        parent::__construct($name, $type, $title);
    }

    /**
     * Возвращает значение свойства arCustomProperties
     *
     * @return array
     */
    public function getArCustomProperties (): array
    {
        return $this->arCustomProperties;
    }

    /**
     * Устанавливает значение свойства arCustomProperties
     *
     * @param array $arCustomProperties
     *
     * @return $this
     */
    public function setArCustomProperties (array $arCustomProperties)
    {
        $this->arCustomProperties = $arCustomProperties;

        return $this;
    }
}
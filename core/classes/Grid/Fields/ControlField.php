<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Grid\Fields;

/**
 * Класс Ms\Core\Grid\Fields\ControlField
 * Поле типа control грида
 */
class ControlField extends Field
{
    protected $editButton = true;
    protected $deleteButton = true;
    protected $clearFilterButton = true;
    protected $modeSwitchButton = true;

    protected $align = 'center';
    protected $width = 50;
    protected $filtering = false;
    protected $inserting = false;
    protected $editing = false;
    protected $sorting = false;

    protected $searchModeButtonTooltip = "Переключить на поиск";
    protected $insertModeButtonTooltip = "Переключить на вставку";
    protected $editButtonTooltip = "Редактировать";
    protected $deleteButtonTooltip = "Удалить";
    protected $searchButtonTooltip = "Поиск";
    protected $clearFilterButtonTooltip = "Очистить фильтр";
    protected $insertButtonTooltip = "Вставить";
    protected $updateButtonTooltip = "Обновить";
    protected $cancelEditButtonTooltip = "Отменить редактирование";


    public function __construct (string $name, string $title = null)
    {
        parent::__construct($name, 'control', $title);
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Grid\Fields;

/**
 * Класс Ms\Core\Grid\Fields\Field
 * Колонка грида
 */
abstract class Field
{
    /** @var string  */
    protected $type = "";
    /** @var string  */
    protected $name = "";
    /** @var string  */
    protected $title = "";
    /** @var string  */
    protected $align = "left";
    /** @var int  */
    protected $width = 100;
    /** @var bool  */
    protected $visible = true;

    /** @var string  */
    protected $css = "";
    /** @var string  */
    protected $headercss = "";
    /** @var string  */
    protected $filtercss = "";
    /** @var string  */
    protected $insertcss = "";
    /** @var string  */
    protected $editcss = "";

    /** @var bool  */
    protected $filtering = false;
    /** @var bool  */
    protected $inserting = false;
    /** @var bool  */
    protected $editing = false;
    /** @var bool  */
    protected $sorting = true;
    /** @var string  */
    protected $sorter = "string";

    /** @var null|string */
    protected $headerTemplate = null;
    /** @var null|string */
    protected $itemTemplate = null;
    /** @var null|string */
    protected $filterTemplate = null;
    /** @var null|string */
    protected $insertTemplate = null;
    /** @var null|string */
    protected $editTemplate = null;

    /** @var null|string */
    protected $filterValue = null;
    /** @var null|string */
    protected $insertValue = null;
    /** @var null|string */
    protected $editValue = null;

    /** @var null|string */
    protected $cellRenderer = null;

    /** @var null|string */
    protected $validate = null;

    public function __construct (string $name, string $type = "string", string $title = null)
    {
        $this->setName($name);
        $this->setType($type);
        if (is_null($title))
        {
            $this->setTitle($name);
        }
        else
        {
            $this->setTitle($title);
        }
    }

    /**
     * Возвращает значение свойства type
     *
     * @return string
     */
    public function getType (): string
    {
        return $this->type;
    }

    /**
     * Устанавливает значение свойства type
     *
     * @param string $type
     *
     * @return $this
     */
    protected function setType (string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Возвращает значение свойства name
     *
     * @return string
     */
    public function getName (): string
    {
        return $this->name;
    }

    /**
     * Устанавливает значение свойства name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName (string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Возвращает значение свойства title
     *
     * @return string
     */
    public function getTitle (): string
    {
        return $this->title;
    }

    /**
     * Устанавливает значение свойства title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle (string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Возвращает значение свойства align
     *
     * @return string
     */
    public function getAlign (): string
    {
        return $this->align;
    }

    /**
     * Устанавливает значение свойства align
     *
     * @param string $align
     *
     * @return $this
     */
    protected function setAlign (string $align)
    {
        $this->align = $align;

        return $this;
    }

    /**
     * Устанавливает значение свойства align
     *
     * @return $this
     */
    public function setAlignLeft ()
    {
        return $this->setAlign('left');
    }

    /**
     * Устанавливает значение свойства align
     *
     * @return $this
     */
    public function setAlignCenter ()
    {
        return $this->setAlign('center');
    }

    /**
     * Устанавливает значение свойства align
     *
     * @return $this
     */
    public function setAlignRight ()
    {
        return $this->setAlign('right');
    }

    /**
     * Возвращает значение свойства width
     *
     * @return int
     */
    public function getWidth (): int
    {
        return $this->width;
    }

    /**
     * Устанавливает значение свойства width
     *
     * @param int $width
     *
     * @return $this
     */
    public function setWidth (int $width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Возвращает значение свойства visible
     *
     * @return bool
     */
    public function isVisible (): bool
    {
        return $this->visible;
    }

    /**
     * Устанавливает значение свойства visible
     *
     * @param bool $visible
     *
     * @return $this
     */
    public function setVisible (bool $visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Возвращает значение свойства css
     *
     * @return string
     */
    public function getCss (): string
    {
        return $this->css;
    }

    /**
     * Устанавливает значение свойства css
     *
     * @param string $css
     *
     * @return $this
     */
    public function setCss (string $css)
    {
        $this->css = $css;

        return $this;
    }

    /**
     * Возвращает значение свойства headercss
     *
     * @return string
     */
    public function getHeadercss (): string
    {
        return $this->headercss;
    }

    /**
     * Устанавливает значение свойства headercss
     *
     * @param string $headercss
     *
     * @return $this
     */
    public function setHeadercss (string $headercss)
    {
        $this->headercss = $headercss;

        return $this;
    }

    /**
     * Возвращает значение свойства filtercss
     *
     * @return string
     */
    public function getFiltercss (): string
    {
        return $this->filtercss;
    }

    /**
     * Устанавливает значение свойства filtercss
     *
     * @param string $filtercss
     *
     * @return $this
     */
    public function setFiltercss (string $filtercss)
    {
        $this->filtercss = $filtercss;

        return $this;
    }

    /**
     * Возвращает значение свойства insertcss
     *
     * @return string
     */
    public function getInsertcss (): string
    {
        return $this->insertcss;
    }

    /**
     * Устанавливает значение свойства insertcss
     *
     * @param string $insertcss
     *
     * @return $this
     */
    public function setInsertcss (string $insertcss)
    {
        $this->insertcss = $insertcss;

        return $this;
    }

    /**
     * Возвращает значение свойства editcss
     *
     * @return string
     */
    public function getEditcss (): string
    {
        return $this->editcss;
    }

    /**
     * Устанавливает значение свойства editcss
     *
     * @param string $editcss
     *
     * @return $this
     */
    public function setEditcss (string $editcss)
    {
        $this->editcss = $editcss;

        return $this;
    }

    /**
     * Возвращает значение свойства filtering
     *
     * @return bool
     */
    public function isFiltering (): bool
    {
        return $this->filtering;
    }

    /**
     * Устанавливает значение свойства filtering
     *
     * @param bool $filtering
     *
     * @return $this
     */
    public function setFiltering (bool $filtering)
    {
        $this->filtering = $filtering;

        return $this;
    }

    /**
     * Возвращает значение свойства inserting
     *
     * @return bool
     */
    public function isInserting (): bool
    {
        return $this->inserting;
    }

    /**
     * Устанавливает значение свойства inserting
     *
     * @param bool $inserting
     *
     * @return $this
     */
    public function setInserting (bool $inserting)
    {
        $this->inserting = $inserting;

        return $this;
    }

    /**
     * Возвращает значение свойства editing
     *
     * @return bool
     */
    public function isEditing (): bool
    {
        return $this->editing;
    }

    /**
     * Устанавливает значение свойства editing
     *
     * @param bool $editing
     *
     * @return $this
     */
    public function setEditing (bool $editing)
    {
        $this->editing = $editing;

        return $this;
    }

    /**
     * Возвращает значение свойства sorting
     *
     * @return bool
     */
    public function isSorting (): bool
    {
        return $this->sorting;
    }

    /**
     * Устанавливает значение свойства sorting
     *
     * @param bool $sorting
     *
     * @return $this
     */
    public function setSorting (bool $sorting)
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * Возвращает значение свойства sorter
     *
     * @return string
     */
    public function getSorter (): string
    {
        return $this->sorter;
    }

    /**
     * Устанавливает значение свойства sorter
     *
     * @param string $sorter
     *
     * @return $this
     */
    public function setSorter (string $sorter)
    {
        $this->sorter = $sorter;

        return $this;
    }

    /**
     * Возвращает значение свойства headerTemplate
     *
     * @return string|null
     */
    public function getHeaderTemplate (): string
    {
        return $this->headerTemplate;
    }

    /**
     * Устанавливает значение свойства headerTemplate
     *
     * @param string|null $headerTemplate
     *
     * @return $this
     */
    public function setHeaderTemplate (string $headerTemplate)
    {
        $this->headerTemplate = $headerTemplate;

        return $this;
    }

    /**
     * Возвращает значение свойства itemTemplate
     *
     * @return string|null
     */
    public function getItemTemplate (): string
    {
        return $this->itemTemplate;
    }

    /**
     * Устанавливает значение свойства itemTemplate
     *
     * @param string|null $itemTemplate
     *
     * @return $this
     */
    public function setItemTemplate (string $itemTemplate)
    {
        $this->itemTemplate = $itemTemplate;

        return $this;
    }

    /**
     * Возвращает значение свойства filterTemplate
     *
     * @return string|null
     */
    public function getFilterTemplate (): string
    {
        return $this->filterTemplate;
    }

    /**
     * Устанавливает значение свойства filterTemplate
     *
     * @param string|null $filterTemplate
     *
     * @return $this
     */
    public function setFilterTemplate (string $filterTemplate)
    {
        $this->filterTemplate = $filterTemplate;

        return $this;
    }

    /**
     * Возвращает значение свойства insertTemplate
     *
     * @return string|null
     */
    public function getInsertTemplate (): string
    {
        return $this->insertTemplate;
    }

    /**
     * Устанавливает значение свойства insertTemplate
     *
     * @param string|null $insertTemplate
     *
     * @return $this
     */
    public function setInsertTemplate (string $insertTemplate)
    {
        $this->insertTemplate = $insertTemplate;

        return $this;
    }

    /**
     * Возвращает значение свойства editTemplate
     *
     * @return string|null
     */
    public function getEditTemplate (): string
    {
        return $this->editTemplate;
    }

    /**
     * Устанавливает значение свойства editTemplate
     *
     * @param string|null $editTemplate
     *
     * @return $this
     */
    public function setEditTemplate (string $editTemplate)
    {
        $this->editTemplate = $editTemplate;

        return $this;
    }

    /**
     * Возвращает значение свойства filterValue
     *
     * @return string|null
     */
    public function getFilterValue (): string
    {
        return $this->filterValue;
    }

    /**
     * Устанавливает значение свойства filterValue
     *
     * @param string|null $filterValue
     *
     * @return $this
     */
    public function setFilterValue (string $filterValue)
    {
        $this->filterValue = $filterValue;

        return $this;
    }

    /**
     * Возвращает значение свойства insertValue
     *
     * @return string|null
     */
    public function getInsertValue (): string
    {
        return $this->insertValue;
    }

    /**
     * Устанавливает значение свойства insertValue
     *
     * @param string|null $insertValue
     *
     * @return $this
     */
    public function setInsertValue (string $insertValue)
    {
        $this->insertValue = $insertValue;

        return $this;
    }

    /**
     * Возвращает значение свойства editValue
     *
     * @return string|null
     */
    public function getEditValue (): string
    {
        return $this->editValue;
    }

    /**
     * Устанавливает значение свойства editValue
     *
     * @param string|null $editValue
     *
     * @return $this
     */
    public function setEditValue (string $editValue)
    {
        $this->editValue = $editValue;

        return $this;
    }

    /**
     * Возвращает значение свойства cellRenderer
     *
     * @return string|null
     */
    public function getCellRenderer (): string
    {
        return $this->cellRenderer;
    }

    /**
     * Устанавливает значение свойства cellRenderer
     *
     * @param string|null $cellRenderer
     *
     * @return $this
     */
    public function setCellRenderer (string $cellRenderer)
    {
        $this->cellRenderer = $cellRenderer;

        return $this;
    }

    /**
     * Возвращает значение свойства validate
     *
     * @return string|null
     */
    public function getValidate (): string
    {
        return $this->validate;
    }

    /**
     * Устанавливает значение свойства validate
     *
     * @param string|null $validate
     *
     * @return $this
     */
    public function setValidate (string $validate)
    {
        $this->validate = $validate;

        return $this;
    }


}
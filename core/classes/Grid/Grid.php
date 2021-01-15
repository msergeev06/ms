<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Grid;

/**
 * Класс Ms\Core\Grid\Grid
 * <Описание>
 */
class Grid
{
    protected $fields = null;
    protected $data = null;

    protected $autoload = false;
    protected $controller = null;

    protected $width = "auto";
    protected $height = "auto";

    protected $heading = true;
    protected $filtering = false;
    protected $inserting = false;
    protected $editing = false;
    protected $selecting = true;
    protected $sorting = false;
    protected $paging = false;
    protected $pageLoading = false;

    protected $rowClass = null;
    protected $rowClick = null;

    protected $rowDoubleClick = null;

    protected $confirmDeleting = true;

    protected $pagerContainer = null;
    protected $pageIndex = 1;
    protected $pageSize = 20;
    protected $pageButtonCount = 15;

    protected $invalidNotify = null;

    protected $loadIndication = true;
    protected $loadIndicationDelay = 500;
    protected $loadShading = true;

    protected $updateOnResize = true;

    protected $rowRenderer = null;
    protected $headerRowRenderer = null;
    protected $filterRowRenderer = null;
    protected $insertRowRenderer = null;
    protected $editRowRenderer = null;

    public function __construct ()
    {
    }


}
<?php
/**
 * Компонент ядра ms:grid
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Components;

use Ms\Core\Entity\System\Application;
use Ms\Core\Grid\Grid;

/**
 * Класс Ms\Core\Entity\Components\GridComponent
 * Компонент грида
 */
class GridComponent extends Component
{
    public function __construct ($component, $template='.default', $arParams=array())
    {
        parent::__construct($component,$template,$arParams);
    }

    public function run ()
    {
        $app = Application::getInstance();

        $this->arParams['AJAX_HANDLER_URL'] = $app->getSitePath(
            $this->componentsRoot . '/'
            . $this->namespace . '/'
            . $this->componentName . '/ajax.php'
        );

/*        if (is_null($this->arParams['GRID']) || !($this->arParams['GRID'] instanceof Grid))
        {
            return;
        }*/

        $this->getData();

/*        if (!isset($this->arResult['JSON']) || (string)$this->arResult['JSON'] == '')
        {
            return;
        }*/

        $this->includeTemplate();
    }

    protected function getData ()
    {
        /** @var Grid $grid */
        $grid = $this->arParams['GRID'];
        // $this->arResult['JSON'] = $grid->convertToJson();
        // $this->arResult['JSON'] = $grid->convertToJsObject();
    }
}
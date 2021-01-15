<?php if(!defined('MS_PROLOG_INCLUDED')||MS_PROLOG_INCLUDED!==true)die();
/**
 * Компонент ядра ms:grid
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

$arParams = &$this->arParams;
$arResult = &$this->arResult;
msDebug($arParams);
msDebug($arResult);
$app = \Ms\Core\Entity\System\Application::getInstance();
$app->includePlugin('ms.jsgrid');
if (is_null($app->getAppParam('isset_grid_locale')))
{
    $app->addBufferContent('head_script',"jsGrid.locale('ru');\n");
    $app->setAppParams('isset_grid_locale',true);
}

msDebug($this);

?>
<div id="grid_my_wishes_list">

</div>


<script type="text/javascript">
    var ajaxHandlerUrl = "<?=$this->arParams['AJAX_HANDLER_URL']?>";

    $("#grid_my_wishes_list").jsGrid({

        pageIndex: 1,
        pageSize: 1,
        pageButtonCount: 8,


        width: "100%",
        // height: "400px",

        // inserting: true,
        // editing: true,
        sorting: true,
        paging: true,
        filtering: true,

        data: [
            {
                "Id": 1,
                "Sort": 500,
                "Name": "Желание 1",
                "Private": true
            },
            {
                "Id": 2,
                "Sort": 200,
                "Name": "Желание 2",
                "Private": false
            }
        ],

        fields: [
            { "name": "Menu", "type": "actions", "width": 10, "sorting": false, "title": "", "filtering": false },
            { "name": "Sort", "type": "number", "width": 50, "title": "Сортировка", "editing": true, "filtering": false },
            { "name": "Id", "type": "number", "width": 50, "title": "ID", "filtering": false },
            { "name": "Name", "type": "string", "width": 200, "title": "Название желания", "filtering": false },
            { "name": "Private", "type": "checkbox", "title": "Личное", "sorting": false }
        ]
    });

</script>

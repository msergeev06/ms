<?php
/**
 * Компонент ядра ms:table
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

use \Ms\Core\Entity\Components\Parameters;

if (isset($_REQUEST['page']))
{
    $page = $_REQUEST['page'];
}
else
{
	$page = 1;
}

IncludeLangFile(__FILE__);

return (new Parameters\ParameterGroupCollection())
    ->addGroup(
        (new Parameters\ParameterGroup())
        ->addParameter(
            (new Parameters\Parameter('TABLE_HEADER'))
            ->setName(GetCoreMessage('table_header'))
            ->setDefaultValue([])
        )
        ->addParameter(
            (new Parameters\Parameter('TABLE_DATA'))
            ->setName(GetCoreMessage('table_data'))
            ->setDefaultValue([])
        )
        ->addParameter(
            (new Parameters\Parameter('TABLE_FOOTER'))
            ->setName(GetCoreMessage('table_footer'))
            ->setDefaultValue([])
        )
    )
;

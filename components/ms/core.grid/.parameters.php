<?php
/**
 * Компонент ядра ms:grid
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

use \Ms\Core\Entity\Components\Parameters;

if (isset($_REQUEST['page']) && (int)$_REQUEST['page'] > 0)
{
    $page = (int)$_REQUEST['page'];
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
                (new Parameters\Parameter('GRID_ID'))
                    ->setName('ID грида')
                    ->setDefaultValue('grid')
            )
            ->addParameter(
                (new Parameters\Parameter('GRID'))
                    ->setName('Объект грида')
                    ->setDefaultValue(null)
            )
            ->addParameter(
                (new Parameters\Parameter('PAGE'))
                    ->setName(GetCoreMessage('page'))
                    ->setDefaultValue($page)
            )
    )
;

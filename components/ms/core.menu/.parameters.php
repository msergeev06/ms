<?php
/**
 * Компонент ядра ms:menu
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

use Ms\Core\Entity\Components\Parameters;

IncludeLangFile(__FILE__);

return (new Parameters\ParameterGroupCollection())
    ->addGroup(
        (new Parameters\ParameterGroup())
        ->addParameter(
            (new Parameters\StringParameter('MAIN_MENU_TYPE'))
            ->setName(GetCoreMessage('main_menu_type'))
            ->setDefaultValue('top')
        )
        ->addParameter(
            (new Parameters\StringParameter('SECOND_MENU_TYPE'))
            ->setName(GetCoreMessage('second_menu_type'))
            ->setDefaultValue(null)
        )
        ->addParameter(
            (new Parameters\StringParameter('THIRD_MENU_TYPE'))
            ->setName(GetCoreMessage('third_menu_type'))
            ->setDefaultValue(null)
        )
    )
;

<?php
/**
 * Компонент ядра ms:form
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

use \Ms\Core\Entity\Components\Parameters;

IncludeLangFile(__FILE__);

return (new Parameters\ParameterGroupCollection())
    ->addGroup(
        (new Parameters\ParameterGroup())
        ->addParameter(
            (new Parameters\StringParameter('FORM_CLASS'))
            ->setName(GetCoreMessage('form_class'))
            ->setDefaultValue('form-horizontal')
        )
        ->addParameter(
            (new Parameters\StringParameter('FORM_NAME'))
            ->setName(GetCoreMessage('form_name'))
            ->setDefaultValue('form')
        )
        ->addParameter(
            (new Parameters\StringParameter('FORM_METHOD'))
            ->setName(GetCoreMessage('form_method'))
            ->setDefaultValue('post')
        )
        ->addParameter(
            (new Parameters\StringParameter('FORM_ACTION'))
            ->setName(GetCoreMessage('form_action'))
            ->setDefaultValue('')
        )
        ->addParameter(
            (new Parameters\StringParameter('FORM_SUBMIT_NAME'))
            ->setName(GetCoreMessage('form_submit_name'))
            ->setDefaultValue('SUBMIT')
        )
        ->addParameter(
            (new Parameters\StringParameter('FORM_SUBMIT_CLASS'))
            ->setName(GetCoreMessage('form_submit_class'))
            ->setDefaultValue('submit btn btn-success')
        )
        ->addParameter(
            (new Parameters\Parameter('FORM_FIELDS'))
            ->setName(GetCoreMessage('form_fields'))
            ->setDefaultValue([])
        )
        ->addParameter(
            (new Parameters\StringParameter('FORM_HANDLER'))
            ->setName(GetCoreMessage('form_handler'))
            ->setDefaultValue(null)
        )
        ->addParameter(
            (new Parameters\CheckboxParameter('SHOW_FORM_IF_OK'))
            ->setName(GetCoreMessage('show_form_if_ok'))
            ->setDefaultValue(false)
        )
        ->addParameter(
            (new Parameters\StringParameter('REDIRECT_IF_OK'))
            ->setName(GetCoreMessage('redirect_if_ok'))
            ->setDefaultValue('')
        )
    )
;

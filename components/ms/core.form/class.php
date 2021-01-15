<?php
/**
 * Компонент ядра ms:form
 *
 * @package    Ms\Core
 * @subpackage Entity\Components
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @since      0.2.0
 */

namespace Ms\Core\Entity\Components;

use Ms\Core\Entity\Errors\FileLogger;
use Ms\Core\Entity\Form\Field;

class FormComponent extends Component
{
    protected $logger = null;

    public function __construct ($component, $template = '.default', $arParams = [], Component $parentComponent = null)
    {
        parent::__construct($component, $template, $arParams, $parentComponent);
        $this->logger = new FileLogger('core');
    }

    public function run ()
    {
        $arParams = &$this->arParams;
        $arResult = &$this->arResult;

        $arResult['FIELDS_VALUES'] = [];
        /**
         * @var Field $field
         */
        foreach ($arParams['FORM_FIELDS'] as $field)
        {
            $data = $this->getData($field->getName());
            $default = $field->getDefaultValue();
            if (!is_null($data))
            {
                $arResult['FIELDS_VALUES'][$field->getName()] = $data;
            }
            elseif (!is_null($default))
            {
                $arResult['FIELDS_VALUES'][$field->getName()] = $default;
            }
            else
            {
                $arResult['FIELDS_VALUES'][$field->getName()] = '';
            }
        }

        $this->includeTemplate();

        //msDebug($arParams);
        //msDebug($arResult);
    }

    protected function getData ($fieldName)
    {
        if (isset($_REQUEST[$fieldName]))
        {
            return $_REQUEST[$fieldName];
        }
        else
        {
            return null;
        }
    }
}
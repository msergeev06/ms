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

namespace Ms\Core\Entity\Components;

use Ms\Core\Entity\Application;
use Ms\Core\Entity\Component;
use Ms\Core\Entity\Form\Field;
use Ms\Core\Lib\Loader;
use Ms\Core\Lib\Modules;

class FormComponent extends Component
{
	public function __construct ($component, $template='.default', $arParams=array(), Component $parentComponent = null)
	{
		parent::__construct($component,$template,$arParams, $parentComponent);
	}

	public function run ()
	{
		$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		$arResult['FIELDS_VALUES'] = array();
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

	protected function getData($fieldName)
	{
		$request = Application::getInstance()->getContext()->getRequest();
//		msDebug($fieldName);
		if (strtolower($this->arParams['FORM_METHOD']) == 'post')
		{
			return $request->getPost($fieldName);
		}
		elseif (strtolower($this->arParams['FORM_METHOD']) == 'get')
		{
			return $request->getQuery($fieldName);
		}
		else
		{
			return $request->get($fieldName);
		}
	}
}
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
	public function __construct ($component, $template='.default', $arParams=array())
	{
		parent::__construct($component,$template,$arParams);
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

		$form_action = $this->getData('form_action');
		if (!is_null($form_action) && intval($form_action)==1)
		{
			$namespace = $arParams['FORM_HANDLER'][0];
			$function = $arParams['FORM_HANDLER'][1];
			if (Loader::includeModule(Modules::getModuleFromNamespace ($namespace)))
			{
				$arResult['FORM_RESULT'] = $namespace::$function($arResult['FIELDS_VALUES']);
			}

			if ($arResult['FORM_RESULT'] !== false)
			{
				if ($arParams['SHOW_FORM_IF_OK']===true)
				{
					$this->includeTemplate();
				}
				elseif ($arParams['REDIRECT_IF_OK']!=='')
				{
					Application::getInstance()->setRefresh($arParams['REDIRECT_IF_OK']);
				}
			}
			else
			{
				$arResult['FORM_ERRORS'] = $namespace::getErrorList();
			}
		}
		else
		{
			$this->includeTemplate();
		}

		//msDebug($arParams);
		//msDebug($arResult);
	}

	protected function getData($fieldName)
	{
		$request = Application::getInstance()->getContext()->getRequest();
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
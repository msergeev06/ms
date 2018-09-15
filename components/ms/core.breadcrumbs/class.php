<?php
/**
 * Компонент ядра ms:code.breadcrumbs
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
use Ms\Core\Lib\Events;

class BreadcrumbsComponent extends Component
{
	public function __construct ($component, $template='.default', $arParams=array())
	{
		parent::__construct($component,$template,$arParams);
	}

	public function run ()
	{
		$arResult = &$this->arResult;

		$app = Application::getInstance();
//		$arResult['BREADCRUMBS'] = $app->getBreadcrumbs()->getNavArray();

		$path = $this->getIncludeTemplatePath();
		if (!is_null($path))
		{
			$app->showBufferContent('breadcrumbs_component');
			$app->addAppParam('breadcrumbs_path',$path);
			Events::addEventHandler(
				'core',
				'OnEndBufferPage',
				'\Ms\Core\Entity\Breadcrumbs::onEndBufferPageHandler'
			);
		}

	}

}
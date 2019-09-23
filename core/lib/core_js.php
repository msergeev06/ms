<?php

namespace Ms\Core\Lib;

use Ms\Core\Entity\Application;

class CoreJs
{
	public static function init ()
	{
		$app = Application::getInstance();
		$app->addJS($app->getDocumentRoot().'/ms/core/js/core.js');
		$content = 'MS.core.user.ID = '.$app->getUser()->getID().';
		MS.core.user.bAdmin = '.($app->getUser()->isAdmin()?'true':'false').';
		MS.core.session.ID = "'.$app->getSession()->getSID().'";
		';
		$app->addBufferContent('head_script',$content,false);
	}

	public static function includeModuleJs ($sModuleName, $sFilename)
	{
		if (strpos($sFilename,'.js')!==false)
		{
			$sFilename = str_replace('.js','',$sFilename);
		}
		$path = Modules::getPathToModuleJs($sModuleName);
		if ($path)
		{
			Application::getInstance()->addJS($path.'/'.$sFilename.'.js');
		}
	}
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\System\Application;

/**
 * Класс Ms\Core\Lib\CoreJs
 * Подключение js файлов ядра
 */
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
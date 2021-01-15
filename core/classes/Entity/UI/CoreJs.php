<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Entity\UI;

use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Entity\Modules\Modules;

/**
 * Класс Ms\Core\Lib\CoreJs
 * Подключение js файлов ядра
 */
class CoreJs extends Multiton
{
	protected function __construct ()
	{
		$app = Application::getInstance();
		$app->addJS($app->getDocumentRoot().'/ms/core/js/core.js');
		$content = <<<EOL
MS.core.user.ID = '#USER_ID#';
MS.core.user.isAdmin = '#IS_ADMIN#';
MS.core.session.ID = "#SESSION_ID#";

EOL;
		$content = str_replace('#USER_ID#',(string)$app->getUser()->getID(),$content);
		$content = str_replace('#IS_ADMIN#',($app->getUser()->isAdmin()?'true':'false'), $content);
		$content = str_replace('#SESSION_ID#',$app->getSession()->getSID(),$content);

		$app->addBufferContent('head_script',$content,false);
	}

	public static function includeModuleJs ($sModuleName, $sFilename)
	{
		if (strpos($sFilename,'.js')!==false)
		{
			$sFilename = str_replace('.js','',$sFilename);
		}
		$path = Modules::getInstance()->getPathToModuleJs($sModuleName);
		if ($path)
		{
			Application::getInstance()->addJS($path.'/'.$sFilename.'.js');
		}
	}
}
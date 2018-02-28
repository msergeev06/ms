<?php
/**
 * Prolog Before
 * Загружается ядро и основной функционал
 *
 * @package Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

use Ms\Core\Entity;

$start = microtime();
$GLOBALS["MS_STATE"] = "PB";
if(isset($_REQUEST["MS_STATE"])) unset($_REQUEST["MS_STATE"]);
if(isset($_GET["MS_STATE"])) unset($_GET["MS_STATE"]);
if(isset($_POST["MS_STATE"])) unset($_POST["MS_STATE"]);
if(isset($_COOKIE["MS_STATE"])) unset($_COOKIE["MS_STATE"]);
if(isset($_FILES["MS_STATE"])) unset($_FILES["MS_STATE"]);

//define('NO_USER_USE',true);


require_once(dirname(__FILE__).'/tools/tools.msdebug.php');
require_once(dirname(__FILE__).'/entity/settings.php');
require_once(dirname(__FILE__).'/entity/application.php');
$application = Entity\Application::getInstance();
$application->setTimes('START_EXEC_PROLOG_BEFORE_1',$start);
$application->setState('PB');
$application->loadSettings();
require_once(dirname(__FILE__).'/include.php');
set_error_handler('\Ms\Core\Lib\ErrorHandler::handler');
defined('MS_AUTOLOAD_CLASSES_ENABLED',true);
\Ms\Core\Lib\Events::runEvents('core','OnPrologBefore');
if ($arAutoLoadModules = $application->getSettings()->getAutoLoadModules())
{
	foreach ($arAutoLoadModules as $module)
	{
		if (\Ms\Core\Lib\Loader::issetModule($module))
		{
			\Ms\Core\Lib\Loader::includeModule($module);
		}
	}
}
\Ms\Core\Lib\Events::runEvents('core','OnProlog');

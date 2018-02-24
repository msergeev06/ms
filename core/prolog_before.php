<?php
/**
 * Prolog Before
 * Загружается ядро и основной функционал
 *
 * @package MSergeev\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

use MSergeev\Core\Entity;

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
set_error_handler('\MSergeev\Core\Lib\ErrorHandler::handler');
\MSergeev\Core\Lib\Events::runEvents('core','OnPrologBefore');
if ($arAutoLoadModules = $application->getSettings()->getAutoLoadModules())
{
	foreach ($arAutoLoadModules as $module)
	{
		if (\MSergeev\Core\Lib\Loader::issetModule($module))
		{
			\MSergeev\Core\Lib\Loader::includeModule($module);
		}
	}
}
\MSergeev\Core\Lib\Events::runEvents('core','OnProlog');

<?php
/**
 * Prolog Before
 * Загружается ядро и основной функционал
 *
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

use Ms\Core\Api\ApiAdapter;
use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\System\Starter;

$pageStart = microtime(true);

require_once(dirname(__FILE__).'/tools/tools.errors.php');
register_shutdown_function('msShutdownHandler');
// set_error_handler("msErrorHandler");
// ob_start("msErrorCallback");
require_once(dirname(__FILE__).'/tools/tools.msdebug.php');
require_once (dirname(__FILE__) . '/classes/Entity/System/Starter.php');
$starter = new Starter($pageStart, dirname(__FILE__));

ApiAdapter::getInstance()->getEventsApi()->runEvents('core','OnPrologBefore');

require_once(dirname(__FILE__).'/include.php');

if (file_exists(Application::getInstance()->getSettings()->getMsRoot() . '/assembly.php'))
{
    include (Application::getInstance()->getSettings()->getMsRoot().'/assembly.php');
}

ApiAdapter::getInstance()->getEventsApi()->runEvents('core','OnProlog');
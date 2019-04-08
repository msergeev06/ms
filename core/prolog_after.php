<?php
/**
 * Prolog After
 * Загрушается все, что отвечает за вывод информации
 *
 * @package Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

use Ms\Core\Entity\Application;

$app = Application::getInstance();

$app->setTimes('START_EXEC_PROLOG_AFTER_1',microtime());
$app->setState('PA');
define('SITE_CHARSET',strtolower($app->getSettings()->getCharset()));

if(!headers_sent())
	header("Content-type: text/html; charset=".SITE_CHARSET);

$app->setTimes('START_EXEC_PROLOG_AFTER_2',microtime());
$app->setState('WA');

$app->startBufferPage();

$templatePath = $app->getSettings()->getTemplatesRoot().'/'.$app->getSiteTemplate();

\Ms\Core\Lib\Events::runEvents('core','OnPrologAfter',array(&$templatePath));

define('SITE_TEMPLATE_PATH',$app->getSitePath($templatePath));
define('MS_PROLOG_INCLUDED',true);

$app->includePlugin('ms.jquery');

if (file_exists($templatePath.'/style.css'))
{
	$app->addCSS($templatePath.'/style.css');
}

if (file_exists($templatePath.'/script.js'))
{
	$app->addJS($templatePath.'/script.js');
}

if (file_exists($templatePath.'/header.php')
)
{
	include($templatePath.'/header.php');
}


<?php
/**
 * Prolog After
 * Загрушается все, что отвечает за вывод информации
 *
 * @package MSergeev\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

use MSergeev\Core\Entity\Application;

$app = Application::getInstance();

$app->setTimes('START_EXEC_PROLOG_AFTER_1',microtime());
$app->setState('PA');
define('SITE_CHARSET',strtolower($app->getSettings()->getCharset()));

if(!headers_sent())
	header("Content-type: text/html; charset=".SITE_CHARSET);

$app->setTimes('START_EXEC_PROLOG_AFTER_2',microtime());
$app->setState('WA');

$app->startBuffer();

define('SITE_TEMPLATE_PATH',$app->getSitePath(
	$app->getSettings()->getTemplatesRoot().'/'.$app->getSiteTemplate()
	)
);
define('MS_PROLOG_INCLUDED',true);

if (file_exists($app->getSettings()->getCoreRoot().'/js/jquery-1.11.3.min.js'))
{
	$app->addJS($app->getSettings()->getCoreRoot().'/js/jquery-1.11.3.min.js');
}
$templatePath = $app->getSettings()->getTemplatesRoot().'/'.$app->getSiteTemplate();

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


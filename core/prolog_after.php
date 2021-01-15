<?php
/**
 * Prolog After
 * Загрушается все, что отвечает за вывод информации
 *
 * @package Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

use Ms\Core\Entity\System\Application;

$app = Application::getInstance();

$app->setTimes('START_EXEC_PROLOG_AFTER_1',microtime());
$app->setState('PA');
$siteCharset = strtolower($app->getSettings()->getCharset());
$app->setAppParams('site_charset',$siteCharset);

if(!headers_sent())
{
    header("Content-type: text/html; charset=".$siteCharset);
}

$app->setTimes('START_EXEC_PROLOG_AFTER_2',microtime());
$app->setState('WA');

$app->startBufferPage();

$templatePath = $app->getSettings()->getTemplatesRoot().'/'.$app->getSiteTemplate();

\Ms\Core\Api\ApiAdapter::getInstance()->getEventsApi()->runEvents(
    'core',
    'OnPrologAfter',
    [&$templatePath]
);

$app->setAppParams('site_template_path', $app->getSitePath($templatePath) );
define('MS_PROLOG_INCLUDED',true);

$app->includePlugin('ms.jquery');

\Ms\Core\Entity\UI\CoreJs::getInstance();

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


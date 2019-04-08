<?php
/**
 * Epilog Before
 * Производятся действия нижней видимой части страницы
 *
 * @package Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */
use Ms\Core\Entity\Application;

/*$footer = Application::getInstance()->getSettings()->getTemplatesRoot().'/'
	.Application::getInstance()->getSiteTemplate().'/footer.php';*/

if (file_exists($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/footer.php'))
{
	include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/footer.php');
}

Application::getInstance()->endBufferPage();
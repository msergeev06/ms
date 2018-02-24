<?php
/**
 * Epilog Before
 * Производятся действия нижней видимой части страницы
 *
 * @package MSergeev\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */
use MSergeev\Core\Entity\Application;

/*$footer = Application::getInstance()->getSettings()->getTemplatesRoot().'/'
	.Application::getInstance()->getSiteTemplate().'/footer.php';*/

if (file_exists(SITE_TEMPLATE_PATH.'/footer.php')
)
{
	include(SITE_TEMPLATE_PATH.'/footer.php');
}

Application::getInstance()->endBufferPage();
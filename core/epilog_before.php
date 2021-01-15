<?php
/**
 * Epilog Before
 * Производятся действия нижней видимой части страницы
 *
 * @package Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */
use Ms\Core\Entity\System\Application;

$siteTemplatePath = Application::getInstance()->getAppParam('site_template_path');

if (file_exists($_SERVER['DOCUMENT_ROOT'] . $siteTemplatePath . '/footer.php'))
{
	include($_SERVER['DOCUMENT_ROOT'] . $siteTemplatePath . '/footer.php');
}

Application::getInstance()->endBufferPage();
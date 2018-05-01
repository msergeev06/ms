<?php
/**
 * Компонент ядра ms:menu
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

use Ms\Core\Lib\Loc;

Loc::includeLocFile(__FILE__);

return array(
	'MAIN_MENU_TYPE' => array(
		'NAME' => Loc::getCoreMessage('main_menu_type'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'top'
	),
	'SECOND_MENU_TYPE' => array(
		'NAME' => Loc::getCoreMessage('second_menu_type'),
		'TYPE' => 'STRING',
		'DEFAULT' => NULL
	),
	'THIRD_MENU_TYPE' => array(
		'NAME' => Loc::getCoreMessage('third_menu_type'),
		'TYPE' => 'STRING',
		'DEFAULT' => NULL
	)
);
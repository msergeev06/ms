<?php
/**
 * Компонент ядра ms:table
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

use Ms\Core\Entity\Application;
use Ms\Core\Lib\Loc;
$request = Application::getInstance()->getContext()->getRequest();

$page = $request->get('page');
if (is_null($page))
{
	$page = 1;
}

Loc::includeLocFile(__FILE__);

return array(
	'TABLE_HEADER' => array(
		'NAME' => Loc::getCoreMessage('table_header'),
		'TYPE' => 'STRING',
		'DEFAULT' => array()
	),
	'TABLE_DATA' => array(
		'NAME' => Loc::getCoreMessage('table_data'),
		'TYPE' => 'STRING',
		'DEFAULT' => array()
	),
	'TABLE_FOOTER' => array(
		'NAME'=> Loc::getCoreMessage('table_footer'),
		'TYPE' => 'STRING',
		'DEFAULT' => array()
	)
);
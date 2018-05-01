<?php
/**
 * Компонент ядра ms:form
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
	'FORM_CLASS' => array(
		'NAME' => Loc::getCoreMessage('form_class'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'form-horizontal'
	),
	'FORM_NAME' => array(
		'NAME' => Loc::getCoreMessage('form_name'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'form'
	),
	'FORM_METHOD' => array(
		'NAME' => Loc::getCoreMessage('form_method'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'post'
	),
	'FORM_ACTION' => array(
		'NAME' => Loc::getCoreMessage('form_action'),
		'TYPE' => 'STRING',
		'DEFAULT' => ''
	),
	'FORM_SUBMIT_NAME' => array(
		'NAME' => Loc::getCoreMessage('form_submit_name'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'SUBMIT'
	),
	'FORM_SUBMIT_CLASS' => array(
		'NAME' => Loc::getCoreMessage('form_submit_class'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'submit btn btn-success'
	),
	'FORM_FIELDS' => array(
		'NAME' => Loc::getCoreMessage('form_fields'),
		'TYPE' => 'LIST',
		'DEFAULT' => array()
	),
	'FORM_HANDLER' => array(
		'NAME' => Loc::getCoreMessage('form_handler'),
		'TYPE' => 'STRING',
		'DEFAULT' => NULL
	),
	'SHOW_FORM_IF_OK' => array(
		'NAME' => Loc::getCoreMessage('show_form_if_ok'),
		'TYPE' => 'BOOL',
		'DEFAULT' => false
	),
	'REDIRECT_IF_OK' => array(
		'NAME' => Loc::getCoreMessage('redirect_if_ok'),
		'TYPE' => 'STRING',
		'DEFAULT' => ''
	)
);
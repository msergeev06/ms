<?php
/**
 * Описание таблицы групп пользователей
 *
 * @package Ms\Core
 * @subpackage Tables
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Lib\DataManager;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\TableHelper;
use Ms\Core\Lib\Loc;
use Ms\Core\Entity\Type\Date;

Loc::includeLocFile(__FILE__);

class UserGroupsTable extends DataManager
{
	public static function getTableTitle ()
	{
		return Loc::getCoreMessage('table_title');
	}

	public static function getTableLinks ()
	{
		return array (
			'ID' => array (
				UserToGroupTable::getTableName() => 'GROUP_ID'
			)
		);
	}

	protected static function getMap ()
	{
		return array (
			TableHelper::primaryField(),
			TableHelper::activeField(),
			TableHelper::sortField(),
			new Fields\StringField('NAME',array (
				'required' => true,
				'title' => Loc::getCoreMessage('field_name')
			)),
			new Fields\StringField('CODE',array (
				'unique' => true,
				'title' => Loc::getCoreMessage('field_code')
			)),
			new Fields\DateTimeField('CREATED',array (
				'required' => true,
				'default_insert' => new Date(),
				'title' => Loc::getCoreMessage('field_created')
			)),
			new Fields\DateTimeField('UPDATED',array (
				'required' => true,
				'default_insert' => new Date(),
				'default_update' => new Date(),
				'title' => Loc::getCoreMessage('field_updated')
			))
		);
	}

	public static function getValues ()
	{
		return array(
			array (
				'ID' => 1,
				'SORT' => 10,
				'NAME' => Loc::getCoreMessage('value_admin'),
				'CODE' => 'ADMIN'
			),
			array(
				'ID' => 2,
				'SORT' => 15,
				'NAME' => Loc::getCoreMessage('value_all'),
				'CODE' => 'ALL'
			)
		);
	}
}
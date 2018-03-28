<?php
/**
 * Описание таблицы привязки пользователей к группам
 * (многие ко многим)
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

class UserToGroupTable extends DataManager
{
	public static function getTableTitle ()
	{
		return Loc::getCoreMessage('table_title');
	}

	protected static function getMap ()
	{
		return array (
			TableHelper::primaryField(),
			new Fields\IntegerField('USER_ID',array (
				'required' => true,
				'link' => UsersTable::getTableName().'.ID',
				'title' => Loc::getCoreMessage('field_user_id')
			)),
			new Fields\IntegerField('GROUP_ID',array (
				'required' => true,
				'link' => UserGroupsTable::getTableName().'.ID',
				'title' => Loc::getCoreMessage('field_group_id')
			)),
			new Fields\DateTimeField('ACTIVE_FROM',array (
				'title' => Loc::getCoreMessage('field_active_from')
			)),
			new Fields\DateTimeField('ACTIVE_TO',array (
				'title' => Loc::getCoreMessage('field_active_to')
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
		return array (
			array(//Пользователь 1 всегда админ
				'USER_ID' => 1,
				'GROUP_ID' => 1
			),
			array(//Пользователь 2 (guest) всегда "Все пользователи"
				'USER_ID' => 2,
				'GROUP_ID' => 2
			)
		);
	}
}
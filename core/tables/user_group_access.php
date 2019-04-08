<?php
/**
 * @since 0.2.0
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\DataManager;
use Ms\Core\Lib\TableHelper;

class UserGroupAccessTable extends DataManager
{
	public static function getTableTitle ()
	{
		return 'Права доступа для групп пользователей';
	}

	protected static function getMap ()
	{
		return array (
			TableHelper::primaryField(),
			new Fields\StringField('MODULE_NAME',array (
				'required' => true,
				'title' => 'Код модуля'
			)),
			new Fields\IntegerField(
				'USER_GROUP_ID',
				array (
				'required' => true,
				'title' => 'ID группы пользователей'
				),
				UserGroupsTable::getTableName().'.ID',
				'cascade',
				'cascade'
			),
			new Fields\StringField('ACCESS_CODE',array (
				'required' => true,
				'size' => 1,
				'title' => 'Однобуквенный код доступа'
			))
		);
	}

}
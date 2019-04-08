<?php

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\DataManager;
use Ms\Core\Lib\TableHelper;

class UserGroupModulesAccessTable extends DataManager
{
	public static function getTableTitle ()
	{
		return 'Права доступа для групп сторонних модулей';
	}

	protected static function getMap ()
	{
		return array (
			TableHelper::primaryField(),
			new Fields\StringField('MODULE_NAME',array (
				'required' => true,
				'title' => 'Модуль, добавивший правило'
			)),
			new Fields\StringField('ACCESS_NAME',array(
				'required' => true,
				'title' => 'Код доступа'
			)),
			new Fields\IntegerField(
				'GROUP_ID',
				array (
					'required' => true,
					'title' => 'Группа пользователей, для которой добавлено правило'
				),
				UserGroupsTable::getTableName().'.ID',
				'cascade',
				'cascade'
			),
			new Fields\TextField('ACCESS_CODE',array (
				'serialized' => true,
				'title' => 'Список установленных прав для группы'
			))
		);
	}

}
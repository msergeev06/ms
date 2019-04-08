<?php

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\DataManager;
use Ms\Core\Lib\TableHelper;

class UserOptionsTable extends DataManager
{
	public static function getTableTitle ()
	{
		return 'Параметры пользователей';
	}

	protected static function getMap ()
	{
		return array (
			TableHelper::primaryField(),
			new Fields\IntegerField('USER_ID',array (
				'required' => true,
				'link' => UsersTable::getTableName().'.ID',
				'on_delete' => 'cascade',
				'title' => 'ID пользователя'
			)),
			new Fields\StringField('NAME',array (
				'required' => true,
				'title' => 'Название параметра'
			)),
			new Fields\TextField('VALUE',array (
				'title' => 'Значение параметра пользователя'
			))
		);
	}

}
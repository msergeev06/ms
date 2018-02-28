<?php

namespace Ms\Core\Tables;

use Ms\Core\Lib;
use Ms\Core\Entity\Db\Fields;

class UsersPropertiesTable extends Lib\DataManager
{
	public static function getTableTitle ()
	{
		return 'Свойства пользователей';
	}

	protected static function getMap ()
	{
		return array(
			Lib\TableHelper::primaryField(),
			new Fields\IntegerField('USER_ID',array(
				'required' => true,
				'link' => 'ms_core_users.ID',
				'title' => 'ID пользователя'
			)),
			new Fields\StringField('PROPERTY_NAME',array(
				'required' => true,
				'title' => 'Код свойства'
			)),
			new Fields\TextField('PROPERTY_VALUE',array(
				'title' => 'Значение свойства'
			))
		);
	}
}
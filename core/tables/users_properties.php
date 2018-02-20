<?php

namespace MSergeev\Core\Tables;

use MSergeev\Core\Lib;
use MSergeev\Core\Entity\Db\Fields;

class UsersPropertiesTable extends Lib\DataManager
{
	public static function getTableTitle ()
	{
		return 'Свойства пользователей';
	}

	public static function getMap ()
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
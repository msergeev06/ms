<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity\Db\Fields;

class UsersTable extends DataManager
{
	public static function getTableTitle ()
	{
		return 'Пользователи';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_core_users_properties' => 'USER_ID'
			)
		);
	}

	public static function getMap ()
	{
		return array(
			new Fields\IntegerField('ID',array(
				'primary' => true,
				'autocomplete' => true,
				'title' => 'ID пользователя'
			)),
			new Fields\BooleanField('ACTIVE',array(
				'required' => true,
				'default_value' => true,
				'title' => 'Активность'
			)),
			new Fields\StringField('LOGIN',array(
				'required' => true,
				'title' => 'Логин'
			)),
			new Fields\StringField('PASSWORD',array(
				'required' => true,
				'title' => 'Пароль'
			)),
			new Fields\StringField('EMAIL',array(
				'required' => true,
				'title' => 'Email'
			)),
			new Fields\StringField('MOBILE',array(
				'title' => 'Номер мобильного'
			)),
			new Fields\StringField('NAME',array(
				'title' => 'Краткое имя (прозвище)'
			)),
			new Fields\StringField('FIO_F',array(
				'title' => 'Фамилия'
			)),
			new Fields\StringField('FIO_I',array(
				'title' => 'Имя (полное)'
			)),
			new Fields\StringField('FIO_O',array(
				'title' => 'Отчество'
			)),
			new Fields\StringField('HASH',array(
				'title' => 'Hash'
			))
		);
	}

	public static function getValues ()
	{
		return array(
			array(
				"ID" => 1,
				"LOGIN" => "admin",
				"PASSWORD" => "123456",
				"EMAIL" => "admin@example.com",
				"NAME" => "Admin"
			),
			array(
				"ID" => 2,
				"LOGIN" => "guest",
				"PASSWORD" => "guest",
				"EMAIL" => "mail@example.com",
				"NAME" => "Гость"
			)
		);
	}
}
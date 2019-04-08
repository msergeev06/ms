<?php
/**
 * Ms\Core\Tables\UsersTable
 *
 * @package Ms\Core
 * @subpackage Tables
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Lib\DataManager;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\Users;

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

	protected static function getMap ()
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
			new Fields\IntegerField('AVATAR',array (
				'size' => 11,
				'link' => FileTable::getTableName().'.ID',
				'title' => 'Аватар пользователя'
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
				"PASSWORD" => Users::createMd5Pass('admin','admin'),
				"EMAIL" => "admin@example.com",
				"NAME" => "Админ"
			),
			array(
				"ID" => 2,
				"LOGIN" => "guest",
				"PASSWORD" => Users::createMd5Pass('guest','guest'),
				"EMAIL" => "mail@example.com",
				"NAME" => "Гость"
			)
		);
	}
}
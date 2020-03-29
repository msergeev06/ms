<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Lib;
use Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Tables\UsersPropertiesTable
 * ORM таблицы "Свойства пользователей" (ms_core_users_properties)
 */
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
			(new Fields\IntegerField('USER_ID'))
                ->setRequired()
                ->setLink(UsersTable::getTableName().'.ID')
                ->setTitle('ID пользователя')
            ,
			(new Fields\StringField('PROPERTY_NAME'))
                ->setRequired()
                ->setTitle('Код свойства')
            ,
			(new Fields\TextField('PROPERTY_VALUE'))
                ->setTitle('Значение свойства')
		);
	}
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\DataManager;
use Ms\Core\Lib\TableHelper;

/**
 * Класс Ms\Core\Tables\UserGroupAccessTable
 * ORM таблицы "Права доступа для групп пользователей" (ms_core_user_group_access)
 */
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
			(new Fields\StringField('MODULE_NAME'))
                ->setRequired()
                ->setTitle('Код модуля')
            ,
			(new Fields\IntegerField('USER_GROUP_ID'))
                ->setRequired()
                ->setTitle('ID группы пользователей')
                ->setLink(UserGroupsTable::getTableName().'.ID')
                ->setForeignOnUpdateCascade()
                ->setForeignOnDeleteCascade()
            ,
			(new Fields\StringField('ACCESS_CODE'))
                ->setRequired()
                ->setSize(1)
                ->setTitle('Однобуквенный код доступа')
		);
	}

}
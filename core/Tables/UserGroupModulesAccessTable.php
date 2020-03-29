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
 * Класс Ms\Core\Tables\UserGroupModulesAccessTable
 * ORM таблицы "Права доступа для групп сторонних модулей" (ms_core_user_group_modules_access)
 */
class UserGroupModulesAccessTable extends DataManager
{
    public static function getTableTitle()
    {
        return 'Права доступа для групп сторонних модулей';
    }

    protected static function getMap()
    {
        return [
            TableHelper::primaryField(),
            (new Fields\StringField('MODULE_NAME'))
                ->setRequired()
                ->setTitle('Модуль, добавивший правило')
            ,
            (new Fields\StringField('ACCESS_NAME'))
                ->setRequired()
                ->setTitle('Код доступа')
            ,
            (new Fields\IntegerField('GROUP_ID'))
                ->setRequired()
                ->setTitle('Группа пользователей, для которой добавлено правило')
                ->setLink(UserGroupsTable::getTableName() . '.ID')
                ->setForeignOnUpdateCascade()
                ->setForeignOnDeleteCascade()
            ,
            (new Fields\TextField('ACCESS_CODE'))
                ->setSerialized()
                ->setTitle('Список установленных прав для группы')
        ];
    }

}
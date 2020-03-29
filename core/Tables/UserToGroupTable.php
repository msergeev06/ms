<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Lib\DataManager;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\TableHelper;
use Ms\Core\Lib\Loc;
use Ms\Core\Entity\Type\Date;

Loc::includeLocFile(__FILE__);

/**
 * Класс Ms\Core\Tables\UserToGroupTable
 * ORM таблицы "Привязка пользователей к группам" (ms_core_user_to_group)
 */
class UserToGroupTable extends DataManager
{
    public static function getTableTitle()
    {
        return Loc::getCoreMessage('table_title');
    }

    protected static function getMap()
    {
        return [
            TableHelper::primaryField(),
            (new Fields\IntegerField('USER_ID'))
                ->setRequired()
                ->setLink(UsersTable::getTableName() . '.ID')
                ->setForeignOnUpdateCascade()
                ->setForeignOnDeleteCascade()
                ->setTitle(Loc::getCoreMessage('field_user_id'))
            ,
            (new Fields\IntegerField('GROUP_ID'))
                ->setRequired()
                ->setLink(UserGroupsTable::getTableName() . '.ID')
                ->setForeignOnUpdateCascade()
                ->setForeignOnDeleteCascade()
                ->setTitle(Loc::getCoreMessage('field_group_id'))
            ,
            (new Fields\DateTimeField('ACTIVE_FROM'))
                ->setTitle(Loc::getCoreMessage('field_active_from'))
            ,
            (new Fields\DateTimeField('ACTIVE_TO'))
                ->setTitle(Loc::getCoreMessage('field_active_to'))
            ,
            (new Fields\DateTimeField('CREATED'))
                ->setRequired()
                ->setDefaultInsert(new Date())
                ->setTitle(Loc::getCoreMessage('field_created'))
            ,
            (new Fields\DateTimeField('UPDATED'))
                ->setRequired()
                ->setDefaultInsert(new Date())
                ->setDefaultUpdate(new Date())
                ->setTitle(Loc::getCoreMessage('field_updated'))
        ];
    }

    public static function getValues()
    {
        return [
            [
                //Пользователь 1 всегда админ
                'USER_ID'  => 1,
                'GROUP_ID' => 1
            ],
            [
                //Пользователь 2 (guest) всегда "Все пользователи"
                'USER_ID'  => 2,
                'GROUP_ID' => 2
            ]
        ];
    }
}
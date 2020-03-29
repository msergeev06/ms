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
 * Класс Ms\Core\Tables\UserGroupsTable
 * ORM таблицы "Группы пользователей" (ms_core_user_groups)
 */
class UserGroupsTable extends DataManager
{
    public static function getTableTitle()
    {
        return Loc::getCoreMessage('table_title');
    }

    public static function getValues()
    {
        return [
            [
                'ID'   => 1,
                'SORT' => 10,
                'NAME' => Loc::getCoreMessage('value_admin'),
                'CODE' => 'ADMIN'
            ],
            [
                'ID'   => 2,
                'SORT' => 15,
                'NAME' => Loc::getCoreMessage('value_all'),
                'CODE' => 'ALL'
            ]
        ];
    }

    protected static function getMap()
    {
        return [
            TableHelper::primaryField(),
            TableHelper::activeField(),
            TableHelper::sortField(),
            (new Fields\StringField('NAME'))
                ->setRequired()
                ->setTitle(Loc::getCoreMessage('field_name'))
            ,
            (new Fields\StringField('CODE'))
                ->setUnique()
                ->setTitle(Loc::getCoreMessage('field_code'))
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
}
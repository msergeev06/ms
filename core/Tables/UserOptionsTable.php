<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\DataManager;
use Ms\Core\Lib\TableHelper;

/**
 * Класс Ms\Core\Tables\UserOptionsTable
 * ORM таблицы "Параметры пользователей" (ms_core_user_options)
 */
class UserOptionsTable extends DataManager
{
    public static function getTableTitle()
    {
        return 'Параметры пользователей';
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
                ->setTitle('ID пользователя')
            ,
            (new Fields\StringField('NAME'))
                ->setRequired()
                ->setTitle('Название параметра')
            ,
            (new Fields\TextField('VALUE'))
                ->setTitle('Значение параметра пользователя')
        ];
    }

}
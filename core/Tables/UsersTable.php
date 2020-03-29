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
use Ms\Core\Lib\Users;

/**
 * Класс Ms\Core\Tables\UsersTable
 * ORM таблицы "Пользователи" (ms_core_users)
 */
class UsersTable extends DataManager
{
    public static function getTableTitle()
    {
        return 'Пользователи';
    }

    protected static function getMap()
    {
        return [
            (new Fields\IntegerField('ID'))
                ->setPrimary()
                ->setAutocomplete()
                ->setTitle('ID пользователя')
            ,
            (new Fields\BooleanField('ACTIVE'))
                ->setRequired()
                ->setDefaultCreate(true)
                ->setDefaultInsert(true)
                ->setTitle('Активность')
            ,
            (new Fields\StringField('LOGIN'))
                ->setRequired()
                ->setTitle('Логин')
            ,
            (new Fields\StringField('PASSWORD'))
                ->setRequired()
                ->setTitle('Пароль')
            ,
            (new Fields\StringField('EMAIL'))
                ->setRequired()
                ->setTitle('Email')
            ,
            (new Fields\StringField('MOBILE'))
                ->setTitle('Номер мобильного')
            ,
            (new Fields\StringField('NAME'))
                ->setTitle('Краткое имя (прозвище)')
            ,
            (new Fields\StringField('FIO_F'))
                ->setTitle('Фамилия')
            ,
            (new Fields\StringField('FIO_I'))
                ->setTitle('Имя (полное)')
            ,
            (new Fields\StringField('FIO_O'))
                ->setTitle('Отчество')
            ,
            (new Fields\IntegerField('AVATAR'))
                ->setSize(18)
                ->setLink(FileTable::getTableName() . '.ID')
                ->setTitle('Аватар пользователя')
            ,
            (new Fields\StringField('HASH'))
                ->setTitle('Hash')
        ];
    }

    public static function getValues()
    {
        return [
            [
                "ID"       => 1,
                "LOGIN"    => "admin",
                "PASSWORD" => Users::createMd5Pass('admin', 'admin'),
                "EMAIL"    => "admin@example.com",
                "NAME"     => "Админ"
            ],
            [
                "ID"       => 2,
                "LOGIN"    => "guest",
                "PASSWORD" => Users::createMd5Pass('guest', 'guest'),
                "EMAIL"    => "mail@example.com",
                "NAME"     => "Гость"
            ]
        ];
    }
}
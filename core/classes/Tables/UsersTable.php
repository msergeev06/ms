<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Links\LinkedField;
use Ms\Core\Entity\Db\Tables\FieldsCollection;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Errors\FileLogger;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Entity\User\UserController;

/**
 * Класс Ms\Core\Tables\UsersTable
 * ORM таблицы "Пользователи" (ms_core_users)
 */
class UsersTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return 'Пользователи';
    }

    public function getMap (): FieldsCollection
    {
        return (new FieldsCollection())
            ->addField(
                (new Fields\IntegerField('ID'))
                    ->setPrimary()
                    ->setAutocomplete()
                    ->setTitle('ID пользователя')
            )
            ->addField(
                (new Fields\BooleanField('ACTIVE'))
                    ->setRequired()
                    ->setDefaultCreate(true)
                    ->setDefaultInsert(true)
                    ->setTitle('Активность')
            )
            ->addField(
                (new Fields\StringField('LOGIN'))
                    ->setRequired()
                    ->setTitle('Логин')
            )
            ->addField(
                (new Fields\StringField('PASSWORD'))
                    ->setRequired()
                    ->setTitle('Пароль')
            )
            ->addField(
                (new Fields\StringField('EMAIL'))
                    ->setRequired()
                    ->setTitle('Email')
            )
            ->addField(
                (new Fields\StringField('MOBILE'))
                    ->setTitle('Номер мобильного')
            )
            ->addField(
                (new Fields\StringField('NAME'))
                    ->setTitle('Краткое имя (прозвище)')
            )
            ->addField(
                (new Fields\StringField('FIO_F'))
                    ->setTitle('Фамилия')
            )
            ->addField(
                (new Fields\StringField('FIO_I'))
                    ->setTitle('Имя (полное)')
            )
            ->addField(
                (new Fields\StringField('FIO_O'))
                    ->setTitle('Отчество')
            )
            ->addField(
                (new Fields\IntegerField('AVATAR'))
                    ->setSize(18)
                    ->setLink((new LinkedField((new FileTable()), 'ID')))
                    ->setTitle('Аватар пользователя')
            )
            ->addField(
                (new Fields\DateTimeField('LAST_ACTIVITY'))
                    ->setRequired()
                    ->setDefaultInsert(new Date())
                    ->setDefaultUpdate(new Date())
                    ->setTitle('Последняя активность')
            )
            ->addField(
                (new Fields\StringField('HASH'))
                    ->setTitle('Hash')
            )
        ;
    }

    public function onBeforeUpdate ($primary, &$arUpdate, &$sSqlWhere = null): bool
    {
        if (array_key_exists('HASH',$arUpdate))
        {
/*            $logger = (new FileLogger('core'))
                ->setTypeDebug()
            ;
            $logger->addMessage(print_r(debug_backtrace(),true));*/
        }

        return parent::onBeforeUpdate($primary, $arUpdate, $sSqlWhere); // TODO: Change the autogenerated stub
    }

    public function getDefaultRowsArray (): array
    {
        return [
            [
                "ID"       => 1,
                "LOGIN"    => "admin",
                "PASSWORD" => UserController::getInstance()->createMd5Pass('admin', 'admin'),
                "EMAIL"    => "admin@example.com",
                "NAME"     => "Админ"
            ],
            [
                "ID"       => 2,
                "LOGIN"    => "guest",
                "PASSWORD" => UserController::getInstance()->createMd5Pass('guest', 'guest'),
                "EMAIL"    => "mail@example.com",
                "NAME"     => "Гость"
            ]
        ];
    }
}
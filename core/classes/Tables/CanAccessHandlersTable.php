<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Tables\FieldsCollection;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Tables\CanAccessHandlersTable
 * Таблица списка обработчиков определенных прав доступа для групп пользователей
 */
class CanAccessHandlersTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return 'Обработчики прав групп пользователей';
    }

    public function getMap (): FieldsCollection
    {
        return (new FieldsCollection())
            ->addField(
                (new Fields\StringField('CODE'))
                    ->setPrimary()
                    ->setTitle('Код доступа')
            )
            ->addField(
                (new Fields\StringField('MODULE'))
                    ->setRequired()
                    ->setTitle('Имя модуля, обработчика доступа')
            )
            ->addField(
                (new Fields\StringField('HANDLER'))
                    ->setRequired()
                    ->setTitle('Класс обработчик доступа')
            )
        ;
    }
}
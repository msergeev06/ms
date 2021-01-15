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
use Ms\Core\Entity\Helpers\TableHelper;

/**
 * Класс Ms\Core\Tables\UsersPropertiesTable
 * ORM таблицы "Свойства пользователей" (ms_core_users_properties)
 */
class UsersPropertiesTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return 'Свойства пользователей';
    }

    public function getMap (): FieldsCollection
    {
        return (new FieldsCollection())
            ->addField(
                TableHelper::getInstance()->primaryField()
            )
            ->addField(
                (new Fields\IntegerField('USER_ID'))
                    ->setRequired()
                    ->setLink(
                        (new LinkedField(
                            (new UsersTable()),
                            'ID'
                        ))
                    )
                    ->setTitle('ID пользователя')
            )
            ->addField(
                (new Fields\StringField('PROPERTY_NAME'))
                    ->setRequired()
                    ->setTitle('Код свойства')
            )
            ->addField(
                (new Fields\TextField('PROPERTY_VALUE'))
                    ->setTitle('Значение свойства')
            )
        ;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Links\ForeignKey;
use Ms\Core\Entity\Db\Links\LinkedField;
use Ms\Core\Entity\Db\Tables\FieldsCollection;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Helpers\TableHelper;

/**
 * Класс Ms\Core\Tables\UserGroupModulesAccessTable
 * ORM таблицы "Права доступа для групп сторонних модулей" (ms_core_user_group_modules_access)
 */
class UserGroupModulesAccessTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return 'Права доступа для групп сторонних модулей';
    }

    public function getMap (): FieldsCollection
    {
        return (new FieldsCollection())
            ->addField(
                TableHelper::getInstance()->primaryField()
            )
            ->addField(
                (new Fields\StringField('MODULE_NAME'))
                    ->setRequired()
                    ->setTitle('Модуль, добавивший правило')
            )
            ->addField(
                (new Fields\StringField('ACCESS_NAME'))
                    ->setRequired()
                    ->setTitle('Код доступа')
            )
            ->addField(
                (new Fields\IntegerField('GROUP_ID'))
                    ->setRequired()
                    ->setTitle('Группа пользователей, для которой добавлено правило')
                    ->setLink(
                        (new LinkedField(
                            (new UserGroupsTable()),
                            'ID',
                            (new ForeignKey())
                                ->setOnUpdateCascade()
                                ->setOnDeleteCascade()
                        ))
                    )
            )
            ->addField(
                (new Fields\TextField('ACCESS_CODE'))
                    ->setSerialized()
                    ->setTitle('Список установленных прав для группы')
            )
        ;
    }
}
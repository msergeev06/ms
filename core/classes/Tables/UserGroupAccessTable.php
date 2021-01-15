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
 * Класс Ms\Core\Tables\UserGroupAccessTable
 * ORM таблицы "Права доступа для групп пользователей" (ms_core_user_group_access)
 */
class UserGroupAccessTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return 'Права доступа для групп пользователей';
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
                    ->setTitle('Код модуля')
            )
            ->addField(
                (new Fields\IntegerField('USER_GROUP_ID'))
                    ->setRequired()
                    ->setTitle('ID группы пользователей')
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
                (new Fields\StringField('ACCESS_CODE'))
                    ->setRequired()
                    ->setSize(1)
                    ->setTitle('Однобуквенный код доступа')
            )
        ;
    }
}
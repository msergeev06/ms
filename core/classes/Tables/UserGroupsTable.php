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
use Ms\Core\Entity\Type\Date;
use Ms\Core\Entity\Helpers\TableHelper;

\IncludeLangFile(__FILE__);

/**
 * Класс Ms\Core\Tables\UserGroupsTable
 * ORM таблицы "Группы пользователей" (ms_core_user_groups)
 */
class UserGroupsTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return \GetCoreMessage('table_title');
    }

    public function getMap (): FieldsCollection
    {
        $helper = TableHelper::getInstance();
        return (new FieldsCollection())
            ->addField(
                $helper->primaryField()
            )
            ->addField(
                $helper->activeField()
            )
            ->addField(
                $helper->sortField()
            )
            ->addField(
                (new Fields\StringField('NAME'))
                    ->setRequired()
                    ->setTitle(\GetCoreMessage('field_name'))
            )
            ->addField(
                (new Fields\StringField('CODE'))
                    ->setUnique()
                    ->setTitle(\GetCoreMessage('field_code'))
            )
            ->addField(
                (new Fields\DateTimeField('CREATED'))
                    ->setRequired()
                    ->setDefaultInsert(new Date())
                    ->setTitle(\GetCoreMessage('field_created'))
            )
            ->addField(
                (new Fields\DateTimeField('UPDATED'))
                    ->setRequired()
                    ->setDefaultInsert(new Date())
                    ->setDefaultUpdate(new Date())
                    ->setTitle(\GetCoreMessage('field_updated'))
            )
        ;
    }

    public function getDefaultRowsArray (): array
    {
        return [
            [
                'ID'   => 1,
                'SORT' => 10,
                'NAME' => \GetCoreMessage('value_admin'),
                'CODE' => 'ADMIN'
            ],
            [
                'ID'   => 2,
                'SORT' => 15,
                'NAME' => \GetCoreMessage('value_all'),
                'CODE' => 'ALL'
            ]
        ];
    }
}
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
use Ms\Core\Entity\Type\Date;

\IncludeLangFile(__FILE__);

/**
 * Класс Ms\Core\Tables\UserToGroupTable
 * ORM таблицы "Привязка пользователей к группам" (ms_core_user_to_group)
 */
class UserToGroupTable extends TableAbstract
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
                (new Fields\IntegerField('USER_ID'))
                    ->setRequired()
                    ->setLink(
                        (new LinkedField(
                            (new UsersTable()),
                            'ID',
                            (new ForeignKey())
                                ->setOnUpdateCascade()
                                ->setOnDeleteCascade()
                        ))
                    )
                    ->setTitle(\GetCoreMessage('field_user_id'))
            )
            ->addField(
                (new Fields\IntegerField('GROUP_ID'))
                    ->setRequired()
                    ->setLink(
                        (new LinkedField(
                            (new UserGroupsTable()),
                            'ID',
                            (new ForeignKey())
                                ->setOnUpdateCascade()
                                ->setOnDeleteCascade()
                        ))
                    )
                    ->setTitle(\GetCoreMessage('field_group_id'))
            )
            ->addField(
                (new Fields\DateTimeField('ACTIVE_FROM'))
                    ->setTitle(\GetCoreMessage('field_active_from'))
            )
            ->addField(
                (new Fields\DateTimeField('ACTIVE_TO'))
                    ->setTitle(\GetCoreMessage('field_active_to'))
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
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
 * Класс Ms\Core\Tables\UserOptionsTable
 * ORM таблицы "Параметры пользователей" (ms_core_user_options)
 */
class UserOptionsTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return 'Параметры пользователей';
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
                            'ID',
                            (new ForeignKey())
                                ->setOnUpdateCascade()
                                ->setOnDeleteCascade()
                        ))
                    )
                    ->setTitle('ID пользователя')
            )
            ->addField(
                (new Fields\StringField('NAME'))
                    ->setRequired()
                    ->setTitle('Название параметра')
            )
            ->addField(
                (new Fields\TextField('VALUE'))
                    ->setTitle('Значение параметра пользователя')
            )
        ;
    }
}
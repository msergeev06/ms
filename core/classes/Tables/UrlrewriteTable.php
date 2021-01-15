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
use Ms\Core\Entity\Helpers\TableHelper;

/**
 * Класс Ms\Core\Tables\UrlrewriteTable
 * ORM таблицы "Обработка адресов" (ms_core_urlrewrite)
 */
class UrlrewriteTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return 'Обработка адресов';
    }

    public function getMap (): FieldsCollection
    {
        return (new FieldsCollection())
            ->addField(
                TableHelper::getInstance()->primaryField()
            )
            ->addField(
                (new Fields\StringField('COMPONENT_NAME'))
                    ->setTitle('Имя компонента, создавшего правило')
            )
            ->addField(
                (new Fields\StringField('CONDITION'))
                    ->setRequired()
                    ->setTitle('Условие')
            )
            ->addField(
                (new Fields\StringField('RULE'))
                    ->setRequired()
                    ->setTitle('Правило')
            )
            ->addField(
                (new Fields\StringField('PATH'))
                    ->setRequired()
                    ->setTitle('Путь')
            )
        ;
    }
}
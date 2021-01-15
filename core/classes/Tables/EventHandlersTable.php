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
use Ms\Core\Entity\Helpers\TableHelper;
use Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Tables\EventHandlersTable
 * ORM таблицы "Обработчики событий" (ms_core_event_handlers)
 */
class EventHandlersTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return 'Обработчики событий';
    }

    public function getMap (): FieldsCollection
    {
        return (new FieldsCollection())
            ->addField(
                TableHelper::getInstance()->primaryField()
            )
            ->addField(
                TableHelper::getInstance()->sortField()
                           ->setDefaultCreate(100)
                           ->setDefaultInsert(100)
            )
            ->addField(
                (new Fields\StringField('FROM_MODULE'))
                    ->setRequired()
                    ->setTitle('Идентификатор модуля инициирующий событие')
            )
            ->addField(
                (new Fields\StringField('EVENT_ID'))
                    ->setRequired()
                    ->setTitle('Идентификатор события')
            )
            ->addField(
                (new Fields\StringField('TO_MODULE_ID'))
                    ->setTitle('Идентификатор модуля содержащий функцию-обработчик события')
            )
            ->addField(
                (new Fields\StringField('TO_CLASS'))
                    ->setRequired()
                    ->setTitle('Класс принадлежащий модулю TO_MODULE_ID')
            )
            ->addField(
                (new Fields\StringField('TO_METHOD'))
                    ->setRequired()
                    ->setTitle('Метод класса $toClass являющийся функцией-обработчиком события')
            )
            ->addField(
                (new Fields\StringField('TO_PATH'))
                    ->setTitle('Относительный путь к исполняемому файлу')
            )
            ->addField(
                (new Fields\TextField('TO_METHOD_ARG'))
                    ->setSerialized()
                    ->setTitle('Массив аргументов для функции-обработчика событий')
            )
        ;
    }
}
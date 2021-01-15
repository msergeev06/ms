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
 * Класс Ms\Core\Tables\FileTable
 * ORM таблицы "Загруженные файлы" (ms_core_file)
 */
class FileTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return 'Загруженные файлы';
    }

    public function getMap (): FieldsCollection
    {
        return (new FieldsCollection())
            ->addField(
                (new Fields\IntegerField('ID'))
                    ->setPrimary()
                    ->setAutocomplete()
                    ->setSize(18)
                    ->setTitle('ID файла')
            )
            ->addField(
                (new Fields\StringField('MODULE'))
                    ->setSize(50)
                    ->setTitle('Имя модуля, чей файл')
            )
            ->addField(
                (new Fields\IntegerField('HEIGHT'))
                    ->setSize(18)
                    ->setTitle('Высота изображения')
            )
            ->addField(
                (new Fields\IntegerField('WIDTH'))
                    ->setSize(18)
                    ->setTitle('Ширина изображения')
            )
            ->addField(
                (new Fields\BigIntField('FILE_SIZE'))
                    ->setTitle('Размер файла в байтах')
            )
            ->addField(
                (new Fields\StringField('CONTENT_TYPE'))
                    ->setTitle('Тип файла')
            )
            ->addField(
                (new Fields\StringField('SUBDIR'))
                    ->setTitle('Поддиректория')
            )
            ->addField(
                (new Fields\StringField('FILE_NAME'))
                    ->setRequired()
                    ->setTitle('Имя файла')
            )
            ->addField(
                (new Fields\StringField('ORIGINAL_NAME'))
                    ->setTitle('Оригинальное имя файла')
            )
            ->addField(
                (new Fields\StringField('DESCRIPTION'))
                    ->setTitle('Описание файла')
            )
            ->addField(
                (new Fields\StringField('HANDLER_ID'))
                    ->setTitle('Обработчик')
            )
            ->addField(
                (new Fields\StringField('EXTERNAL_ID'))
                    ->setTitle('Внешний код')
            )
        ;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib;

/**
 * Класс Ms\Core\Tables\FileTable
 * ORM таблицы "Загруженные файлы" (ms_core_file)
 */
class FileTable extends Lib\DataManager
{
	public static function getTableTitle ()
	{
		return 'Загруженные файлы';
	}

	protected static function getMap ()
	{
		return [
			(new Fields\IntegerField('ID'))
                ->setPrimary()
                ->setAutocomplete()
                ->setSize(18)
                ->setTitle('ID файла')
            ,
			(new Fields\StringField('MODULE'))
                ->setSize(50)
                ->setTitle('Имя модуля, чей файл')
            ,
			(new Fields\IntegerField('HEIGHT'))
                ->setSize(18)
                ->setTitle('Высота изображения')
            ,
			(new Fields\IntegerField('WIDTH'))
                ->setSize(18)
                ->setTitle('Ширина изображения')
            ,
			(new Fields\BigIntField('FILE_SIZE'))
                ->setTitle('Размер файла в байтах')
            ,
			(new Fields\StringField('CONTENT_TYPE'))
                ->setTitle('Тип файла')
            ,
			(new Fields\StringField('SUBDIR'))
                ->setTitle('Поддиректория')
            ,
			(new Fields\StringField('FILE_NAME'))
                ->setRequired()
                ->setTitle('Имя файла')
            ,
			(new Fields\StringField('ORIGINAL_NAME'))
                ->setTitle('Оригинальное имя файла')
            ,
			(new Fields\StringField('DESCRIPTION'))
                ->setTitle('Описание файла')
            ,
			(new Fields\StringField('HANDLER_ID'))
                ->setTitle('Обработчик')
            ,
			(new Fields\StringField('EXTERNAL_ID'))
                ->setTitle('Внешний код')
        ];
	}
}
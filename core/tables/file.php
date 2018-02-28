<?php

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib;

class FileTable extends Lib\DataManager
{
	public static function getTableTitle ()
	{
		return 'Загруженные файлы';
	}

	protected static function getMap ()
	{
		return array(
			new Fields\IntegerField('ID',array(
				'primary' => true,
				'autocomplete' => true,
				'size' => 18,
				'title' => 'ID файла'
			)),
			new Fields\StringField('MODULE',array(
				'size' => 50,
				'title' => 'Имя модуля, чей файл'
			)),
			new Fields\IntegerField('HEIGHT',array(
				'size' => 18,
				'title' => 'Высота изображения'
			)),
			new Fields\IntegerField('WIDTH',array(
				'size' => 18,
				'title' => 'Ширина изображения'
			)),
			new Fields\BigIntField('FILE_SIZE',array(
				'title' => 'Размер файла в байтах'
			)),
			new Fields\StringField('CONTENT_TYPE',array(
				'title' => 'Тип файла'
			)),
			new Fields\StringField('SUBDIR',array(
				'title' => 'Поддиректория'
			)),
			new Fields\StringField('FILE_NAME',array(
				'required' => true,
				'title' => 'Имя файла'
			)),
			new Fields\StringField('ORIGINAL_NAME',array(
				'title' => 'Оригинальное имя файла'
			)),
			new Fields\StringField('DESCRIPTION',array(
				'title' => 'Описание файла'
			)),
			new Fields\StringField('HANDLER_ID',array(
				'title' => 'Обработчик'
			)),
			new Fields\StringField('EXTERNAL_ID',array(
				'title' => 'Внешний код'
			))
		);
	}
}
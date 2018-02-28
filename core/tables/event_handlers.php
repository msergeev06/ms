<?php

namespace Ms\Core\Tables;

use Ms\Core\Lib\DataManager;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\TableHelper;

class EventHandlersTable extends DataManager
{
	public static function getTableTitle ()
	{
		return 'Обработчики событий';
	}

	protected static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			TableHelper::sortField(array('default_value'=>100)),
			new Fields\StringField('FROM_MODULE',array(
				'required' => true,
				'title' => 'Идентификатор модуля инициирующий событие'
			)),
			new Fields\StringField('EVENT_ID',array(
				'required' => true,
				'title' => 'Идентификатор события'
			)),
			new Fields\StringField('TO_MODULE_ID',array(
				'title' => 'Идентификатор модуля содержащий функцию-обработчик события'
			)),
			new Fields\StringField('TO_CLASS',array(
				'title' => 'Класс принадлежащий модулю TO_MODULE_ID'
			)),
			new Fields\StringField('TO_METHOD',array(
				'title' => 'Метод класса $toClass являющийся функцией-обработчиком события'
			)),
			new Fields\StringField('TO_PATH',array(
				'title' => 'Относительный путь к исполняемому файлу'
			)),
			new Fields\StringField('FULL_PATH',array(
				'title' => 'Полный путь к исполняемому файлу'
			)),
			new Fields\TextField('TO_METHOD_ARG',array(
				'serialized' => true,
				'title' => 'Массив аргументов для функции-обработчика событий'
			))
		);
	}
}
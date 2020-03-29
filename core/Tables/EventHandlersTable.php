<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Lib\DataManager;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\TableHelper;

/**
 * Класс Ms\Core\Tables\EventHandlersTable
 * ORM таблицы "Обработчики событий" (ms_core_event_handlers)
 */
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
			TableHelper::sortField()
                ->setDefaultCreate(100)
                ->setDefaultInsert(100)
            ,
            (new Fields\StringField('FROM_MODULE'))
                ->setRequired()
                ->setTitle('Идентификатор модуля инициирующий событие')
            ,
			(new Fields\StringField('EVENT_ID'))
                ->setRequired()
                ->setTitle('Идентификатор события')
            ,
			(new Fields\StringField('TO_MODULE_ID'))
                ->setTitle('Идентификатор модуля содержащий функцию-обработчик события')
            ,
			(new Fields\StringField('TO_CLASS'))
                ->setTitle('Класс принадлежащий модулю TO_MODULE_ID')
            ,
			(new Fields\StringField('TO_METHOD'))
                ->setTitle('Метод класса $toClass являющийся функцией-обработчиком события')
            ,
			(new Fields\StringField('TO_PATH'))
                ->setTitle('Относительный путь к исполняемому файлу')
            ,
			(new Fields\StringField('FULL_PATH'))
                ->setTitle('Полный путь к исполняемому файлу')
            ,
			(new Fields\TextField('TO_METHOD_ARG'))
                ->setSerialized()
                ->setTitle('Массив аргументов для функции-обработчика событий')
		);
	}
}
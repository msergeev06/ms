<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2019 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\DataManager;
use Ms\Core\Lib\TableHelper;

/**
 * Класс Ms\Core\Tables\UrlrewriteTable
 * ORM таблицы "Обработка адресов" (ms_core_urlrewrite)
 */
class UrlrewriteTable extends DataManager
{
	public static function getTableTitle ()
	{
		return 'Обработка адресов';
	}

	protected static function getMap ()
	{
		return array (
			TableHelper::primaryField(),
			(new Fields\StringField('COMPONENT_NAME'))
                ->setTitle('Имя компонента, создавшего правило')
            ,
			(new Fields\StringField('CONDITION'))
                ->setRequired()
                ->setTitle('Условие')
            ,
			(new Fields\StringField('RULE'))
                ->setRequired()
                ->setTitle('Правило')
            ,
			(new Fields\StringField('PATH'))
                ->setRequired()
                ->setTitle('Путь')
		);
	}

}
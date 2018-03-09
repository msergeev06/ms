<?php
/**
 * Ms\Core\Tables\OptionsTable
 *
 * @package Ms\Core
 * @subpackage Tables
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\DataManager;

class OptionsTable extends DataManager
{
	public static function getTableTitle()
	{
		return "Таблица настроек";
	}

	protected static function getMap ()
	{
		return array(
			new Fields\IntegerField ('ID', array(
				"primary" => true,
				"autocomplete" => true,
				"title" => 'ID настройки'
			)),
			new Fields\StringField ('NAME', array(
				"title" => 'Имя настройки'
			)),
			new Fields\StringField ('VALUE', array(
				"title" => "Значение настройки"
			))
		);
	}

	public static function getValues ()
	{
		return array (
			array (
				'NAME' => 'MS_CORE_SORT_DEFAULT',
				'VALUE' => 500
			)
		);
	}

}
<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Tables;

use MSergeev\Core\Entity\Db\Fields;
use MSergeev\Core\Lib\DataManager;

class OptionsTable extends DataManager
{
	public static function getTableTitle()
	{
		return "Таблица настроек";
	}

	public static function getMap ()
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

}
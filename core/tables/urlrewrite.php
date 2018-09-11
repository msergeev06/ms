<?php

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\DataManager;
use Ms\Core\Lib\TableHelper;

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
			new Fields\StringField('COMPONENT_NAME',array (
				'title' => 'Имя компонента, создавшего правило'
			)),
			new Fields\StringField('CONDITION',array (
				'required' => true,
				'title' => 'Условие'
			)),
			new Fields\StringField('RULE',array(
				'required' => true,
				'title' => 'Правило'
			)),
			new Fields\StringField('PATH',array (
				'required' => true,
				'title' => 'Путь'
			))
		);
	}

}
<?php
/**
 * Ms\Core\Tables\SectionsTable
 *
 * @package Ms\Core
 * @subpackage Tables
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Lib;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Query;

class SectionsTable extends Lib\DataManager
{
	public static function getTableTitle()
	{
		return 'Разделы';
	}
	public static function getTableLinks()
	{
		return array(
			'ID' => array(
				static::getTableName() => 'PARENT_SECTION_ID'
			)
		);
	}
	protected static function getMap()
	{
		$arTreeFields = parent::getMap();

		$arSectionFields = [
			Lib\TableHelper::activeField(),
			Lib\TableHelper::sortField(),
			new Fields\StringField('NAME',array(
				'required' => true,
				'title' => 'Название раздела'
			)),
			new Fields\IntegerField('PARENT_SECTION_ID',array(
				'required' => true,
				'default_create' => 0,
				'default_insert' => 0,
				//'link' => 'ms_core_sections.ID',
				'link' => static::getTableName().'.ID',
				'title' => 'Родительский раздел'
			))
		];

		return array_merge($arTreeFields, $arSectionFields);
	}

	/**
	 * Тестовые данные для отладки
	 *
	 * @return array
	 */
	public static function getValues ()
	{
		return array(
			array(
				'ID' => 1,
				'NAME' => 'Узел 1',
				'LEFT_MARGIN' => 1,
				'RIGHT_MARGIN' => 32,
				'DEPTH_LEVEL' => 1,
				'PARENT_SECTION_ID' => 0
			),
			array(
				'ID' => 2,
				'NAME' => 'Узел 2',
				'LEFT_MARGIN' => 2,
				'RIGHT_MARGIN' => 9,
				'DEPTH_LEVEL' => 2,
				'PARENT_SECTION_ID' => 1
			),
			array(
				'ID' => 3,
				'NAME' => 'Узел 3',
				'LEFT_MARGIN' => 10,
				'RIGHT_MARGIN' => 23,
				'DEPTH_LEVEL' => 2,
				'PARENT_SECTION_ID' => 1
			),
			array(
				'ID' => 4,
				'NAME' => 'Узел 4',
				'LEFT_MARGIN' => 24,
				'RIGHT_MARGIN' => 31,
				'DEPTH_LEVEL' => 2,
				'PARENT_SECTION_ID' => 1
			),
			array(
				'ID' => 5,
				'NAME' => 'Узел 5',
				'LEFT_MARGIN' => 3,
				'RIGHT_MARGIN' => 8,
				'DEPTH_LEVEL' => 3,
				'PARENT_SECTION_ID' => 2
			),
			array(
				'ID' => 6,
				'NAME' => 'Узел 6',
				'LEFT_MARGIN' => 11,
				'RIGHT_MARGIN' => 12,
				'DEPTH_LEVEL' => 3,
				'PARENT_SECTION_ID' => 3
			),
			array(
				'ID' => 7,
				'NAME' => 'Узел 7',
				'LEFT_MARGIN' => 13,
				'RIGHT_MARGIN' => 20,
				'DEPTH_LEVEL' => 3,
				'PARENT_SECTION_ID' => 3
			),
			array(
				'ID' => 8,
				'NAME' => 'Узел 8',
				'LEFT_MARGIN' => 21,
				'RIGHT_MARGIN' => 22,
				'DEPTH_LEVEL' => 3,
				'PARENT_SECTION_ID' => 3
			),
			array(
				'ID' => 9,
				'NAME' => 'Узел 9',
				'LEFT_MARGIN' => 25,
				'RIGHT_MARGIN' => 30,
				'DEPTH_LEVEL' => 3,
				'PARENT_SECTION_ID' => 4
			),
			array(
				'ID' => 10,
				'NAME' => 'Узел 10',
				'LEFT_MARGIN' => 4,
				'RIGHT_MARGIN' => 5,
				'DEPTH_LEVEL' => 4,
				'PARENT_SECTION_ID' => 5
			),
			array(
				'ID' => 11,
				'NAME' => 'Узел 11',
				'LEFT_MARGIN' => 6,
				'RIGHT_MARGIN' => 7,
				'DEPTH_LEVEL' => 4,
				'PARENT_SECTION_ID' => 5
			),
			array(
				'ID' => 12,
				'SORT' => 100,
				'NAME' => 'Узел 12',
				'LEFT_MARGIN' => 14,
				'RIGHT_MARGIN' => 15,
				'DEPTH_LEVEL' => 4,
				'PARENT_SECTION_ID' => 7
			),
			array(
				'ID' => 13,
				'SORT' => 200,
				'NAME' => 'Узел 13',
				'LEFT_MARGIN' => 16,
				'RIGHT_MARGIN' => 17,
				'DEPTH_LEVEL' => 4,
				'PARENT_SECTION_ID' => 7
			),
			array(
				'ID' => 14,
				'SORT' => 300,
				'NAME' => 'Узел 14',
				'LEFT_MARGIN' => 18,
				'RIGHT_MARGIN' => 19,
				'DEPTH_LEVEL' => 4,
				'PARENT_SECTION_ID' => 7
			),
			array(
				'ID' => 15,
				'NAME' => 'Узел 15',
				'LEFT_MARGIN' => 26,
				'RIGHT_MARGIN' => 27,
				'DEPTH_LEVEL' => 4,
				'PARENT_SECTION_ID' => 9
			),
			array(
				'ID' => 16,
				'NAME' => 'Узел 16',
				'LEFT_MARGIN' => 28,
				'RIGHT_MARGIN' => 29,
				'DEPTH_LEVEL' => 4,
				'PARENT_SECTION_ID' => 9
			)
		);
	}

	/**
	 * Добавляет индекс в таблицу
	 * Функция запускается автоматически после создания таблицы.
	 *
	 * @return bool
	 */
	public static function OnAfterCreateTable()
	{
		$sqlHelper = new SqlHelper(static::getTableName());
		$sql = "CREATE INDEX "
			.$sqlHelper->wrapQuotes('LEFT_MARGIN')." ON "
			.$sqlHelper->wrapTableQuotes()." ("
			.$sqlHelper->wrapQuotes('LEFT_MARGIN').", "
			.$sqlHelper->wrapQuotes('RIGHT_MARGIN').", "
			.$sqlHelper->wrapQuotes('DEPTH_LEVEL').")";
		$query = new Query\QueryBase($sql);
		$res = $query->exec();
		if ($res->getResult())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
<?php
/**
 * Ms\Core\Lib\TableHelper
 * Помощник обработки данных таблиц
 *
 * @package Ms\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @since 0.1.0
 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/table_helper/start
 */

namespace Ms\Core\Lib;

use \Ms\Core\Entity\Db\Fields;

class TableHelper
{
	/**
	 * Возвращает сущность Fields\IntegerField для primary поля таблицы 'ID' (Ключ)
	 * Если указаны дополнительные параметры, они также добавляются к свойствам поля
	 *
	 * @api
	 *
	 * @param array $arParams Массив дополнительных параметров
	 *
	 * @return Fields\IntegerField
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/table_helper/method_primary_field
	 */
	public static function primaryField ($arParams=array())
	{
		if (isset($arParams['field']))
		{
			$field_name = $arParams['field'];
			unset($arParams['field']);
		}
		else
		{
			$field_name = "ID";
		}
		$arResult = array(
			'primary' => true,
			'autocomplete' => true,
			'title' => 'Ключ'
		);
		self::parseParams($arResult,$arParams);

		return new Fields\IntegerField($field_name,$arResult);
	}

	/**
	 * Возвращает сущность Fields\BooleanField для поля таблицы 'ACTIVE' (Активность)
	 * Если указаны дополнительные параметры, они также добавляются к свойствам поля
	 *
	 * @api
	 *
	 * @param array $arParams Массив дополнительных параметров
	 *
	 * @return Fields\BooleanField
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/table_helper/method_active_field
	 */
	public static function activeField($arParams=array())
	{
		if (isset($arParams['field']))
		{
			$field_name = $arParams['field'];
			unset($arParams['field']);
		}
		else
		{
			$field_name = "ACTIVE";
		}
		$arResult = array(
			'required' => true,
			'default_create' => true,
			'default_insert' => true,
			'title' => 'Активность'
		);
		self::parseParams($arResult,$arParams);

		return new Fields\BooleanField($field_name,$arResult);
	}

	/**
	 * Возвращает сущность Fields\IntegerField для поля таблицы 'SORT' (Сортировка)
	 * Если указаны дополнительные параметры, они также добавляются к свойствам поля
	 *
	 * @api
	 *
	 * @param array $arParams Массив дополнительных параметров
	 *
	 * @return Fields\IntegerField
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/table_helper/method_sort_field
	 */
	public static function sortField($arParams=array())
	{
		if (isset($arParams['field']))
		{
			$field_name = $arParams['field'];
			unset($arParams['field']);
		}
		else
		{
			$field_name = "SORT";
		}
		$arResult = array(
			'required' => true,
			'default_create' => Options::getOptionInt('MS_CORE_SORT_DEFAULT',500),
			'default_insert' => Options::getOptionInt('MS_CORE_SORT_DEFAULT',500),
			'title' => 'Сортировка'
		);
		self::parseParams($arResult,$arParams);

		return new Fields\IntegerField($field_name,$arResult);
	}

	/**
	 * Обрабатывает переданные параметры и объединяет с параметрами сущности
	 *
	 * @param array $arResult Массив основных параметров сущности
	 * @param array $arParams Массив дополнительных параметро сущности
	 *
	 * @return array Объединенный массив параметров сущности
	 */
	private static function parseParams (array &$arResult,array $arParams)
	{
		if (isset($arParams['primary']))
		{
			$arResult['primary'] = $arParams['primary'];
			unset($arParams['primary']);
		}
		if (isset($arParams['autocomplete']))
		{
			$arResult['autocomplete'] = $arParams['autocomplete'];
			unset($arParams['autocomplete']);
		}
		if (isset($arParams['required']))
		{
			$arResult['required'] = $arParams['required'];
			unset($arParams['required']);
		}
		if (isset($arParams['default_value']))
		{
			$arResult['default_value'] = $arParams['default_value'];
			unset($arParams['default_value']);
		}
		if (isset($arParams['default_create']))
		{
			$arResult['default_create'] = $arParams['default_create'];
			unset($arParams['default_create']);
		}
		if (isset($arParams['default_insert']))
		{
			$arResult['default_insert'] = $arParams['default_insert'];
			unset($arParams['default_insert']);
		}
		if (isset($arParams['default_update']))
		{
			$arResult['default_update'] = $arParams['default_update'];
			unset($arParams['default_update']);
		}
		if (isset($arParams['title']))
		{
			$arResult['title'] = $arParams['title'];
			unset($arParams['title']);
		}
		if (!empty($arParams))
		{
			$arResult = array_merge($arResult,$arParams);
		}
	}
}
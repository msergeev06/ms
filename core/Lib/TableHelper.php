<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\System\Application;
use \Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Tables\UsersTable;

/**
 * Класс Ms\Core\Lib\TableHelper
 * Помощник обработки данных таблиц
 *
 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/table_helper/start
 */
class TableHelper
{
	/**
	 * Возвращает сущность Fields\IntegerField для primary поля таблицы 'ID' (Ключ)
	 *
	 * @param string $field_name Свое имя поля
	 *
	 * @return Fields\IntegerField
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/table_helper/method_primary_field
	 */
	public static function primaryField (string $field_name = 'ID')
	{
	    $field_name = strtoupper($field_name);

		$field = (new Fields\IntegerField($field_name))
            ->setPrimary()
            ->setAutocomplete()
            ->setTitle('Ключ')
        ;

		return $field;
	}

    /**
     * Возвращает сущность Fields\BooleanField для поля таблицы 'ACTIVE' (Активность)
     *
     * @param string $field_name Свое имя поля
     *
     * @return \Ms\Core\Entity\Db\Fields\BooleanField
     * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/table_helper/method_active_field
     */
	public static function activeField(string $field_name = 'ACTIVE')
	{
        $field_name = strtoupper($field_name);

        $field = (new Fields\BooleanField($field_name))
            ->setRequired()
            ->setDefaultCreate(true)
            ->setDefaultInsert(true)
            ->setTitle('Активность')
        ;

		return $field;
	}

	/**
	 * Возвращает сущность Fields\IntegerField для поля таблицы 'SORT' (Сортировка)
	 *
     * @param string $field_name Свое имя поля
     *
	 * @return Fields\IntegerField
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/table_helper/method_sort_field
	 */
	public static function sortField(string $field_name = 'SORT')
	{
        $field_name = strtoupper($field_name);

		$sortDefault = Options::getOptionInt(
		    'MS_CORE_SORT_DEFAULT',
            500
        );

		$field = (new Fields\IntegerField($field_name))
            ->setRequired()
            ->setDefaultCreate($sortDefault)
            ->setDefaultInsert($sortDefault)
            ->setTitle('Сортировка')
        ;

		return $field;
	}

	/**
	 * Возвращает сущность Fields\IntegerField для поля таблицы 'CREATED_BY' (Кем создан)
	 *
     * @param string $field_name Свое имя поля
     *
	 * @return Fields\IntegerField
	 */
	public static function createdByField (string $field_name = 'CREATED_BY')
	{
        $field_name = strtoupper($field_name);

        $field = (new Fields\IntegerField($field_name))
            ->setRequired()
            ->setRequiredNull()
            ->setDefaultInsert(Application::getInstance()->getUser()->getID())
            ->setLink(UsersTable::getTableName())
            ->setForeignOnUpdateCascade()
            ->setForeignOnDeleteSetNull()
            ->setTitle('ID пользователя кем создан')
        ;

		return $field;
	}

	//TODO: Переделать на методы классов
	/**
	 * Возвращает сущность Fields\DateTimeField для поля таблицы 'CREATED_DATE' (Дата создания)
	 * Если указаны дополнительные параметры, они также добавляются к свойствам поля
	 *
	 * @param array $arParams Массив дополнительных параметров
	 *
	 * @return Fields\DateTimeField
	 */
	public static function createdDateField ($arParams=array())
	{
		if (isset($arParams['field']))
		{
			$field_name = $arParams['field'];
			unset($arParams['field']);
		}
		else
		{
			$field_name = "CREATED_DATE";
		}
		$arResult = [
			'required' => true,
			'required_null' => true,
			'default_insert' => new Date(),
			'title' => 'Дата создания'
		];
		self::parseParams($arResult,$arParams);

		return new Fields\DateTimeField($field_name,$arResult);
	}

	/**
	 * Возвращает сущность Fields\IntegerField для поля таблицы 'UPDATED_BY' (Кем изменен)
	 * Если указаны дополнительные параметры, они также добавляются к свойствам поля
	 *
	 * @param array $arParams Массив дополнительных параметров
	 *
	 * @return Fields\IntegerField
	 */
	public static function updatedByField ($arParams=array())
	{
		if (isset($arParams['field']))
		{
			$field_name = $arParams['field'];
			unset($arParams['field']);
		}
		else
		{
			$field_name = "UPDATED_BY";
		}
		$arResult = [
			'required' => true,
			'required_null' => true,
			'default_insert' => Application::getInstance()->getUser()->getID(),
			'title' => 'ID пользователя, кем изменен'
		];
		self::parseParams($arResult,$arParams);

		return new Fields\IntegerField($field_name,$arResult,UsersTable::getTableName().'.ID','cascade','set_null');
	}

	/**
	 * Возвращает сущность Fields\DateTimeField для поля таблицы 'UPDATED_DATE' (Дата изменения)
	 * Если указаны дополнительные параметры, они также добавляются к свойствам поля
	 *
	 * @param array $arParams Массив дополнительных параметров
	 *
	 * @return Fields\DateTimeField
	 */
	public static function updatedDateField ($arParams=array())
	{
		if (isset($arParams['field']))
		{
			$field_name = $arParams['field'];
			unset($arParams['field']);
		}
		else
		{
			$field_name = "UPDATED_DATE";
		}
		$arResult = [
			'required' => true,
			'required_null' => true,
			'default_update' => new Date(),
			'title' => 'Дата изменения'
		];
		self::parseParams($arResult,$arParams);

		return new Fields\DateTimeField($field_name,$arResult);
	}

	/**
	 * Обрабатывает переданные параметры и объединяет с параметрами сущности
	 *
	 * @param array &$arResult Массив основных параметров сущности
	 * @param array  $arParams Массив дополнительных параметро сущности
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
		if (isset($arParams['required_null']))
		{
			$arResult['required_null'] = $arParams['required_null'];
			unset($arParams['required_null']);
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
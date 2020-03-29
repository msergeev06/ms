<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Entity\Type\Date;

/**
 * Класс Ms\Core\Entity\Db\Fields\TimeField
 * Сущность поля базы данных, содержащего время
 */
class TimeField extends ScalarField
{
	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @param Date              $value
	 * @param TimeField|null    $obj
	 *
	 * @return array|mixed
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($value))
		{
			$value = new Date($value,'site_time');

			$value = parent::fetchDataModification ($value, $obj);
		}

		return $value;
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базу данных
	 *
	 * @param Date              $value
	 * @param TimeField|null    $obj
	 *
	 * @return bool|mixed|string
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($value))
		{
			//$value = self::validate($value, $obj);
			$value = $value->getTime();

			$value = parent::saveDataModification($value);
		}

		return $value;
	}

    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     *
     * @throws \Ms\Core\Exception\ArgumentNullException
     */
	public function __construct ($name)
	{
		parent::__construct($name);

		$this->dataType = 'time';
		$this->fieldType = 'Ms\Core\Entity\Type\Date';
	}

    /**
     * Возвращает значение поля в формате SQL
     *
     * @param mixed $value
     *
     * @return string
     */
	public function getSqlValue ($value)
	{
		if ($value instanceof Date)
		{
			return "'".$value->getTimeSite()."'";
		}
		else
		{
			return "'".$value."'";
		}
	}
}
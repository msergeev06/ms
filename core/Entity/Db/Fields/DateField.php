<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Entity\Type\Date;
use Ms\Core\Exception\ArgumentTypeException;

/**
 * Класс Ms\Core\Entity\Db\Fields\DateField
 * Сущность поля базы данных, содержащего дату
 */
class DateField extends ScalarField
{
	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @param Date|string $value
	 * @param DateField|null $obj
	 *
	 * @return array|bool|mixed|string
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($value))
		{
			if ($value instanceof Date)
			{
				$date = $value;
			}
			else
			{
				$date = new Date($value, 'db');
			}
			if (!is_null($date->getTimestamp()))
			{
				$value = $date;
			}
		}

		return $value;
	}

    /**
     * Обрабатывает значение поля перед сохранением в базу данных
     *
     * @param Date|null      $value
     * @param DateField|null $obj
     *
     * @return bool|mixed|string
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($value) && $value=='')
		{
			$value=null;
		}

		if (!is_null($value))
		{
            if (!($value instanceof Date))
            {
                throw new ArgumentTypeException((string)$value,'Ms\Core\Entity\Type\Date');
            }
            else
            {
                $value = $value->getDateDB();
            }
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
	public function __construct($name)
	{
		parent::__construct($name);

		$this->dataType = 'date';
		$this->fieldType = 'Ms\Core\Entity\Type\Date';
	}

    /**
     * Возвращает значение поля в формате SQL
     *
     * @param Date|string $value
     *
     * @return string
     */
	public function getSqlValue ($value)
	{
		if ($value instanceof Date)
		{
			return "'".$value->getDateDB()."'";
		}
		else
		{
			return "'".$value."'";
		}
	}


}
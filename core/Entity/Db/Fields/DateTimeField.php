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
 * Класс Ms\Core\Entity\Db\Fields\DateTimeField
 * Сущность поля базы данных, содержащего дату и время
 */
class DateTimeField extends ScalarField
{
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

		$this->dataType = 'datetime';
		$this->fieldType = 'Ms\Core\Entity\Type\Date';
	}

    /**
     * Устанавливает значение по-умолчанию для всех действий
     *
     * @param Date|string $defaultValue
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultValue ($defaultValue)
	{
		if (!($defaultValue instanceof Date) && !is_string($defaultValue))
		{
			throw new ArgumentTypeException(
			    '$defaultValue',
                '\Ms\Core\Entity\Type\Date|string'
            );
		}

		return parent::setDefaultValue($defaultValue);
	}

    /**
     * Устанавливает значение по-умолчанию для действия CREATE
     *
     * @param Date|string $defaultCreate
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultCreate ($defaultCreate)
	{
		if (!($defaultCreate instanceof Date) && !is_string($defaultCreate))
		{
			throw new ArgumentTypeException(
			    '$defaultCreate',
                '\Ms\Core\Entity\Type\Date|string'
            );
		}

		return parent::setDefaultCreate($defaultCreate);
	}

    /**
     * Устанавливает значение по-умолчанию для действия INSERT
     *
     * @param Date|string $defaultInsert
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultInsert ($defaultInsert)
	{
		if (!($defaultInsert instanceof Date) && !is_string($defaultInsert))
		{
			throw new ArgumentTypeException(
			    '$defaultInsert',
                '\Ms\Core\Entity\Type\Date|string'
            );
		}

		return parent::setDefaultInsert($defaultInsert);
	}

    /**
     * Устанавливает значение по-умолчанию для действия UPDATE
     *
     * @param null|Date|string $defaultUpdate
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultUpdate ($defaultUpdate)
	{
		if (!is_null($defaultUpdate) && !($defaultUpdate instanceof Date) && !is_string($defaultUpdate))
		{
			throw new ArgumentTypeException(
			    '$defaultUpdate',
                '\Ms\Core\Entity\Type\Date|string'
            );
		}

		return parent::setDefaultUpdate($defaultUpdate);
	}


	/**
	 * Обрабатывает значение поля перед сохранением в базу данных
	 *
	 * @api
	 *
	 * @param Date $value
	 * @param DateTimeField|null $obj
	 *
	 * @return bool|mixed|string
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($value))
		{
			$value = $value->getDateTimeDB();
		}

		return $value;
	}

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @param string $value
	 * @param DateTimeField|null $obj
	 *
	 * @return Date|null
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($value))
		{
			$date = new Date($value, 'db_datetime');
			if (!is_null($date->getTimestamp()))
				$value = $date;
		}

		return $value;
	}

	public function getSqlValue ($value)
	{
		if ($value instanceof Date)
		{
			return "'".$value->getDateTimeDB()."'";
		}
		else
		{
			return "'".$value."'";
		}
	}


}
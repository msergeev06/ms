<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Exception\ArgumentTypeException;

/**
 * Класс Ms\Core\Entity\Db\Fields\BooleanField
 * Сущность поля базы данных, содержащего булево значение
 */
class BooleanField extends ScalarField
{
	/**
	 * @var int Размер типа поля в базе данных
	 */
	protected $size=1;
	/**
	 * Value (false, true) equivalent map
	 * @var array
	 */
	protected $values;

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @param string $value
	 * @param BooleanField|null $obj
	 *
	 * @return array|mixed
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			$value = $obj->normalizeValue($value);
		}
		$value = parent::fetchDataModification($value, $obj);

		return $value;
	}

	/**
	 * Обрабатывает значение поля перед записью в базу данных
	 *
	 * @param mixed $value
	 * @param BooleanField|null $obj
	 *
	 * @return mixed|string
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			$value = $obj->normalizeValue($value);
		}
		if ($value)
		{
			$value = 'Y';
		}
		else
		{
			$value = 'N';
		}
		$value = parent::saveDataModification($value, $obj);

		return $value;
	}

    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     *
     */
	function __construct($name)
	{
		parent::__construct($name);

		$this->dataType = 'varchar';
		$this->fieldType = 'boolean';

        $this->values = array(false, true);
	}

	/**
	 * Возвращает значение по-умолчанию для базы данных
	 *
	 * @return null|string
	 */
	public function getDefaultValueDB()
	{
		$value = $this->getDefaultValue();
		if (!is_null($value))
		{
			if ($value === true) {
				return 'Y';
			}
			else {
				return 'N';
			}
		}
		else
		{
			return null;
		}
	}

	/**
	 * Возвращает размер типа поля в базе данных
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * Возвращает значение приобразованное в формат SQL запроса
	 *
	 * @param mixed $value значение
	 *
	 * @return string
	 */
	public function getSqlValue($value)
	{
		if ($value === true || $value==='Y' || $value===1)
		{
			return "'Y'";
		}
		else
		{
			return "'N'";
		}
	}

	/**
	 * Возвращает варианты значений поля
	 *
	 * @return array
	 */
	public function getValues()
	{
		return $this->values;
	}

	/**
     * Конвертирует значения, которые можно интерпретировать как TRUE/FALSE в актуальные для поля значения
	 *
	 * @param boolean|integer|string $value
	 * @return mixed
	 */
	public function normalizeValue($value)
	{
		if (
			(is_string($value) && ($value == '1' || $value == '0'))
			||
			(is_bool($value))
		)
		{
			$value = (int) $value;
		}
		elseif (is_string($value) && ($value == 'true' || $value == 'Y'))
		{
			$value = 1;
		}
		elseif (is_string($value) && ($value == 'false' || $value== 'N'))
		{
			$value = 0;
		}

		if (is_integer($value) && ($value == 1 || $value == 0))
		{
			$value = $this->values[$value];
		}

		return $value;
	}

    /**
     * Устанавливает значение по-умолчанию для действия CREATE
     *
     * @param bool|string $defaultCreate
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultCreate ($defaultCreate)
	{
		if (!is_bool($defaultCreate) && !is_string($defaultCreate))
		{
			throw new ArgumentTypeException('$defaultCreate','bool|string');
		}

		return parent::setDefaultCreate($defaultCreate);
	}

    /**
     * Устанавливает значение по-умолчанию для действия INSERT
     *
     * @param bool|string $defaultInsert
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultInsert ($defaultInsert)
	{
		if (!is_bool($defaultInsert) && !is_string($defaultInsert))
		{
			throw new ArgumentTypeException('$defaultInsert','bool|string');
		}
		return parent::setDefaultInsert($defaultInsert);
	}

    /**
     * Устанавливает значение по-умолчанию для действия UPDATE
     *
     * @param bool|string $defaultUpdate
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultUpdate ($defaultUpdate)
	{
		if (!is_bool($defaultUpdate) && !is_string($defaultUpdate))
		{
			throw new ArgumentTypeException('$defaultUpdate','bool|string');
		}

		return parent::setDefaultUpdate($defaultUpdate);
	}

    /**
     * Устанавливает значение по-умолчанию для всех действий
     *
     * @param bool|string $defaultValue
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultValue ($defaultValue)
	{
		if (!is_bool($defaultValue) && !is_string($defaultValue))
		{
			throw new ArgumentTypeException('$defaultValue','bool|string');
		}

		return parent::setDefaultValue($defaultValue);
	}
}
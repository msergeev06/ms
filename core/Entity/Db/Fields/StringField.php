<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Entity\System\Application;
use Ms\Core\Exception;

/**
 * Класс Ms\Core\Entity\Db\Fields\StringField
 * Сущность поля базы данных, содержащего строку
 */
class StringField extends ScalarField
{
	/**
	 * @var int Размер типа varchar базы данных
	 */
	protected $size = 255;

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

		$this->dataType = 'varchar';
		$this->fieldType = 'string';
	}

    /**
     * Устанавливает размер строки, сохраняемой в БД
     *
     * @param int $size
     *
     * @return $this
     */
	public function setSize (int $size = 255)
	{
		if ((int)$size > 0 && (int)$size<=255)
		{
			$this->size = $size;
		}
		else
		{
			$this->size = 255;
		}

		return $this;
	}

    /**
     * Устанавливает значение по-умолчанию для всех действий
     *
     * @param string $defaultValue
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultValue ($defaultValue)
	{
		if (!is_string($defaultValue))
		{
			throw new Exception\ArgumentTypeException('$defaultValue','string');
		}

		return parent::setDefaultValue($defaultValue);
	}

    /**
     * Устанавливает значение по-умолчанию для действия INSERT
     *
     * @param string $defaultInsert
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultInsert ($defaultInsert)
	{
		if (!is_string($defaultInsert))
		{
			throw new Exception\ArgumentTypeException('$defaultInsert','string');
		}

		return parent::setDefaultInsert($defaultInsert);
	}

    /**
     * Устанавливает значение по-умолчанию для действия CREATE
     *
     * @param string $defaultCreate
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultCreate ($defaultCreate)
	{
		if (!is_string($defaultCreate))
		{
			throw new Exception\ArgumentTypeException('$defaultCreate','string');
		}

		return parent::setDefaultCreate($defaultCreate);
	}

    /**
     * Устанавливает значение по-умолчанию для действия UPDATE
     *
     * @param string $defaultUpdate
     *
     * @return \Ms\Core\Entity\Db\Fields\ScalarField
     * @throws \Ms\Core\Exception\ArgumentTypeException
     */
	public function setDefaultUpdate ($defaultUpdate)
	{
		if (!is_string($defaultUpdate))
		{
			throw new Exception\ArgumentTypeException('$defaultUpdate','string');
		}

		return parent::setDefaultUpdate($defaultUpdate);
	}

	/**
	 * Возвращает размер поля в базе данных (в символах)
	 *
	 * @return int|null
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базе данных
	 *
	 * @param                   $value
	 * @param StringField|null  $obj
	 *
	 * @return mixed|string
	 * @throws Exception\Db\DbException
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		$DB = Application::getInstance()->getConnection();
		$value = parent::saveDataModification($value,$obj);
		//$value = mysql_real_escape_string($value);
		$value = $DB->getConnectionRealEscapeString($value);
		$value = str_replace("%", "\%", $value);

		return $value;
	}

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @param string           $value
	 * @param StringField|null $obj
	 *
	 * @return array|mixed
	 */
	public static function fetchDataModification ($value, $obj = NULL)
	{
		$value = parent::fetchDataModification($value, $obj);

		return $value;
	}
}
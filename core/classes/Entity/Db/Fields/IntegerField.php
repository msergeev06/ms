<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Fields\IntegerField
 * Сущность поля базы данных, содержащего целое число
 */
class IntegerField extends ScalarFieldAbstract implements IField
{
	/**
	 * @var int Размерность поля int базы данных
	 */
	protected $size = 10;

	/**
	 * Обрабатывает значение после получения из базы данных
	 *
	 * @param mixed                 $value
	 *
	 * @return array|int|mixed
     * @unittest
	 */
	public function fetchDataModification ($value)
	{
		if (!is_null($value))
		{
			$value = parent::fetchDataModification($value);
			$value = intval($value);
		}

		return $value;
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базу данных
	 *
	 * @param mixed                 $value  Значение
	 *
	 * @return mixed
     * @unittest
	 */
	public function saveDataModification ($value)
	{
		if (!is_null($value))
		{
			$value = parent::saveDataModification($value);
			$value = intval($value);
		}

		return $value;
	}

    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     */
	public function __construct($name)
	{
		parent::__construct($name);

		$this->dataType = 'int';
		$this->fieldType = 'integer';
	}

	/**
	 * Возвращает размерность поля int базы данных
	 *
	 * @return int
     * @unittest
	 */
	public function getSize ()
	{
		return $this->size;
	}

	/**
     * Устанавливает размерность поля
     *
	 * @param int $size
	 *
	 * @return $this
     * @unittest
	 */
	public function setSize (int $size)
	{
		$this->size = $size;

		return $this;
	}

	/**
	 * Возвращает значение поля в SQL формате
	 *
	 * @param int    $value
	 *
	 * @return string
	 */
	public function getSqlValue ($value): string
	{
		return (string) $value;
	}

    /**
     * Возвращает SQL код устанавливающий размерность поля, если необходимо, либо пустую строку
     *
     * @return string
     */
    public function getSizeSql (): string
    {
        return '(' . $this->getSize() . ')';
    }
}
<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Entity\Db\Fields\IntegerField
 * Сущность поля базы данных, содержащего целое число
 */
class IntegerField extends ScalarField
{
	/**
	 * @var int Размерность поля int базы данных
	 */
	protected $size = 10;

	/**
	 * Обрабатывает значение после получения из базы данных
	 *
	 * @param mixed                 $value
	 * @param IntegerField|null     $obj
	 *
	 * @return array|int|mixed
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($value))
		{
			$value = parent::fetchDataModification($value, $obj);
			$value = intval($value);
		}

		return $value;
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базу данных
	 *
	 * @param mixed                 $value  Значение
	 * @param IntegerField|null     $obj    Объект поля
	 *
	 * @return mixed
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($value))
		{
			$value = parent::saveDataModification($value, $obj);
			$value = intval($value);
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

		$this->dataType = 'int';
		$this->fieldType = 'integer';
	}

	/**
	 * Возвращает размерность поля int базы данных
	 *
	 * @return int
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
	 * @return IntegerField
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
	public function getSqlValue ($value)
	{
		return (string) $value;
	}
}
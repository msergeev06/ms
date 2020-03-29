<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Entity\Db\Fields\FloatField
 * Сущность поля базы данных, содержащего вещественное число
 */
class FloatField extends ScalarField
{
	/**
	 * @var int Точность, знаков после запятой
	 */
	protected $scale=2;

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @param                   $value
	 * @param FloatField|null   $obj
	 *
	 * @return array|float|mixed
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			if (!is_null($value))
			{
				$scale = $obj->getScale();
				$value = round($value,$scale);
			}
		}

		return $value;
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базе данных
	 *
	 * @param                   $value
	 * @param FloatField|null   $obj
	 *
	 * @return float|mixed|string
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($obj))
		{
			if (!is_null($value))
			{
				$scale = $obj->getScale();
				$value = round($value,$scale);
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

		$this->dataType = $this->fieldType = 'float';
	}

	/**
	 * Возвращает точность (количество знаков после запятой)
	 *
	 * @return int|null
	 */
	public function getScale()
	{
		return $this->scale;
	}

	/**
     * Устанавливает точность (количество знаков после запятой) значения
     *
	 * @param int $scale
	 *
	 * @return FloatField
	 */
	public function setScale (int $scale)
	{
		$this->scale = $scale;

		return $this;
	}

	/**
	 * Возвращает значение поля в SQL формате
	 *
	 * @param float $value
	 *
	 * @return string
	 */
	public function getSqlValue ($value)
	{
		return (string) $value;
	}
}
<?php
/**
 * Ms\Core\Entity\Db\Fields\ColorField
 * Сущность поля базы данных, содержащего цвет
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Entity\Type\Color;
use Ms\Core\Exception\ArgumentTypeException;

class ColorField extends IntegerField
{
	/**
	 * Конструктор
	 *
	 * @param string $name              Имя поля таблицы БД
	 * @param array  $parameters        Параметры поля таблицы БД
	 * @param string $link              Связанное поле вида "таблица.поле"
	 * @param string $onUpdate          Действие при изменении связанного поля
	 * @param string $onDelete          Действие при удалении связанного поля
	 * @param bool   $linkNotForeignKey Флаг, что связь не является FOREIGN KEY
	 */
	public function __construct($name, $parameters = array(),$link=null,$onUpdate='cascade',$onDelete='restrict', $linkNotForeignKey=false)
	{
		parent::__construct($name, $parameters,$link,$onUpdate,$onDelete,$linkNotForeignKey);

		$this->dataType = 'int';
		$this->fieldType = 'Ms\Core\Entity\Type\Color';
	}

	/**
	 * Обрабатывает значение поля перед сохранением в базу данных
	 *
	 * @api
	 *
	 * @param Color|null $value
	 * @param ColorField|null $obj
	 *
	 * @return bool|mixed|int
	 */
	public static function saveDataModification ($value, $obj=null)
	{
		if (!is_null($value))
		{
			try
			{
				if (!($value instanceof Color))
				{
					throw new ArgumentTypeException((int)$value,'Ms\Core\Entity\Type\Color');
				}
				else
				{
					$value = $value->getFormatInteger();
				}
			}
			catch (ArgumentTypeException $e)
			{
				die($e->showException());
			}
		}

		return $value;
	}

	/**
	 * Обрабатывает значение поля после получения из базы данных
	 *
	 * @api
	 *
	 * @param Color|int $value
	 * @param ColorField|null $obj
	 *
	 * @return array|bool|mixed|string
	 */
	public static function fetchDataModification ($value, $obj=null)
	{
		if (!is_null($value))
		{
			if ($value instanceof Color)
			{
				$color = $value;
			}
			else
			{
				$color = (new Color())->setFromRgbInt($value);
			}
			if ($color->isCorrect())
			{
				$value = $color;
			}
		}

		return $value;
	}
}
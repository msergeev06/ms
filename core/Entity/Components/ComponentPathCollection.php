<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Components;

use Ms\Core\Entity\Type\Dictionary;

/**
 * Класс Ms\Core\Entity\Components\ComponentPathCollection
 * Класс-коллекция путей компонента
 */
class ComponentPathCollection extends Dictionary
{
	/**
	 * ComponentPathCollection constructor.
	 *
	 * @return ComponentPathCollection
	 */
	public function __construct ()
	{
		parent::__construct();

		return $this;
	}

	/**
	 * Добавляет путь в коллекцию
	 *
	 * @param ComponentPaths $value
	 *
	 * @return ComponentPathCollection
	 */
	public function addPath (ComponentPaths $value)
	{
		$i = $this->count();
		parent::add($i, $value);

		return $this;
	}

	/**
	 * Возвращает путь из коллекции
	 *
	 * @param $index
	 *
	 * @return ComponentPaths
	 */
	public function getPath ($index)
	{
		return parent::get($index);
	}

	/**
	 * Возвращает массив значений коллекции
	 *
	 * @return ComponentPaths[]
	 */
	public function toArray ()
	{
		return parent::toArray();
	}


}
<?php

namespace Ms\Core\Entity\Collections;

use Ms\Core\Entity\ComponentPaths;
use Ms\Core\Entity\Type\Dictionary;

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
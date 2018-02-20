<?php
/**
 * MSergeev\Core\Entity\Type\ParameterDictionary
 *
 * @package MSergeev\Core
 * @subpackage Entity\Type
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace MSergeev\Core\Entity\Type;

use MSergeev\Core\Exception\NotSupportedException;

class ParameterDictionary extends Dictionary
{
	/**
	 * @var array
	 */
	protected $arRawValues = null;

	/**
	 * @param array $values
	 * @since 0.2.0
	 */
	protected function setValuesNoDemand(array $values)
	{
		if ($this->arRawValues === null)
			$this->arRawValues = $this->values;
		$this->values = $values;
	}

	/**
	 * @param $name
	 *
	 * @return null
	 * @since 0.2.0
	 */
	public function getRaw($name)
	{
		if ($this->arRawValues === null)
		{
			if (isset($this->values[$name]) || array_key_exists($name, $this->values))
				return $this->values[$name];
		}
		else
		{
			if (isset($this->arRawValues[$name]) || array_key_exists($name, $this->arRawValues))
				return $this->arRawValues[$name];
		}

		return null;
	}

	/**
	 * @param $name
	 * @param $value
	 * @since 0.2.0
	 */
	public function addRaw ($name, $value)
	{
		$this->arRawValues[$name] = $value;
	}

	/**
	 * @return array
	 * @since 0.2.0
	 */
	public function toArrayRaw()
	{
		return $this->arRawValues;
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 *
	 * @throws NotSupportedException
	 * @since 0.2.0
	 */
	public function offsetSet($offset, $value)
	{
		throw new NotSupportedException("Can not set readonly value");
	}

	/**
	 * Offset to unset
	 *
	 * @param mixed $offset
	 *
	 * @throws NotSupportedException
	 * @since 0.2.0
	 */
	public function offsetUnset($offset)
	{
		throw new NotSupportedException("Can not unset readonly value");
	}
}
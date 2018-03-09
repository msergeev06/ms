<?php
/**
 * Ms\Core\Entity\Type\Dictionary
 * Класс для хранения различных значений в удобном формате
 *
 * @package Ms\Core
 * @subpackage Entity\Type
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Type;

class Dictionary implements \ArrayAccess, \Iterator, \Countable
{
	/**
	 * @var array
	 */
	protected $values = array();

	/**
	 * Конструктор
	 *
	 * @param array $values
	 * @since 0.2.0
	 */
	public function __construct(array $values = null)
	{
		if($values !== null)
		{
			$this->values = $values;
		}
	}

	/**
	 * Возвращает указанное значение
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function get($name)
	{
		if (isset($this->values[$name]) || array_key_exists($name, $this->values))
		{
			return $this->values[$name];
		}

		return null;
	}

	/**
	 * Устанавливает все значения, переданные в массиве
	 *
	 * @param array $values
	 * @since 0.2.0
	 */
	public function set(array $values)
	{
		$this->values = $values;
	}

	/**
	 * Добавляет новое значение
	 *
	 * @param string $name
	 * @param mixed $value
	 * @since 0.2.0
	 */
	public function add ($name, $value)
	{
		$this->values[$name] = $value;
	}

	/**
	 * Очищает все значения
	 * @since 0.2.0
	 */
	public function clear()
	{
		$this->values = array();
	}

	/**
	 * Возвращает текущий элемент массива значений
	 *
	 * Обертка функции current для массива значений
	 * @link http://php.net/manual/en/function.current.php
	 *
	 * @return mixed
	 * @since 0.2.0
	 */
	public function current()
	{
		return current($this->values);
	}

	/**
	 * Advance the internal array pointer of an array
	 * @link http://php.net/manual/en/function.next.php
	 *
	 * @return mixed
	 * @since 0.2.0
	 */
	public function next()
	{
		return next($this->values);
	}

	/**
	 * Fetch a key from an array
	 * @link http://php.net/manual/en/function.key.php
	 *
	 * @return mixed
	 * @since 0.2.0
	 */
	public function key()
	{
		return key($this->values);
	}

	/**
	 * Проверяет $this->key() !== null
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public function valid()
	{
		return ($this->key() !== null);
	}

	/**
	 * Set the internal pointer of an array to its first element
	 * @link http://php.net/manual/en/function.reset.php
	 *
	 * @return mixed
	 * @since 0.2.0
	 */
	public function rewind()
	{
		return reset($this->values);
	}

	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public function offsetExists($offset)
	{
		return isset($this->values[$offset]) || array_key_exists($offset, $this->values);
	}

	/**
	 * @param mixed $offset
	 *
	 * @return mixed|null
	 * @since 0.2.0
	 */
	public function offsetGet($offset)
	{
		if (isset($this->values[$offset]) || array_key_exists($offset, $this->values))
		{
			return $this->values[$offset];
		}

		return null;
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @since 0.2.0
	 */
	public function offsetSet($offset, $value)
	{
		if($offset === null)
		{
			$this->values[] = $value;
		}
		else
		{
			$this->values[$offset] = $value;
		}
	}

	/**
	 * @param mixed $offset
	 * @since 0.2.0
	 */
	public function offsetUnset($offset)
	{
		unset($this->values[$offset]);
	}

	/**
	 * @return int
	 * @since 0.2.0
	 */
	public function count()
	{
		return count($this->values);
	}

	/**
	 * @return array
	 * @since 0.2.0
	 */
	public function toArray()
	{
		return $this->values;
	}

	/**
	 * @return bool
	 * @since 0.2.0
	 */
	public function isEmpty()
	{
		return empty($this->values);
	}
}
<?php
/**
 * Ms\Core\Entity\Type\Dictionary
 * Класс для хранения различных значений в удобном формате
 *
 * @package Ms\Core
 * @subpackage Entity\Type
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
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
	 *
	 * @return Dictionary
	 */
	public function __construct(array $values = null)
	{
		if($values !== null)
		{
			$this->values = $values;
		}

		return $this;
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
	 *
	 * @return Dictionary
	 */
	public function set(array $values)
	{
		$this->values = $values;

		return $this;
	}

	/**
	 * Добавляет новое значение
	 *
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return Dictionary
	 */
	public function add ($name, $value)
	{
		$this->values[$name] = $value;

		return $this;
	}

	/**
	 * Очищает все значения
	 *
	 * @return Dictionary
	 */
	public function clear()
	{
		$this->values = array();

		return $this;
	}

	/**
	 * Возвращает текущий элемент массива значений
	 *
	 * Обертка функции current для массива значений
	 * @link http://php.net/manual/ru/function.current.php
	 *
	 * @return mixed
	 */
	public function current()
	{
		return current($this->values);
	}

	/**
	 * Advance the internal array pointer of an array
	 * @link http://php.net/manual/ru/function.next.php
	 *
	 * @return mixed
	 */
	public function next()
	{
		return next($this->values);
	}

	/**
	 * Fetch a key from an array
	 * @link http://php.net/manual/ru/function.key.php
	 *
	 * @return mixed
	 */
	public function key()
	{
		return key($this->values);
	}

	/**
	 * Проверяет $this->key() !== null
	 *
	 * @return bool
	 */
	public function valid()
	{
		return ($this->key() !== null);
	}

	/**
	 * Set the internal pointer of an array to its first element
	 * @link http://php.net/manual/ru/function.reset.php
	 *
	 * @return mixed
	 */
	public function rewind()
	{
		return reset($this->values);
	}

	/**
	 * Проверяет существование ключа
	 *
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->values[$offset]) || array_key_exists($offset, $this->values);
	}

	/**
	 * Возвращает значение по ключу
	 *
	 * @param mixed $offset
	 *
	 * @return mixed|null
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
	 * Устанавливает значение по ключу
	 *
	 * @param mixed $offset
	 * @param mixed $value
	 *
	 * @return Dictionary
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

		return $this;
	}

	/**
	 * Удаляет значение по ключу
	 *
	 * @param mixed $offset
	 *
	 * @return Dictionary
	 */
	public function offsetUnset($offset)
	{
		unset($this->values[$offset]);

		return $this;
	}

	/**
	 * Возвращает текущее количество значений
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->values);
	}

	/**
	 * Возвращает значения в виде массива
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->values;
	}

	/**
	 * Проверяет пуст ли список значений
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->values);
	}

	/**
	 * Возвращает первый элемент коллекции
	 *
	 * @return mixed
	 */
	public function getFirst ()
	{
		$this->rewind();

		return $this->current();
	}

	/**
	 * Возвращает последний элемент коллекции
	 *
	 * @return mixed
	 */
	public function getLast ()
	{
		return end($this->values);
	}
}
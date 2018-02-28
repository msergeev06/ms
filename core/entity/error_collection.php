<?php
/**
 * Ms\Core\Entity\ErrorCollection
 * Объект коллекции ошибок
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity;

/**
 * Class ErrorCollection
 * @package Ms\Core
 * @subpackage Entity
 * @extends Type\Dictionary
 */
class ErrorCollection extends Type\Dictionary
{
	/**
	 * Конструктор. Создает новую коллекцию ошибок
	 *
	 * @param Error[] $values Значения ошибок
	 * @access public
	 */
	public function __construct(array $values = null)
	{
		if($values)
		{
			$this->add($values);
		}
	}

	/**
	 * Добавляет массив ошибок в коллекцию
	 *
	 * @param Error[] $errors Массив ошибок
	 * @access public
	 * @return void
	 */
	public function add(array $errors)
	{
		foreach($errors as $error)
		{
			$this->setError($error);
		}
	}

	/**
	 * Возвращает ошибку по ее коду
	 *
	 * @param string|int $code Код ошибки
	 *
	 * @access public
	 * @return Error|null
	 */
	public function getErrorByCode($code)
	{
		foreach($this->values as $error)
		{
			/** @var Error $error */
			if($error->getCode() == $code)
			{
				return $error;
			}
		}

		return null;
	}

	/**
	 * Добавляет ошибку в коллекцию
	 *
	 * @param string $error Текст ошибки
	 * @param string|int $code Код ошибки
	 * @access public
	 * @return void
	 */
	public function setError($error, $code = null)
	{
		parent::offsetSet($code, $error);
	}

	/**
	 * Магический метод, позволяющий работать с объектом, как с массивом
	 * \ArrayAccess thing.
	 * @param mixed $offset Ключ
	 * @param mixed $value  Значение
	 * @access public
	 */
	public function offsetSet($offset, $value)
	{
		$this->setError($value, $offset);
	}
}
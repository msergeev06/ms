<?php
/**
 * Ms\Core\Exception\ObjectPropertyException
 * Класс для исключений, возникающих, когда свойство объекта недопустимо
 *
 * @package Ms\Core
 * @subpackage Exception
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception;

/**
 * Class ObjectPropertyException
 * @package Ms\Core
 * @subpackage Exception
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_object_property_exception/
 */
class ObjectPropertyException extends ArgumentException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string            $parameter  Имя аргумента, вызвавшего исключение
	 *                                      Необязательный, по-умолчанию пустая строка
	 * @param \Exception|null   $previous   Предыдущее исключение
	 *                                      Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_object_property_exception_construct/
	 */
	public function __construct($parameter = "", \Exception $previous = null)
	{
		parent::__construct("Object property \"".$parameter."\" not found.", $parameter, $previous);
	}
}

<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Classes;

use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Exceptions\Classes\ClassNotFoundException
 * Класс для исключений, возникающих при обращении к классу, файл с описанием
 * которого не был подключен вручную или расположен не по правилам расположения
 * классов и не был добавлен в автозагрузку
 *
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_class_not_found_exception/
 */
class ClassNotFoundException extends SystemException
{
	/**
	 * Конструктор. Создает объект исключения
	 *
	 * @param string          $className Имя класса, который не был найден
	 * @param \Exception|null $previous  Предыдущее исключение
	 *                                   Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_class_not_found_exception_construct/
	 */
	public function __construct($className = "", \Exception $previous = null)
	{
		$message = sprintf("Class '%s' not found. Check this namespace, or maybe it wasn't added to AutoLoad", $className);
		parent::__construct($message, 510, '', 0, $previous);
	}
}

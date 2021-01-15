<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 */

namespace Ms\Core\Exceptions\Classes;

use Ms\Core\Exceptions\SystemException;
use Ms\Core\Lib\Errors;

/**
 * Класс Ms\Core\Exceptions\Classes\ClassNotFoundException
 * Класс для исключений, возникающих при обращении к классу, файл с описанием
 * которого не был подключен вручную или расположен не по правилам расположения
 * классов и не был добавлен в автозагрузку
 */
class ClassNotFoundException extends SystemException
{
    /**
     * Конструктор. Создает объект исключения
     *
     * @param string          $className Имя класса, который не был найден
     * @param string          $file      Путь к файлу, где произошла ошибка
     * @param int             $line      Строка в файле, где произошла ошибка
     * @param \Exception|null $previous  Предыдущее исключение
     *                                   Необязательный, по-умолчанию null
     */
	public function __construct(string $className, string $file = '', int $line = 0, \Exception $previous = null)
	{
		// $message = sprintf("Class '%s' not found. Check this namespace, or maybe it wasn't added to AutoLoad", $className);
		$message = sprintf('Класс "%s" не найден. Проверьте пространство имен. Возможно класс не попал в автозагрузку', $className);
		parent::__construct($message, Errors::ERROR_CLASS_NOT_FOUND, $file, $line, $previous);
	}
}

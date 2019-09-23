<?php
/**
 * Ms\Core\Exception\SystemException
 * Данный класс является базовым для всех исключений системы и наследуется от системного исключения \Exception
 * Все остальные исключения должны быть унаследованы от него, либо от его потомков.
 *
 * @package Ms\Core
 * @subpackage Exception
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception;

use Ms\Core\Entity\Application;
use Ms\Core\Lib\IO\Files;

/**
 * Class SystemException
 * @package Ms\Core
 * @subpackage Exception
 * @link https://api.dobrozhil.ru/classes/ms_core_exception_system_exception/
 */
class SystemException extends \Exception
{
	/**
	 * Конструктор. Создает новый объект исключения.
	 *
	 * @param string $message       Сообщение исключения
	 * @param int $code             Код исключения.
	 *                              Необязательный, по-умолчанию равен 0
	 * @param string $file          Путь к файлу, в котором произошло исключение
	 *                              Необязательный, по-умолчанию пустая строка
	 * @param int $line             Порядковый номер строки, в которой произошло исключение
	 *                              Необязательный, по-умолчанию 0
	 * @param \Exception $previous  Исключение, предшествующее текущему
	 *                              Необязательный, по-умолчанию null
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_system_exception_construct/
	 */
	public function __construct($message = "", $code = 0, $file = "", $line = 0, \Exception $previous = null)
	{
		$message = htmlspecialchars($message);
		parent::__construct($message, $code, $previous);

		if (!empty($file) && !empty($line))
		{
			$this->file = $file;
			$this->line = $line;
		}
	}

	/**
	 * Генерирует сообщение об исключении. Сохраняет информацию о новом исключении в системный лог-файл.
	 * Кроме этого, при включенном режиме отладки, возвращает html-код исключения, при выключенном пустую строку
	 *
	 * @return string
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_system_exception_show_exception/
	 */
	public function showException()
	{
		$filename = Application::getInstance()->getSettings()->getSystemLogFile();
		if (!is_null($filename))
		{
			Files::createDir(dirname($filename));
			$f1 = fopen ($filename, 'a');
			$tmp=explode(' ', microtime());
			fwrite($f1, date("H:i:s ").$tmp[0]."\n");
			fwrite($f1, $this->getClassName().': "'.$this->getMessage().'"'."\n");
			fwrite($f1, "Stack trace:\n");
			fwrite($f1, $this->getTraceAsString()."\n");
			fwrite($f1, $this->getFile()." ".$this->getLine());
			fwrite($f1, "\n------------------\n");
			fclose ($f1);
			@chmod($filename, Files::getFileChmod());
		}

		if (Application::getInstance()->getSettings()->isDebugMode())
		{
			$tmp=explode(' ', microtime());
			$html = '<pre>'.date('Y-m-d H:i:s ').$tmp[0].'<br><b><i>'.$this->getClassName().':</i></b> "'.$this->getMessage().'"'."\n";
			$html .= "<b>Stack trace:</b>\n".$this->getTraceAsString()."\n";
			$html .= "<b>".$this->getFile()." ".$this->getLine()."</b>";
			$html .= "</pre>";

			return $html;
		}

		return '';
	}

	/**
	 * Возвращает имя (с пространством имен), вызвавшего его класса (используется get_called_class)
	 *
	 * @return string
	 *
	 * @link https://api.dobrozhil.ru/methods/ms_core_exception_system_exception_get_class_name/
	 */
	final public function getClassName ()
	{
		return get_called_class();
	}

}


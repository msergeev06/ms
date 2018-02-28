<?php

namespace Ms\Core\Exception;

use Ms\Core\Entity\Application;
use Ms\Core\Lib\IO\Files;

/**
 * Base class for fatal exceptions
 */
class SystemException extends \Exception
{
	/**
	 * Creates new exception object.
	 *
	 * @param string $message
	 * @param int $code
	 * @param string $file
	 * @param int $line
	 * @param \Exception $previous
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
			$html = '<pre><b><i>'.$this->getClassName().':</i></b> "'.$this->getMessage().'"'."\n";
			$html .= "<b>Stack trace:</b>\n".$this->getTraceAsString()."\n";
			$html .= "<b>".$this->getFile()." ".$this->getLine()."</b>";
			$html .= "</pre>";

			return $html;
		}

		return '';
	}

	public function getClassName ()
	{
		return __CLASS__;
	}

}


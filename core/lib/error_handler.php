<?php
/**
 * Ms\Core\Lib\ErrorHandler
 * Внутренний обработчик ошибок
 *
 * @package Ms\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\Application;
use Ms\Core\Lib\IO\Files;

class ErrorHandler
{
	/**
	 * Обработчик пользовательских ошибок
	 *
	 * @param      $errNo
	 * @param      $errStr
	 * @param null $errFile
	 * @param null $errLine
	 * @param null $errContext
	 *
	 * @return bool
	 */
	public static function handler ($errNo, $errStr, $errFile=null, $errLine=null, $errContext=null)
	{

/*		if (!(error_reporting() & $errNo)) {

			// Этот код ошибки не включен в error_reporting,
			// так что пусть обрабатываются стандартным обработчиком ошибок PHP
			return false;
		}*/

		echo '<p><strong>';
		//echo $errNo;
		switch ($errNo)
		{
			case E_USER_ERROR:
				echo 'ERROR: ';
				break;
			case E_USER_WARNING:
				echo 'WARNING: ';
				break;
			case E_USER_NOTICE:
				echo 'NOTICE: ';
				break;
			default:
				echo 'Error[',$errNo,']: ';
				break;
		}
		echo $errStr,'</strong><br>','On: ',$errFile,':',$errLine,'<br>';
		if (!is_null($errContext))
		{
			echo '<pre>',print_r($errContext,true),'</pre>';
		}
		$backtrace = debug_backtrace ();
		echo 'BackTrace:','<br>';
		foreach ($backtrace as $back)
		{
			if ($back['file']!='')
			{
				echo $back['file'],':',$back['line'],'<br>';
			}
		}
		echo '</p>';

		return true;
	}

	public static function exceptionHandler (\Throwable $e)
	{
		$filename = $_SERVER['DOCUMENT_ROOT'].'/logs/sys-errors-'.date('Y-m-d').'.log';
		if ($f1 = @fopen($filename,'a'))
		{
			$tmp=explode(' ', microtime());
			fwrite($f1, date("H:i:s ").$tmp[0]."\n");
			fwrite($f1, 'Error['.$e->getCode().']: '.$e->getMessage()."\n");
			fwrite($f1, "Stack trace:\n");
			fwrite($f1, $e->getTraceAsString()."\n");
			fwrite($f1, $e->getFile().": ".$e->getLine());
			fwrite($f1, "\n------------------\n");
			fclose ($f1);
			@chmod($filename, 0644);
		}

		if (Application::getInstance()->getSettings()->isDebugMode())
		{
			$html = "<pre><b>Error[{$e->getCode()}]:</b> {$e->getMessage()}\n";
			$html .= "<b>Stack trace:</b>\n{$e->getTraceAsString()}\n";
			$html .= "<b>{$e->getFile()}: {$e->getLine()}</b></pre>";

			die($html);
		}

		die();
	}
}
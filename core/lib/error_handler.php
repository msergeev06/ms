<?php
/**
 * MSergeev\Core\Lib\ErrorHandler
 * Внутренний обработчик ошибок
 *
 * @package MSergeev\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace MSergeev\Core\Lib;

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
	 */
	public static function handler ($errNo, $errStr, $errFile=null, $errLine=null, $errContext=null)
	{

		if (!(error_reporting() & $errNo)) {

			// Этот код ошибки не включен в error_reporting,
			// так что пусть обрабатываются стандартным обработчиком ошибок PHP
			return false;
		}

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
	}
}
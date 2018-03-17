<?php
/**
 * Класс для работы с логами
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
use Ms\Core\Entity\Type\Date;

class Logs
{
	/**
	 * Возвращает путь к директории, где хранятся логи системы
	 *
	 * @return string
	 */
	public static function getLogsDir()
	{
		$dir = Application::getInstance()->getSettings()->getDirLogs();

		if (!file_exists($dir))
		{
			Files::createDir($dir);
			$data = 'Deny From All';
			Files::saveFile($dir.'/.htaccess',$data);
		}

		return $dir;
	}

	/**
	 * Создает запись в указанном лог файле, по умолчанию пишет в общий системный лог
	 *
	 * @param string $strMessage Текст для записи в лог
	 * @param string $nameLog    Название лог файла
	 * @param bool   $noDate     Флаг, не использовать текущую дату в логе
	 */
	public static function write2Log ($strMessage, $nameLog='sys', $noDate=false)
	{
		$now = new Date();
		$dir = static::getLogsDir();
		$filename = $dir.'/'.strtolower($nameLog);
		if (!$noDate)
		{
			$filename .= '_'.$now->format('Ymd');
		}
		$filename .= '.log';
		if ($f1 = @fopen($filename,'a'))
		{
			$tmp = explode(' ',microtime());
			$data = '';
			if ($noDate)
			{
				$data .= $now->format('Y-m-d').' ';
			}
			$data .= $now->format('H:i:s').' '.$tmp[0]."\t".$strMessage."\n-----------------------------\n";
			fwrite ($f1, $data);
			fclose($f1);
		}
	}
}

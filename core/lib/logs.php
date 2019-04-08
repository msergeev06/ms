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
use Ms\Core\Entity\ErrorCollection;
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
	 * Создает запись типа Debug в лог файлах, а также добавляет описание ошибки в коллекцию, если она указана
	 *
	 * Создает запись одновременно в общем файле логов и в специальном лог-файле
	 *
	 * @param string               $strMessage          Сообщение. Может содержать маркеры вида #MARKER#
	 *                                                  для замещения из массива arReplace
	 * @param array                $arReplace           Массив замен для сообщения. Также может содержать
	 *                                                  специализированные поля:
	 *                                                  ERROR_CODE - используется совместно с коллекцией ошибок, задавая
	 *                                                  код произошедшей ошибки/сообщения
	 *                                                  EXCEPTION - должно содержать объект исключения. Добавляет
	 *                                                  специфичные для исключения данные
	 * @param ErrorCollection|NULL &$errorCollection    Коллекция ошибок, передается по ссылке и в нее добавляется новыя
	 *                                                  ошибка с тем же сообщением, что идет в лог файл
	 * @param null|int             $iErrorNumber        Номер ошибки
	 * @param null|\Throwable      $exception           Исключение
	 */
	public static function setDebug ($strMessage, $arReplace=array (), ErrorCollection &$errorCollection=null,$iErrorNumber=null,\Throwable $exception=null)
	{
		static::setSpecial('debug',$strMessage,$arReplace,$errorCollection, $iErrorNumber, $exception);
	}

	/**
	 * Создает запись типа Info в лог файлах, а также добавляет описание ошибки в коллекцию, если она указана
	 *
	 * Создает запись одновременно в общем файле логов и в специальном лог-файле
	 *
	 * @param string               $strMessage          Сообщение. Может содержать маркеры вида #MARKER#
	 *                                                  для замещения из массива arReplace
	 * @param array                $arReplace           Массив замен для сообщения. Также может содержать
	 *                                                  специализированные поля:
	 *                                                  ERROR_CODE - используется совместно с коллекцией ошибок, задавая
	 *                                                  код произошедшей ошибки/сообщения
	 *                                                  EXCEPTION - должно содержать объект исключения. Добавляет
	 *                                                  специфичные для исключения данные
	 * @param ErrorCollection|NULL &$errorCollection    Коллекция ошибок, передается по ссылке и в нее добавляется новыя
	 *                                                  ошибка с тем же сообщением, что идет в лог файл
	 * @param null|int             $iErrorNumber        Номер ошибки
	 * @param null|\Throwable      $exception           Исключение
	 */
	public static function setInfo ($strMessage, $arReplace=array (), ErrorCollection &$errorCollection=null,$iErrorNumber=null,\Throwable $exception=null)
	{
		static::setSpecial('info',$strMessage,$arReplace,$errorCollection, $iErrorNumber, $exception);
	}

	/**
	 * Создает запись типа Notice в лог файлах, а также добавляет описание ошибки в коллекцию, если она указана
	 *
	 * Создает запись одновременно в общем файле логов и в специальном лог-файле
	 *
	 * @param string               $strMessage          Сообщение. Может содержать маркеры вида #MARKER#
	 *                                                  для замещения из массива arReplace
	 * @param array                $arReplace           Массив замен для сообщения. Также может содержать
	 *                                                  специализированные поля:
	 *                                                  ERROR_CODE - используется совместно с коллекцией ошибок, задавая
	 *                                                  код произошедшей ошибки/сообщения
	 *                                                  EXCEPTION - должно содержать объект исключения. Добавляет
	 *                                                  специфичные для исключения данные
	 * @param ErrorCollection|NULL &$errorCollection    Коллекция ошибок, передается по ссылке и в нее добавляется новыя
	 *                                                  ошибка с тем же сообщением, что идет в лог файл
	 * @param null|int             $iErrorNumber        Номер ошибки
	 * @param null|\Throwable      $exception           Исключение
	 */
	public static function setNotice ($strMessage, $arReplace=array (), ErrorCollection &$errorCollection=null,$iErrorNumber=null,\Throwable $exception=null)
	{
		static::setSpecial('notice',$strMessage,$arReplace,$errorCollection, $iErrorNumber, $exception);
	}

	/**
	 * Создает запись типа Warning в лог файлах, а также добавляет описание ошибки в коллекцию, если она указана
	 *
	 * Создает запись одновременно в общем файле логов и в специальном лог-файле
	 *
	 * @param string               $strMessage          Сообщение. Может содержать маркеры вида #MARKER#
	 *                                                  для замещения из массива arReplace
	 * @param array                $arReplace           Массив замен для сообщения. Также может содержать
	 *                                                  специализированные поля:
	 *                                                  ERROR_CODE - используется совместно с коллекцией ошибок, задавая
	 *                                                  код произошедшей ошибки/сообщения
	 *                                                  EXCEPTION - должно содержать объект исключения. Добавляет
	 *                                                  специфичные для исключения данные
	 * @param ErrorCollection|NULL &$errorCollection    Коллекция ошибок, передается по ссылке и в нее добавляется новыя
	 *                                                  ошибка с тем же сообщением, что идет в лог файл
	 * @param null|int             $iErrorNumber        Номер ошибки
	 * @param null|\Throwable      $exception           Исключение
	 */
	public static function setWarning ($strMessage, $arReplace=array (), ErrorCollection &$errorCollection=null,$iErrorNumber=null,\Throwable $exception=null)
	{
		static::setSpecial('warning',$strMessage,$arReplace,$errorCollection, $iErrorNumber, $exception);
	}

	/**
	 * Создает запись типа Error в лог файлах, а также добавляет описание ошибки в коллекцию, если она указана
	 *
	 * Создает запись одновременно в общем файле логов и в специальном лог-файле
	 *
	 * @param string               $strMessage          Сообщение. Может содержать маркеры вида #MARKER#
	 *                                                  для замещения из массива arReplace
	 * @param array                $arReplace           Массив замен для сообщения. Также может содержать
	 *                                                  специализированные поля:
	 *                                                  ERROR_CODE - используется совместно с коллекцией ошибок, задавая
	 *                                                  код произошедшей ошибки/сообщения
	 *                                                  EXCEPTION - должно содержать объект исключения. Добавляет
	 *                                                  специфичные для исключения данные
	 * @param ErrorCollection|NULL &$errorCollection    Коллекция ошибок, передается по ссылке и в нее добавляется новыя
	 *                                                  ошибка с тем же сообщением, что идет в лог файл
	 * @param null|int             $iErrorNumber        Номер ошибки
	 * @param null|\Throwable      $exception           Исключение
	 */
	public static function setError ($strMessage, $arReplace=array (), ErrorCollection &$errorCollection=null,$iErrorNumber=null,\Throwable $exception=null)
	{
		static::setSpecial('error',$strMessage,$arReplace,$errorCollection, $iErrorNumber, $exception);
	}

	/**
	 * Создает запись типа Critical в лог файлах, а также добавляет описание ошибки в коллекцию, если она указана
	 *
	 * Создает запись одновременно в общем файле логов и в специальном лог-файле
	 *
	 * @param string               $strMessage          Сообщение. Может содержать маркеры вида #MARKER#
	 *                                                  для замещения из массива arReplace
	 * @param array                $arReplace           Массив замен для сообщения. Также может содержать
	 *                                                  специализированные поля:
	 *                                                  ERROR_CODE - используется совместно с коллекцией ошибок, задавая
	 *                                                  код произошедшей ошибки/сообщения
	 *                                                  EXCEPTION - должно содержать объект исключения. Добавляет
	 *                                                  специфичные для исключения данные
	 * @param ErrorCollection|NULL &$errorCollection    Коллекция ошибок, передается по ссылке и в нее добавляется новыя
	 *                                                  ошибка с тем же сообщением, что идет в лог файл
	 * @param null|int             $iErrorNumber        Номер ошибки
	 * @param null|\Throwable      $exception           Исключение
	 */
	public static function setCritical ($strMessage, $arReplace=array (), ErrorCollection &$errorCollection=null,$iErrorNumber=null,\Throwable $exception=null)
	{
		static::setSpecial('critical',$strMessage,$arReplace,$errorCollection, $iErrorNumber, $exception);
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
			$data .= $now->format('H:i:s')."\t".$tmp[0]."\t".$strMessage."\n------------------------------\n";
			fwrite ($f1, $data);
			fclose($f1);
		}
	}

	private static function setSpecial ($type,$strMessage,$arReplace=array (),ErrorCollection &$errorCollection=null,$iErrorNumber=null,\Throwable $exception=null)
	{
		if (isset($arReplace['EXCEPTION']))
		{
			if (is_null($exception))
			{
				$exception = $arReplace['EXCEPTION'];
			}
			unset($arReplace['EXCEPTION']);
		}
		if (isset($arReplace['ERROR_CODE']))
		{
			if (is_null($iErrorNumber))
			{
				$iErrorNumber = $arReplace['ERROR_CODE'];
			}
			unset($arReplace['ERROR_CODE']);
		}
		if (!empty($arReplace))
		{
			foreach ($arReplace as $code=>$replace)
			{
				$strMessage = str_replace('#'.$code.'#',$replace,$strMessage);
			}
		}
		if (is_null($errorCollection))
		{
			$errorCollection = new ErrorCollection();
		}
		$errorCollection->setError($strMessage,((int)$iErrorNumber>0)?$iErrorNumber:null);
		if (!is_null($exception))
		{
			$strMessage.="\n".$exception->getMessage().' ('.$exception->getFile().': '.$exception->getLine().")\n"
				.$exception->getTraceAsString();
		}
		$tmp = strtoupper($type);
		if (!is_null($iErrorNumber))
		{
			$tmp .= '['.$iErrorNumber.']';
		}
		$tmp .= ":\t".$strMessage;
		$strMessage = $tmp;
		//Вызываем события добавления сообщения в логи
		switch (strtoupper($type))
		{
			case 'DEBUG':
				Events::runEvents('core','OnAddDebugMessageToLog',array ($strMessage));
				break;
			case 'INFO':
				Events::runEvents('core','OnAddInfoMessageToLog',array ($strMessage));
				break;
			case 'NOTICE':
				Events::runEvents('core','OnAddNoticeMessageToLog',array ($strMessage));
				break;
			case 'WARNING':
				Events::runEvents('core','OnAddWarningMessageToLog',array ($strMessage));
				break;
			case 'CRITICAL':
				Events::runEvents('core','OnAddCriticalMessageToLog',array ($strMessage));
				break;
			default://ERROR
				Events::runEvents('core','OnAddErrorMessageToLog',array ($strMessage));
				break;
		}
		//Пишем лог в общий файл логов
		static::write2Log($strMessage);
		//Пишем лог в файл info-логов
		$date = new Date();
		$sDate = $date->format('Y-m');
		static::write2Log($strMessage, $type.'_'.$sDate,true);
	}
}

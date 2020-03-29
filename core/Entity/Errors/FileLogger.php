<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Errors;

use Ms\Core\Entity\Application;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exception\ArgumentException;
use Ms\Core\Exception\ArgumentOutOfRangeException;
use Ms\Core\Interfaces\ILogger;
use Ms\Core\Lib\IO\Files;
use Ms\Core\Lib\Loader;
use Ms\Core\Lib\Modules;

/**
 * Класс Ms\Core\Entity\Errors\FileLogger
 * Логирование в файлы
 */
class FileLogger implements ILogger
{
	const PERIOD_DAILY = 'daily';
	const PERIOD_MONTHLY = 'monthly';

	const PERIODS_LIST = [
		self::PERIOD_DAILY,
		self::PERIOD_MONTHLY
	];

	const TYPE_ERROR = 'error';
	const TYPE_DEBUG = 'debug';
	const TYPE_NOTICE = 'notice';

	const TYPES_LIST = [
		self::TYPE_ERROR,
		self::TYPE_DEBUG,
		self::TYPE_NOTICE
	];

	protected $module = null;
	protected $logsDir = null;
	protected $period = null;
	protected $type = null;
	protected $logFileName = null;
	protected $prefix = null;

	/**
	 * Logger constructor.
	 *
	 * @param string      $module  Имя модуля, для которого создается лог
	 * @param string      $type    Тип лога (Ошибка/Отладка/Уведомление)
	 * @param string      $period  Период лога (Ежедневный/Ежемесячный)
	 * @param string      $prefix  Префикс для файла лога
	 * @param string|null $logsDir Путь к файлам лога
	 *
	 * @return FileLogger
	 * @throws ArgumentException
	 * @throws ArgumentOutOfRangeException
	 */
	public function __construct (string $module, string $type = 'error', string $period = 'daily', string $prefix = '', string $logsDir = null)
	{
		$this->setModule ($module);
		$this->setType ($type);
		$this->setPeriod ($period);
		$this->setLogsDir ($logsDir);
		$this->setPrefix ($prefix);
		$this->setLogFileName ();

		return $this;
	}

	//<editor-fold defaultstate="collapse" desc=">>> Getters and Setters">
	/**
	 * Устанавливает имя модуля, для которого создается лог
	 *
	 * @param string $module Имя модуля
	 *
	 * @return FileLogger
	 * @throws ArgumentException
	 */
	public function setModule (string $module)
	{
		if ($module == 'old_system' || $module == 'system' || $module == 'core')
		{
			$this->module = $module;
		}
		elseif (Modules::checkModuleName($module) && Loader::issetModule($module))
		{
			$this->module = $module;
		}
		else
		{
			throw new ArgumentException('Неверное имя модуля! Модуля с именем "'.$module.'" не существует');
		}

		return $this;
	}

	/**
	 * Возвращает установленное имя модуля
	 *
	 * @return string
	 */
	public function getModule ()
	{
		return $this->module;
	}

	/**
	 * Устанавливает один из возможных типов логов
	 *
	 * @param string $type Тип лога
	 *
	 * @return FileLogger
	 * @throws ArgumentOutOfRangeException
	 */
	public function setType (string $type): FileLogger
	{
		$type = strtolower ($type);
		if (!in_array($type,self::TYPES_LIST))
		{
			throw new ArgumentOutOfRangeException('Log type',self::TYPES_LIST);
		}
		else
		{
			$this->type = $type;
		}

		return $this;
	}

	/**
	 * Устанавливает тип лога "Ошибка"
	 *
	 * @return FileLogger
	 */
	public function setTypeError (): FileLogger
	{
		try
		{
			$this->setType(self::TYPE_ERROR);
		}
		catch (ArgumentOutOfRangeException $e){}

		return $this;
	}

	/**
	 * Устанавливает тип лога "Отладка"
	 *
	 * @return FileLogger
	 */
	public function setTypeDebug (): FileLogger
	{
		try
		{
			$this->setType(self::TYPE_DEBUG);
		}
		catch (ArgumentOutOfRangeException $e){}

		return $this;
	}

	/**
	 * Устанавливает тип лога "Уведомление"
	 *
	 * @return FileLogger
	 */
	public function setTypeNotice (): FileLogger
	{
		try
		{
			$this->setType(self::TYPE_NOTICE);
		}
		catch (ArgumentOutOfRangeException $e){}

		return $this;
	}

	/**
	 * Возвращает тип лога
	 *
	 * @return string
	 */
	public function getType (): string
	{
		return (string)$this->type;
	}

	/**
	 * Устанавливает один из возможных периодов логов
	 *
	 * @param string $period Период ведения лога (ежедневный/ежемесячный)
	 *
	 * @return FileLogger
	 * @throws ArgumentOutOfRangeException
	 */
	public function setPeriod (string $period): FileLogger
	{
		$period = strtolower($period);
		if (!in_array($period,self::PERIODS_LIST))
		{
			throw new ArgumentOutOfRangeException('Log period', self::PERIODS_LIST);
		}
		else
		{
			$this->period = $period;
		}

		return $this;
	}

	/**
	 * Устанавливает период лога "Ежедневный"
	 *
	 * @return FileLogger
	 */
	public function setPeriodDaily (): FileLogger
	{
		try
		{
			$this->setPeriod(self::PERIOD_DAILY);
		}
		catch (ArgumentOutOfRangeException $e){}

		return $this;
	}

	/**
	 * Устанавливает период лога "Ежемесячный"
	 *
	 * @return FileLogger
	 */
	public function setPeriodMonthly (): FileLogger
	{
		try
		{
			$this->setPeriod(self::PERIOD_MONTHLY);
		}
		catch (ArgumentOutOfRangeException $e){}

		return $this;
	}

	/**
	 * Возвращает период ведения лога
	 *
	 * @return string
	 */
	public function getPeriod (): string
	{
		return $this->period;
	}

	/**
	 * Устанавливает путь к директории, хранящей файлы лога
	 * Если директория не задана, она берется из системных настроек
	 *
	 * @param string|null $logsDir Путь к директории с логами
	 *
	 * @return FileLogger
	 */
	public function setLogsDir (string $logsDir = null): FileLogger
	{
		if (is_null($logsDir))
		{
			$logsDir = Application::getInstance()->getSettings()->getDirLogs();
		}

		if (!file_exists($logsDir))
		{
			Files::createDir($logsDir);
			$data = 'Deny From All';
			Files::saveFile($logsDir.'/.htaccess',$data);
		}
		foreach (self::PERIODS_LIST as $period)
		{
			if (!file_exists($logsDir . '/' . $period))
			{
				Files::createDir($logsDir . '/' . $period);
				$data = 'Deny From All';
				Files::saveFile($logsDir . '/' . $period . '/.htaccess',$data);
			}
		}

		$this->logsDir = $logsDir;

		return $this;
	}

	/**
	 * Возвращает путь к директории с логами
	 *
	 * @return string
	 */
	public function getLogsDir (): string
	{
		return $this->logsDir;
	}

	protected function setPrefix (string $prefix)
	{
		$prefix = strtolower((string)$prefix);
		if ($prefix != '')
		{
			$this->prefix .= '_';
		}
		else
		{
			$this->prefix = '';
		}

		return $this;
	}

	/**
	 * Устанавливает имя файла лога
	 *
	 * @param string|null $prefix Префикс для файла лога
	 *
	 * @return FileLogger
	 */
	public function setLogFileName (string $prefix = null): FileLogger
	{
		if (!is_null($prefix))
		{
			$this->setPrefix ($prefix);
		}
		$now = new Date();
		$fileName = $this->prefix . $this->type . '_';
		if ($this->period == self::PERIOD_MONTHLY)
		{
			$fileName .= $now->format('Y-m');
		}
		else
		{
			$fileName .= $now->format('Y-m-d');
		}
		$fileName .= '.txt';

		$this->logFileName = $fileName;

		return $this;
	}

	/**
	 * Возвращает имя файла лога
	 *
	 * @return string
	 */
	public function getLogFileName (): string
	{
		return (string)$this->logFileName;
	}
	//</editor-fold>

	/**
	 * Добавляет сообщение в лог-файл
	 *
	 * @param string $strMessage    Сообщение
	 * @param array  $arReplace     Массив замен переменных значений в сообщении
	 *
	 * @return FileLogger
	 */
	public function addMessage (string $strMessage, array $arReplace = []): FileLogger
	{
		if (!empty($arReplace))
		{
			foreach ($arReplace as $code=>$value)
			{
				$strMessage = str_replace('#' . $code . '#', $value, $strMessage);
			}
		}
		$now = new Date();

		if ($f1 = @fopen ($this->logsDir . '/' . $this->period . '/' . $this->logFileName, 'a'))
		{
			$tmp = explode(' ',microtime());
			$data = '';
			if ($this->period != self::PERIOD_DAILY)
			{
				$data .= $now->format('Y-m-d').' ';
			}
			$data .= $now->format('H:i:s')."\t".$tmp[0]."\t".'['.$this->module."]\t".$strMessage."\n";
			if ($this->type != self::TYPE_DEBUG)
			{
				$data .= "------------------------------\n";
			}
			fwrite ($f1, $data);
			fclose($f1);
		}

		return $this;
	}

	/**
	 * Добавляет сообщение в лог-файл другого типа
	 *
	 * @param string $type          Тип лог-файла
	 * @param string $strMessage    Сообщение
	 * @param array  $arReplace     Массив замен в сообщении
	 *
	 * @return FileLogger
	 */
	public function addMessageOtherType (string $type, string $strMessage, array $arReplace = [])
	{
		$currentType = $this->type;
		$bOk = false;
		try
		{
			$this->setType ($type);
			$this->setLogFileName ();
			$bOk = true;
		}
		catch (ArgumentOutOfRangeException $e)
		{
			$bOk = false;
		}
		if ($bOk)
		{
			$this->addMessage ($strMessage, $arReplace);
			$this->type = $currentType;
			$this->setLogFileName ();
		}

		return $this;
	}


}
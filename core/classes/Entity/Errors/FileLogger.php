<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Errors;

use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Arguments\ArgumentException;
use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Interfaces\ILogger;
use Ms\Core\Lib\IO\Files;
use Ms\Core\Entity\Modules\Loader;
use Ms\Core\Entity\Modules\Modules;

/**
 * Класс Ms\Core\Entity\Errors\FileLogger
 * Логирование в файлы
 */
class FileLogger implements ILogger
{
    const PERIOD_DAILY   = 'daily';

    const PERIOD_MONTHLY = 'monthly';

    const PERIODS_LIST   = [
        self::PERIOD_DAILY,
        self::PERIOD_MONTHLY
    ];

    const TYPE_ERROR     = 'error';//баги

    const TYPE_DEBUG     = 'debug';//подробные уведомления, которые помогают отладить систему

    const TYPE_NOTICE    = 'notice';//системные уведомления

    const TYPE_INFO	 = 'info';//сообщения о процессах в системе

    const TYPE_WARN	 = 'warn';//предупреждение от системы

    const TYPES_LIST     = [
        self::TYPE_ERROR,
        self::TYPE_DEBUG,
        self::TYPE_NOTICE,
	self::TYPE_INFO,
	self::TYPE_WARN
    ];
    protected $logFileName = null;
    protected $logsDir     = null;
    protected $module      = null;
    protected $period      = null;
    protected $prefix      = null;
    protected $type        = null;

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
     */
    public function __construct (
        string $module, string $type = 'error', string $period = 'daily', string $prefix = '', string $logsDir = null
    ) {
        try
        {
            $this->setModule($module);
            $this->setType($type);
            $this->setPeriod($period);
        }
        catch (SystemException $e)
        {
        }
        $this->setLogsDir($logsDir);
        $this->setPrefix($prefix);
        $this->setLogFileName();

        return $this;
    }

    /**
     * Добавляет сообщение в лог-файл
     *
     * @param string $strMessage Сообщение
     * @param array  $arReplace  Массив замен переменных значений в сообщении
     *
     * @return $this
     * @unittest
     */
    public function addMessage (string $strMessage, array $arReplace = []): FileLogger
    {
        if (!empty($arReplace))
        {
            foreach ($arReplace as $code => $value)
            {
                $strMessage = str_replace('#' . $code . '#', $value, $strMessage);
            }
        }

        if ($f1 = @fopen($this->logsDir . '/' . $this->period . '/' . $this->logFileName, 'a'))
        {
            $tmp = explode(' ', microtime());
            $data = '';
            try
            {
                $now = new Date();
                if ($this->period != self::PERIOD_DAILY)
                {
                    $data .= $now->format('Y-m-d') . ' ';
                }
                $data .= $now->format('H:i:');
                $data .= (string)((int)$now->format('s') + (float)$tmp[0]);
            }
            catch (SystemException $e)
            {
            }
            $data .= "\t" . '[' . $this->module . "]\t" . $strMessage . "\n";
            if ($this->type != self::TYPE_DEBUG)
            {
                $data .= "------------------------------\n";
            }
            fwrite($f1, $data);
            fclose($f1);
        }

        return $this;
    }

    /**
     * Добавляет сообщение в лог-файл другого типа
     *
     * @param string $type       Тип лог-файла
     * @param string $strMessage Сообщение
     * @param array  $arReplace  Массив замен в сообщении
     *
     * @return $this
     * @unittest
     */
    public function addMessageOtherType (string $type, string $strMessage, array $arReplace = [])
    {
        $currentType = $this->type;
        try
        {
            $this->setType($type);
            $bOk = true;
        }
        catch (ArgumentOutOfRangeException $e)
        {
            $bOk = false;
        }
        if ($bOk)
        {
            $this->addMessage($strMessage, $arReplace);
            try
            {
                $this->setType($currentType);
            }
            catch (ArgumentOutOfRangeException $e)
            {
            }
        }

        return $this;
    }

    /**
     * Возвращает имя файла лога
     *
     * @return string
     * @unittest
     */
    public function getLogFileName (): string
    {
        return (string)$this->logFileName;
    }

    /**
     * Возвращает путь к директории с логами
     *
     * @return string
     * @unittest
     */
    public function getLogsDir (): string
    {
        return $this->logsDir;
    }

    /**
     * Возвращает установленное имя модуля
     *
     * @return string
     * @unittest
     */
    public function getModule ()
    {
        return $this->module;
    }

    /**
     * Возвращает период ведения лога
     *
     * @return string
     * @unittest
     */
    public function getPeriod (): string
    {
        return $this->period;
    }

    /**
     * Возвращает тип лога
     *
     * @return string
     * @unittest
     */
    public function getType (): string
    {
        return (string)$this->type;
    }

    /**
     * Устанавливает имя файла лога
     *
     * @param string|null $prefix Префикс для файла лога
     *
     * @return $this
     * @unittest
     */
    public function setLogFileName (string $prefix = null): FileLogger
    {
        if (!is_null($prefix))
        {
            $this->setPrefix($prefix);
        }
        $fileName = $this->prefix . $this->type . '_';
        try
        {
            $now = new Date();
            if ($this->period == self::PERIOD_MONTHLY)
            {
                $fileName .= $now->format('Y-m');
            }
            else
            {
                $fileName .= $now->format('Y-m-d');
            }
        }
        catch (SystemException $e)
        {
            return $this;
        }
        $fileName .= '.txt';

        $this->logFileName = $fileName;

        return $this;
    }

    /**
     * Устанавливает путь к директории, хранящей файлы лога
     * Если директория не задана, она берется из системных настроек
     *
     * @param string|null $logsDir Путь к директории с логами
     *
     * @return $this
     * @unittest
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
            Files::saveFile($logsDir . '/.htaccess', $data);
        }
        foreach (self::PERIODS_LIST as $period)
        {
            if (!file_exists($logsDir . '/' . $period))
            {
                Files::createDir($logsDir . '/' . $period);
                $data = 'Deny From All';
                Files::saveFile($logsDir . '/' . $period . '/.htaccess', $data);
            }
        }

        $this->logsDir = $logsDir;

        return $this;
    }

    /**
     * Устанавливает имя модуля, для которого создается лог
     *
     * @param string $module Имя модуля
     *
     * @return $this
     * @throws ArgumentException
     * @unittest
     */
    public function setModule (string $module)
    {
        if ($module == 'old_system' || $module == 'system' || $module == 'core' || $module == 'ms.core')
        {
            $this->module = $module;
        }
        elseif (Modules::getInstance()->checkModuleName($module) && Loader::issetModule($module))
        {
            $this->module = $module;
        }
        else
        {
            throw new ArgumentException('Неверное имя модуля! Модуля с именем "' . $module . '" не существует');
        }

        return $this;
    }

    /**
     * Устанавливает один из возможных периодов логов
     *
     * @param string $period Период ведения лога (ежедневный/ежемесячный)
     *
     * @return $this
     * @throws ArgumentOutOfRangeException
     * @unittest
     */
    public function setPeriod (string $period): FileLogger
    {
        $period = strtolower($period);
        if (!in_array($period, self::PERIODS_LIST))
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
     * @return $this
     * @unittest
     */
    public function setPeriodDaily (): FileLogger
    {
        try
        {
            $this->setPeriod(self::PERIOD_DAILY);
        }
        catch (ArgumentOutOfRangeException $e)
        {
        }

        return $this;
    }

    /**
     * Устанавливает период лога "Ежемесячный"
     *
     * @return $this
     * @unittest
     */
    public function setPeriodMonthly (): FileLogger
    {
        try
        {
            $this->setPeriod(self::PERIOD_MONTHLY);
        }
        catch (ArgumentOutOfRangeException $e)
        {
        }

        return $this;
    }

    /**
     * Устанавливает один из возможных типов логов
     *
     * @param string $type Тип лога
     *
     * @return $this
     * @throws ArgumentOutOfRangeException
     * @unittest
     */
    public function setType (string $type): FileLogger
    {
        $type = strtolower($type);
        if (!in_array($type, self::TYPES_LIST))
        {
            throw new ArgumentOutOfRangeException('Log type', self::TYPES_LIST);
        }
        else
        {
            $this->type = $type;
        }
        $this->setLogFileName();

        return $this;
    }

    /**
     * Устанавливает тип лога "Отладка"
     *
     * @return $this
     * @unittest
     */
    public function setTypeDebug (): FileLogger
    {
        try
        {
            $this->setType(self::TYPE_DEBUG);
        }
        catch (ArgumentOutOfRangeException $e)
        {
        }

        return $this;
    }

    /**
     * Устанавливает тип лога "Ошибка"
     *
     * @return $this
     * @unittest
     */
    public function setTypeError (): FileLogger
    {
        try
        {
            $this->setType(self::TYPE_ERROR);
        }
        catch (ArgumentOutOfRangeException $e)
        {
        }

        return $this;
    }

    /**
     * Устанавливает тип лога "Уведомление"
     *
     * @return $this
     * @unittest
     */
    public function setTypeNotice (): FileLogger
    {
        try
        {
            $this->setType(self::TYPE_NOTICE);
        }
        catch (ArgumentOutOfRangeException $e)
        {
        }

        return $this;
    }

    /**
     * Устанавливает префикс файла лога
     *
     * @param string $prefix
     *
     * @return $this
     */
    protected function setPrefix (string $prefix)
    {
        $prefix = strtolower((string)$prefix);
        if ($prefix != '')
        {
            $this->prefix = $prefix . '_';
        }
        else
        {
            $this->prefix = '';
        }

        return $this;
    }
}

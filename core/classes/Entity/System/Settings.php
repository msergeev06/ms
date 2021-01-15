<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

use Ms\Core\Entity\Type\Date;

/**
 * Класс Ms\Core\Entity\System\Settings
 * Класс хранения настроек системы
 */
class Settings extends Multiton
{
    /**
     * Имя модуля должно быть вида:
     * [буква][буква|цифра|ничего].[буква][буква|цифра|подчеркивание|ничего]
     * первый символ только буквы от a до z
     * далее любое количество символов (буквы a-z и цифры 0-9)
     * точка (.) в качестве разделителя бренда и имени модуля
     * после точки первый символ - буква от a до z
     * далее любое количество символов (буквы a-z, цифры 0-9 и символ подчёркивания)
     */
    const MODULE_NAME_REGULAR_EXPRESSION = '/^([a-z]{1}[a-z0-9]*)[.]{1}([a-z]{1}[a-z0-9_]*)$/';

    /**
     * Максимально допустимая длина имени модуля, включая бренд
     */
    const MAX_LENGTH_MODULE_NAME = 100;

    /** @var array Массив настроек */
    protected $arSettings = [];

    /** @var null|string DOCUMENT_ROOT */
    protected $documentRoot = null;

    /** @var bool Флаг использования кодировки UTF-8 */
    protected $useUtf8 = true;

    /**
     * Возвращает DocumentRoot
     *
     * @return string
     */
    public function getDocumentRoot ()
    {
        if (is_null($this->documentRoot))
        {
            if (empty($_SERVER['DOCUMENT_ROOT']))
            {
                if (
                    isset($this->arSettings['Paths']['DocumentRoot'])
                    && !empty($this->arSettings['Paths']['DocumentRoot'])
                ) {
                    $this->documentRoot = $this->parseSetting($this->arSettings['Paths']['DocumentRoot']);
                }

                $_SERVER['DOCUMENT_ROOT'] = $this->documentRoot;
            }
            else
            {
                $this->documentRoot = $_SERVER['DOCUMENT_ROOT'];
            }
        }

        return $this->documentRoot;
    }

    /**
     * Обрабатывает настройку, заменяя шаблон {Level1:Level2} на значение настройки arSettings['Level1']['Level2']
     *
     * @param mixed $setting Необработанное значение настройки
     *
     * @return mixed
     */
    protected function parseSetting ($setting)
    {
        if (is_string($setting))
        {
            if (preg_match_all('/\{([A-Za-z]+):([A-Za-z]+)\}/',$setting, $m))
            {
                for ($i=0; $i<count($m[1]); $i++)
                {
                    if (isset($this->arSettings[$m[1][$i]][$m[2][$i]]))
                    {
                        $setting = str_replace(
                            '{' . $m[1][$i] . ':' . $m[2][$i] . '}',
                            $this->arSettings[$m[1][$i]][$m[2][$i]],
                            $setting
                        );
                    }
                    else
                    {
                        $setting = str_replace(
                            '{' . $m[1][$i] . ':' . $m[2][$i] . '}',
                            '',
                            $setting
                        );
                    }
                }
            }
        }

        return $setting;
    }

    /**
     * Возвращает папку, где установлена системы. Указывается относительно корня.
     * Если система установлена в корне - равен пустой строке
     *
     * @return string
     */
    public function getInstalledDir ()
    {
        return isset($this->arSettings['Paths']['InstalledDir'])
            ? $this->parseSetting($this->arSettings['Paths']['InstalledDir'])
            : ''
        ;
    }

    /**
     * Возвращает абсолютный путь к локальной директории
     *
     * @return string
     */
    public function getLocalRoot()
    {
        $docRoot = $this->getDocumentRoot();
        return (
                isset($this->arSettings['Paths']['LocalRoot'])
                && !empty($this->arSettings['Paths']['LocalRoot'])
            )
            ? $this->parseSetting($this->arSettings['Paths']['LocalRoot'])
            : $docRoot . $this->getInstalledDir() . '/local'
        ;
    }

    /**
     * Возвращает абсолютный путь к системной директории ms
     *
     * @return string
     */
    public function getMsRoot()
    {
        $docRoot = $this->getDocumentRoot();
        return (
                isset($this->arSettings['Paths']['MsRoot'])
                && !empty($this->arSettings['Paths']['MsRoot'])
            )
            ? $this->parseSetting($this->arSettings['Paths']['MsRoot'])
            : $docRoot . $this->getInstalledDir() . '/ms'
        ;
    }

    /**
     * Возвращает протокол, по которому работает сайт (http или https)
     *
     * @return string
     */
    public function getSiteProtocol()
    {
        return (
                isset($this->arSettings['System']['Protocol'])
                && !empty($this->arSettings['System']['Protocol'])
            )
            ? $this->parseSetting($this->arSettings['System']['Protocol'])
            : 'http'
        ;
    }

    /**
     * Возвращает TRUE, если необходимо проверить необходимость произвести HTTP авторизацию, иначе FALSE
     *
     * @return bool
     */
    public function isNeedCheckAuth ()
    {
        $bNeed = isset($this->arSettings['System']['NeedCheckAuth'])
            ? $this->arSettings['System']['NeedCheckAuth']
            : true
        ;

        if ($bNeed)
        {
            if (defined('NO_HTTP_AUTH') && NO_HTTP_AUTH === true)
            {
                $bNeed = false;
            }
        }

        return $bNeed;
    }

    /**
     * Возвращает префикс для cookie
     *
     * @return string
     */
    public function getCookiePrefix ()
    {
        return isset($this->arSettings['System']['CookiePrefix'])
            ? $this->parseSetting($this->arSettings['System']['CookiePrefix'])
            : 'ms'
        ;
    }

    /**
     * Возвращает IP адрес домашней сети
     *
     * @return string
     */
    public function getHomeNetwork ()
    {
        return (
                isset($this->arSettings['System']['HomeNetwork'])
                && !empty($this->arSettings['System']['HomeNetwork'])
            )
            ? $this->parseSetting($this->arSettings['System']['HomeNetwork'])
            : '192.168.0.*'
        ;
    }

    /**
     * Возвращает абсолютный путь к основной папке сайта
     *
     * @return string
     */
    public function getSiteUrl()
    {
        return (
                isset($this->arSettings['System']['SiteUrl'])
                && !empty($this->arSettings['System']['SiteUrl'])
            )
            ? $this->parseSetting($this->arSettings['System']['SiteUrl'])
            : $_SERVER['HTTP_HOST']
        ;
    }

    /**
     * Возвращает абсолютный путь к папке upload
     *
     * @return string
     */
    public function getUploadDir ()
    {
        $localRoot = $this->getDocumentRoot();
        return (
                isset($this->arSettings['Paths']['UploadDir'])
                && !empty($this->arSettings['Paths']['UploadDir'])
            )
            ? $this->parseSetting($this->arSettings['Paths']['UploadDir'])
            : $localRoot.'/upload'
        ;
    }

    /**
     * Возвращает абсолютный путь к папке ядра
     *
     * @return string
     */
    public function getCoreRoot ()
    {
        return $this->getMsRoot().'/core';
    }

    /**
     * Возвращает абсолютный путь к системным шаблонам
     *
     * @return string
     */
    public function getTemplatesRoot ()
    {
        return $this->getMsRoot().'/templates';
    }

    /**
     * Возвращает абсолютный путь к пользовательским шаблонам
     *
     * @return string
     */
    public function getLocalTemplatesRoot ()
    {
        return $this->getLocalRoot().'/templates';
    }

    /**
     * Возвращает абсолютный путь к системным модулям
     *
     * @return string
     */
    public function getModulesRoot ()
    {
        return $this->getMsRoot().'/modules';
    }

    /**
     * Возвращает абсолютный путь к пользовательским модулям
     *
     * @return string
     */
    public function getLocalModulesRoot ()
    {
        return $this->getLocalRoot().'/modules';
    }

    /**
     * Возвращает абсолютный путь к системным компонентам
     *
     * @return string
     */
    public function getComponentsRoot ()
    {
        return $this->getMsRoot().'/components';
    }

    /**
     * Возвращает абсолютный путь к пользовательским компонентам
     *
     * @return string
     */
    public function getLocalComponentsRoot ()
    {
        return $this->getLocalRoot().'/components';
    }

    /**
     * Инициализирует объект настроек
     *
     * @param array $arSettings Массив основных настроек
     */
    public function init(array $arSettings)
    {
        $this->arSettings = $arSettings;
        $this->mergeLocalSettings();

        if ($this->isCharsetUtf8())
        {
            $this->useUtf8 = true;
        }
    }

    /**
     * Возвращает абсолютный путь к папке с изображениями модулей
     *
     * @return string
     */
    public function getModulesImages ()
    {
        return $this->getMsRoot().'/images';
    }

    /**
     * Возвращает текущий язык сайта
     *
     * @return string
     */
    public function getSiteLang ()
    {
        return (
                isset($this->arSettings['System']['Lang'])
                && !empty($this->arSettings['System']['Lang'])
            )
            ? strtolower($this->parseSetting($this->arSettings['System']['Lang']))
            : 'ru'
        ;
    }

    /**
     * Возвращает текущую кодировку сайта
     *
     * @return string
     */
    public function getCharset ()
    {
        return (
                isset($this->arSettings['System']['Charset'])
                && !empty($this->arSettings['System']['Charset'])
            )
            ? strtoupper($this->parseSetting($this->arSettings['System']['Charset']))
            : 'UTF-8'
        ;
    }

    /**
     * Возвращает TRUE, если установлена кодировка UTF-8, иначе FALSE
     *
     * @return bool
     */
    public function isCharsetUtf8 ()
    {
        // return (strtolower($this->getCharset())=='utf-8');
        //Кодировка всегда UTF-8
        return true;
    }

    /**
     * Возвращает флаг отключения Iconv
     *
     * @return bool
     */
    public function isDisableIconv ()
    {
        return isset($this->arSettings['System']['DisableIconv'])
            ? $this->arSettings['System']['DisableIconv']
            : false
        ;
    }

    /**
     * Возвращает имя шаблона сайта по-умолчанию
     *
     * @return string
     */
    public function getTemplate ()
    {
        return (
                isset($this->arSettings['System']['Template'])
                && !empty($this->arSettings['System']['Template'])
            )
            ? $this->parseSetting($this->arSettings['System']['Template'])
            : '.default'
        ;
    }

    /**
     * Возвращает настройки CHMOD для файла
     *
     * @return int
     */
    public function getChmodFile ()
    {
        return isset($this->arSettings['Files']['ChmodFile'])
            ? $this->arSettings['Files']['ChmodFile']
            : 0666
        ;
    }

    /**
     * Возвращает настройки CHMOD для директории
     *
     * @return int
     */
    public function getChmodDir ()
    {
        return isset($this->arSettings['Files']['ChmodDir'])
            ? $this->arSettings['Files']['ChmodDir']
            : 0777
        ;
    }

    /**
     * Возвращает абсолютный путь к директории кеша
     *
     * @return string
     */
    public function getCacheDir ()
    {
        return (
                isset($this->arSettings['Paths']['CacheDir'])
                && !empty($this->arSettings['Paths']['CacheDir'])
            )
            ? $this->parseSetting($this->arSettings['Paths']['CacheDir'])
            : $this->getLocalRoot().'/cached'
        ;
    }

    /**
     * Возвращает абсолютный путь к папке резервных копий базы данных
     *
     * @return string
     */
    public function getDirBackupDb ()
    {
        return (
                isset($this->arSettings['Paths']['DirBackupDb'])
                && !empty($this->arSettings['Paths']['DirBackupDb'])
            )
            ? $this->parseSetting($this->arSettings['Paths']['DirBackupDb'])
            : $this->getLocalRoot().'/backup_db'
        ;
    }

    /**
     * Возвращает абсолютный путь к папке резервных копий
     *
     * @return string
     */
    public function getDirBackup ()
    {
        return (
                isset($this->arSettings['Paths']['BackupFiles'])
                && !empty($this->arSettings['Paths']['BackupFiles'])
            )
            ? $this->parseSetting($this->arSettings['Paths']['BackupFiles'])
            : $this->getLocalRoot().'/backup'
        ;
    }

    /**
     * Вовзращает время хранения резервных файлов в днях
     *
     * @return int
     */
    public function getExpireBackupFiles ()
    {
        return isset($this->arSettings['Backup']['ExpireBackupFiles'])
            ? $this->arSettings['Backup']['ExpireBackupFiles']
            : 5
        ;
    }

    /**
     * Возвращает флаг режима отладки
     *
     * @return bool
     */
    public function isDebugMode ()
    {
        return (
                isset($this->arSettings['Debug']['DebugMode'])
                && $this->arSettings['Debug']['DebugMode']===true
            )
            ? true
            : false
        ;
    }

    /**
     * Возвращает абсолютный путь к каталогу логов
     *
     * @return string
     */
    public function getDirLogs ()
    {
        return (
                isset($this->arSettings['Paths']['DirLogs'])
                && !empty($this->arSettings['Paths']['DirLogs'])
            )
            ? $this->parseSetting($this->arSettings['Paths']['DirLogs'])
            : $this->getLocalRoot() . '/logs'
        ;
    }

    /**
     * Возвращает абсолютный путь к системному лог-файлу
     *
     * @return string
     */
    public function getSystemLogFile ()
    {
        return (
                isset($this->arSettings['Debug']['SystemLogFile'])
                && !empty($this->arSettings['Debug']['SystemLogFile'])
            )
            ? $this->parseSetting($this->arSettings['Debug']['SystemLogFile'])
            : $this->getDirLogs() . '/sys_'.date('Ymd').'.log'
        ;
    }

    /**
     * Возвращает срок хранения системных файлов лога в днях
     *
     * @return int
     */
    public function getExpireLogFiles ()
    {
        return isset($this->arSettings['Debug']['ExpireSystemLogFiles'])
            ? $this->arSettings['Debug']['ExpireSystemLogFiles']
            : 14
        ;
    }

    /**
     * Возвращает срок хранения ежедневных файлов лога в днях
     *
     * @return int
     */
    public function getExpireDailyLogFiles ()
    {
        return isset($this->arSettings['Debug']['ExpireDailyLogFiles'])
            ? $this->arSettings['Debug']['ExpireDailyLogFiles']
            : 14
        ;
    }

    /**
     * Возвращает срок хранения ежемесячных файлов лога в месяцах
     *
     * @return int
     */
    public function getExpireMonthlyLogFiles ()
    {
        return isset($this->arSettings['Debug']['ExpireMonthlyLogFiles'])
            ? $this->arSettings['Debug']['ExpireMonthlyLogFiles']
            : 6
        ;
    }

    /**
     * Возвращает временнУю зону (Timezone)
     *
     * @return string
     */
    public function getTimezone ()
    {
        $timezone = '';
        if (isset($this->arSettings['Time']['Timezone']))
        {
            $timezone = $this->parseSetting($this->arSettings['Time']['Timezone']);
        }

        return (
                !empty($timezone)
                && in_array($timezone,Date::getTimezonesList())
            )
            ? $timezone
            : 'Europe/Moscow'
        ;
    }

    /**
     * Возвращает формат показа даты
     *
     * @return string
     */
    public function getSiteDateFormat ()
    {
        return (
                isset($this->arSettings['Time']['SiteDate'])
                && !empty($this->arSettings['Time']['SiteDate'])
            )
            ? $this->parseSetting($this->arSettings['Time']['SiteDate'])
            : 'd.m.Y'
        ;
    }

    /**
     * Возвращает формат показа времени
     *
     * @return string
     */
    public function getSiteTimeFormat ()
    {
        return (
                isset($this->arSettings['Time']['SiteTime'])
                && !empty($this->arSettings['Time']['SiteDate'])
            )
            ? $this->parseSetting($this->arSettings['Time']['SiteTime'])
            : 'H:i:s'
        ;
    }

    /**
     * Возвращает формат показа даты и веремени
     *
     * @return string
     */
    public function getSiteDateTimeFormat ()
    {
        return (
                isset($this->arSettings['Time']['SiteDateTime'])
                && !empty($this->arSettings['Time']['SiteDateTime'])
            )
            ? $this->parseSetting($this->arSettings['Time']['SiteDateTime'])
            : $this->getSiteDateFormat() . ' ' . $this->getSiteTimeFormat()
        ;
    }

    /**
     * Возвращает host для соединения с базой данных
     *
     * @return string
     */
    public function getDbHost ()
    {
        return (
                isset($this->arSettings['DataBase']['Host'])
                && !empty($this->arSettings['DataBase']['Host'])
            )
            ? $this->parseSetting($this->arSettings['DataBase']['Host'])
            : 'localhost'
        ;
    }

    /**
     * Возвращает имя базы данных
     *
     * @return string
     */
    public function getDbName ()
    {
        return (
                isset($this->arSettings['DataBase']['Base'])
                && !empty($this->arSettings['DataBase']['Base'])
            )
            ? $this->parseSetting($this->arSettings['DataBase']['Base'])
            : 'dobro'
        ;
    }

    /**
     * Возвращает имя пользователя базы данных
     *
     * @return string
     */
    public function getDbUser ()
    {
        return (
                isset($this->arSettings['DataBase']['User'])
                && !empty($this->arSettings['DataBase']['User'])
            )
            ? $this->parseSetting($this->arSettings['DataBase']['User'])
            : 'root'
        ;
    }

    /**
     * Возвращает пароль пользователя базы данных
     *
     * @return string
     */
    public function getDbPass ()
    {
        return isset($this->arSettings['DataBase']['Password'])
            ? $this->parseSetting($this->arSettings['DataBase']['Password'])
            : ''
        ;
    }

    /**
     * Возвращает используемый драйвер подключения к БД
     *
     * @return string
     */
    public function getDbDriver ()
    {
        return (
                isset($this->arSettings['DataBase']['DbDriver'])
                && !empty($this->arSettings['DataBase']['DbDriver'])
            )
            ? $this->parseSetting($this->arSettings['DataBase']['DbDriver'])
            : \Ms\Core\Entity\Db\Drivers\MySqliDriver::class
        ;
    }

    /**
     * Возвращает полный массив настроек
     *
     * @return array
     */
    public function getAllSettings ()
    {
        return $this->arSettings;
    }

    /**
     * Возвращает значения настроек, если они установлены, либо значение, переданное в default, либо null
     * Значения принимаются:<br>
     * в виде массива ['setting1level'],<br>
     * либо ['setting1level','setting2level']<br>
     * либо строки 'setting1level',<br>
     * либо 'setting1level.setting2level',<br>
     * либо 'setting1level,setting2level',<br>
     * либо 'setting1level:setting2level'
     *
     * @param mixed $settings
     * @param mixed $default  Значение по-умолчанию
     *
     * @return mixed|null
     */
    public function get ($settings, $default = null)
    {
        $value = null;
        if (!is_array($settings))
        {
            if (strpos($settings,'.')!==false)
            {
                $value = $this->getVal($settings,'.');
            }
            elseif (strpos($settings,',')!==false)
            {
                $value = $this->getVal($settings,',');
            }
            elseif (strpos($settings,':')!==false)
            {
                $value = $this->getVal($settings,':');
            }
            else
            {
                if (isset($this->arSettings[$settings]))
                {
                    $value = $this->arSettings[$settings];
                }
            }
        }
        else
        {
            if (count($settings)==1)
            {
                if (isset($this->arSettings[$settings[0]]))
                {
                    $value = $this->arSettings[$settings[0]];
                }
            }
            elseif (count($settings)>=2)
            {
                if (isset($this->arSettings[$settings[1]]))
                {
                    $value = $this->arSettings[$settings[1]];
                }
            }
        }

        if (is_null($value) && !is_null($default))
        {
            return $default;
        }

        return $value;
    }

    /**
     * Возвращает флаг использования кодировки UTF-8
     *
     * @return bool
     */
    public function isUseUtf8 ()
    {
        // return $this->useUtf8;
        return true;
    }

    /**
     * Возвращает значение настройки, разделяя название по разделителю
     *
     * @param string $settings  Название настройки с разделителем
     * @param string $del       Символ-разделитель для настройки
     *
     * @return mixed|null
     */
    protected function getVal ($settings, $del='.')
    {
        $value = null;

        list($first,$second) = explode($del,$settings);
        if (isset($this->arSettings[$first][$second]))
        {
            $value = $this->parseSetting($this->arSettings[$first][$second]);
        }

        return $value;
    }

    /**
     * Объединяет системные настройки с пользовательскими.
     * Пользовательские настройки заменяют системные, при совпадении названий
     *
     * @return $this
     */
    public function mergeLocalSettings ()
    {
        $localSettingsFilePath = $this->getLocalRoot() . '/.settings.php';
        if (!file_exists($localSettingsFilePath))
        {
            return $this;
        }
        $arLocalSettings = include ($localSettingsFilePath);
        if (!is_array($arLocalSettings) || empty($arLocalSettings))
        {
            return $this;
        }
        foreach ($arLocalSettings as $general => $ar_settings)
        {
            if (!is_array($ar_settings) || empty($ar_settings))
            {
                continue;
            }
            foreach ($ar_settings as $name => $value)
            {
                $this->arSettings[$general][$name] = $value;
            }
        }

        return $this;
    }

}
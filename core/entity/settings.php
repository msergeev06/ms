<?php
/**
 * Ms\Core\Entity\Settings
 * Объект настроек
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity;

use Ms\Core\Entity\Type\Date;

class Settings
{
	/**
	 * Массив настроек
	 * @var array
	 * @access protected
	 */
	protected $arSettings = null;

	/**
	 * Флаг, использование UTF-8 кодировки
	 * @var bool
	 * @access protected
	 */
	protected $useUtf = true;

	/**
	 * Создает объект настроек
	 *
	 * @param array $arr Массив настроек
	 * @access public
	 */
	public function __construct(array $arr)
	{
		if (!empty($arr))
		{
			foreach ($arr as $key=>$ar)
			{
				$newKey = strtolower($key);
				if (!empty($arr[$key]))
				{
					foreach ($arr[$key] as $key2=>$value)
					{
						$newKey2 = strtolower($key2);
						$this->arSettings[$newKey][$newKey2] = $value;
					}
				}
			}
		}
		if ($this->getCharset()!='UTF-8')
		{
			$this->useUtf = false;
		}
	}

	/**
	 * Возвращает значения настроек, если они установлены, либо значение, переданное в default, либо null
	 * Значения принимаются в виде массива ['setting1level'], либо ['setting1level','setting2level']
	 * либо строки 'setting1level', либо 'setting1level.setting2level',
	 * либо 'setting1level,setting2level', либо 'setting1level:setting2level'
	 *
	 * @param mixed $settings
	 * @param mixed $default  Значение по-умолчанию
	 *
	 * @return mixed|null
	 */
	public function get($settings, $default=null)
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
				if (isset($this->arSettings[strtolower($settings)]))
				{
					$value = $this->arSettings[strtolower($settings)];
				}
			}
		}
		else
		{
			//TODO: проверить работу без следующей строчки
//			$settings = ''.$settings;
			if (count($settings)==1)
			{
				if (isset($this->arSettings[strtolower($settings[0])]))
				{
					$value = $this->arSettings[strtolower($settings[0])];
				}
			}
			elseif (count($settings)>=2)
			{
				if (isset($this->arSettings[strtolower($settings[1])]))
				{
					$value = $this->arSettings[strtolower($settings[1])];
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
	 * Возвращает true, если используется кодировка UTF-8
	 *
	 * @access public
	 * @return bool
	 */
	public function useUtf ()
	{
		return $this->useUtf;
	}

	/**
	 * Возвращает host для соединения с базой данных
	 *
	 * @access public
	 * @return string
	 */
	public function getDbHost ()
	{
		if (isset($this->arSettings['database']['host']))
		{
			return $this->arSettings['database']['host'];
		}
		else
		{
			return 'localhost';
		}
	}

	/**
	 * Возвращает имя базы данных
	 *
	 * @access public
	 * @return mixed
	 */
	public function getDbName ()
	{
		return $this->arSettings['database']['base'];
	}

	/**
	 * Возвращает имя пользователя базы данных
	 *
	 * @access public
	 * @return mixed
	 */
	public function getDbUser ()
	{
		return $this->arSettings['database']['user'];
	}

	/**
	 * Возвращает пароль пользователя базы данных
	 *
	 * @access public
	 * @return mixed
	 */
	public function getDbPass ()
	{
		return $this->arSettings['database']['password'];
	}

	/**
	 * Возвращает используемый драйвер подключения к БД
	 *
	 * @return string
	 */
	public function getDbDriver()
	{
		if (isset($this->arSettings['database']['driver']))
		{
			return $this->arSettings['database']['driver'];
		}
		else
		{
			return 'mysqli';
		}
	}

	/**
	 * Возвращает протокол, по которому работает сайт (http или https)
	 *
	 * @access public
	 * @return string
	 */
	public function getSiteProtocol ()
	{
		if (isset($this->arSettings['site']['protocol']))
			return $this->arSettings['site']['protocol'];
		else
			return 'http';
	}

	/**
	 * Возвращает абсолютный путь к основной папке сайта
	 *
	 * @access public
	 * @return string
	 */
	public function getSiteUrl ()
	{
		if (isset($this->arSettings['site']['siteurl']))
			return $this->arSettings['site']['siteurl'];
		else
			return $_SERVER['HTTP_HOST'];
	}

	/**
	 * Возвращает абсолютный путь к сборке
	 *
	 * @access public
	 * @return string
	 */
	public function getMsRoot ()
	{
		$docRoot = $this->getDocumentRoot();
		if (isset($this->arSettings['site']['msroot']))
			return $this->arSettings['site']['msroot'];
		else
			return $docRoot.'/ms';
	}

	/**
	 * Возвращает абсолютный путь к папке upload
	 *
	 * @access public
	 * @return string
	 */
	public function getUploadDir ()
	{
		$docRoot = $this->getDocumentRoot();
		if (isset($this->arSettings['site']['uploaddir']))
			return $this->arSettings['site']['uploaddir'];
		else
			return $docRoot.'/upload';
	}

	/**
	 * Возвращает абсолютный путь к директории кеша
	 *
	 * @access public
	 * @return string
	 */
	public function getCacheDir ()
	{
		$docRoot = $this->getDocumentRoot();
		if (isset($this->arSettings['files']['cachedir']))
			return $this->arSettings['files']['cachedir'];
		else
			return $docRoot.'/cached';
	}

	/**
	 * Возвращает абсолютный путь к директории ядра
	 *
	 * @access public
	 * @return string
	 */
	public function getCoreRoot ()
	{
		if (isset($this->arSettings['site']['coreroot']))
			return $this->arSettings['site']['coreroot'];
		else
			return $this->getMsRoot().'/core';
	}

	/**
	 * Возвращает абсолютный путь к папке шаблонов
	 *
	 * @access public
	 * @return string
	 */
	public function getTemplatesRoot ()
	{
		if (isset($this->arSettings['site']['templatesroot']))
			return $this->arSettings['site']['templatesroot'];
		else
			return $this->getMsRoot().'/templates';
	}

	/**
	 * Возвращает абсолютный путь к папке модулей
	 *
	 * @access public
	 * @return string
	 */
	public function getModulesRoot ()
	{
		if (isset($this->arSettings['site']['modulesroot']))
			return $this->arSettings['site']['modulesroot'];
		else
			return $this->getMsRoot().'/modules';
	}

	/**
	 * Возвращает абсолютный путь к папке изображений модулей
	 *
	 * @access public
	 * @return string
	 */
	public function getModulesImages()
	{
		return $this->getMsRoot().'/images';
	}

	/**
	 * Возвращает абсолютный путь к папке компонентов
	 *
	 * @access public
	 * @return string
	 */
	public function getComponentsRoot ()
	{
		if (isset($this->arSettings['site']['componentsroot']))
			return $this->arSettings['site']['componentsroot'];
		else
			return $this->getMsRoot().'/components';
	}

	/**
	 * Возвращает текущий язык сайта
	 *
	 * @access public
	 * @return string
	 */
	public function getSiteLang ()
	{
		if (isset($this->arSettings['site']['lang']))
			return $this->arSettings['site']['lang'];
		else
			return 'ru';
	}

	/**
	 * Возвращает текущую кодировку сайта
	 *
	 * @access public
	 * @return string
	 */
	public function getCharset ()
	{
		if (isset($this->arSettings['site']['charset']))
			return $this->arSettings['site']['charset'];
		else
			return 'UTF-8';
	}

	/**
	 * Возвращает TRUE, если установлена кодировка UTF-8
	 *
	 * @return bool
	 */
	public function isCharsetUtf8()
	{
		return (strtolower($this->getCharset())=='utf-8');
	}

	/**
	 * Возвращает флаг отключения Iconv
	 *
	 * @access public
	 * @return bool
	 */
	public function isDisableIconv ()
	{
		if (isset($this->arSettings['site']['disableiconv']))
			return $this->arSettings['site']['disableiconv'];
		else
			return false;
	}

	/**
	 * Возвращает шаблон сайта по-умолчанию
	 *
	 * @access public
	 * @return string
	 */
	public function getTemplate ()
	{
		if (isset($this->arSettings['site']['template']))
			return $this->arSettings['site']['template'];
		else
			return '.default';
	}

	/**
	 * Возвращает настройки CHMOD для файла
	 *
	 * @access public
	 * @return int
	 */
	public function getChmodFile ()
	{
		if (isset($this->arSettings['files']['chmodfile']))
			return $this->arSettings['files']['chmodfile'];
		else
			return 0666;
	}

	/**
	 * Возвращает настройки CHMOD для директории
	 *
	 * @access public
	 * @return int
	 */
	public function getChmodDir ()
	{
		if (isset($this->arSettings['files']['chmoddir']))
			return $this->arSettings['files']['chmoddir'];
		else
			return 0777;
	}

	/**
	 * Возвращает абсолютный путь к папке резервных копий базы данных
	 *
	 * @access public
	 * @return string
	 */
	public function getDirBackupDb ()
	{
		if (isset($this->arSettings['backup']['dirbackupdb']))
			return $this->arSettings['backup']['dirbackupdb'];
		else
			return $this->getMsRoot().'/backup_db';
	}

	/**
	 * Возвращает абсолютный путь к папке резервных копий
	 *
	 * @access public
	 * @return string
	 */
	public function getDirBackup ()
	{
		if (isset($this->arSettings['backup']['dirbackup']))
			return $this->arSettings['backup']['dirbackup'];
		else
			return $this->getMsRoot().'/backup';
	}

	/**
	 * Вовзращает время хранения резервных файлов в днях
	 *
	 * @access public
	 * @return int
	 */
	public function getExpireBackupFiles ()
	{
		if (isset($this->arSettings['backup']['expirebackupfiles']))
			return $this->arSettings['backup']['expirebackupfiles'];
		else
			return 5;
	}

	/**
	 * Возвращает флаг режима отладки
	 *
	 * @access public
	 * @return bool
	 */
	public function isDebugMode ()
	{
		if (isset($this->arSettings['debug']['debugmode'])
			&& $this->arSettings['debug']['debugmode']===true
		)
			return true;
		else
			return false;
	}

	/**
	 * Возвращает абсолютный путь к каталогу логов
	 *
	 * @access public
	 * @return string
	 */
	public function getDirLogs ()
	{
		if (isset($this->arSettings['debug']['dirlogs']))
			return $this->arSettings['debug']['dirlogs'];
		else
			return $this->getDocumentRoot().'/logs';
	}

	/**
	 * Возвращает абсолютный путь к системному лог-файлу
	 *
	 * @access public
	 * @return string
	 */
	public function getSystemLogFile ()
	{
		if (isset($this->arSettings['debug']['systemlogfile']))
			return $this->arSettings['debug']['systemlogfile'];
		else
			return null;
	}

	/**
	 * Возвращает срок хранения системных файлов лога в днях
	 *
	 * @access public
	 * @return int
	 */
	public function getExpireLogFiles ()
	{
		if (isset($this->arSettings['debug']['expiresystemlogfiles']))
			return $this->arSettings['debug']['expiresystemlogfiles'];
		else
			return 14;
	}

	/**
	 * Возвращает срок хранения ежедневных файлов лога в днях
	 *
	 * @access public
	 * @return int
	 */
	public function getExpireDailyLogFiles ()
	{
		if (isset($this->arSettings['debug']['expiredailylogfiles']))
			return $this->arSettings['debug']['expiredailylogfiles'];
		else
			return 14;
	}

	/**
	 * Возвращает срок хранения ежедневных файлов лога в месяцах
	 *
	 * @access public
	 * @return int
	 */
	public function getExpireMonthlyLogFiles ()
	{
		if (isset($this->arSettings['debug']['expiremonthlylogfiles']))
			return $this->arSettings['debug']['expiremonthlylogfiles'];
		else
			return 6;
	}

	/**
	 * Возвращает временнУю зону (Timezone)
	 *
	 * @return string
	 */
	public function getTimezone ()
	{
		if (isset($this->arSettings['time']['timezone'])
			&& in_array($this->arSettings['time']['timezone'],Date::getTimezonesList()))
		{
			return $this->arSettings['time']['timezone'];
		}
		else
		{
			return 'Europe/Moscow';
		}
	}

	/**
	 * Возвращает формат показа даты
	 *
	 * @return string
	 */
	public function getSiteDate ()
	{
		if (isset($this->arSettings['time']['sitedate']))
		{
			return $this->arSettings['time']['sitedate'];
		}
		else
		{
			return 'd.m.Y';
		}
	}

	/**
	 * Возвращает формат показа даты и веремени
	 *
	 * @return string
	 */
	public function getSiteDateTime()
	{
		if (isset($this->arSettings['time']['sitedatetime']))
		{
			return $this->arSettings['time']['sitedatetime'];
		}
		else
		{
			return 'd.m.Y H:i:s';
		}
	}

	/**
	 * Возвращает формат показа времени
	 *
	 * @return string
	 */
	public function getSiteTime ()
	{
		if (isset($this->arSettings['time']['sitetime']))
		{
			return $this->arSettings['time']['sitetime'];
		}
		else
		{
			return 'H:i:s';
		}
	}

	/**
	 * Возвращает список автоматически подключаемых модулей
	 *
	 * @access public
	 * @return bool|array
	 */
	public function getAutoLoadModules ()
	{
		if (isset($this->arSettings['autoloadmodules']) && !empty($this->arSettings['autoloadmodules']))
		{
			return $this->arSettings['autoloadmodules'];
		}

		return false;
	}

	public function getAllSettings ()
	{
		return $this->arSettings;
	}

	/**
	 * Возвращает DocumentRoot
	 *
	 * @access protected
	 * @return string
	 */
	protected function getDocumentRoot ()
	{
		static $documentRoot = null;
		if (!is_null($documentRoot))
			return $documentRoot;

		$app = Application::getInstance();
		$documentRoot = $app->getDocumentRoot();

		return $documentRoot;
	}

	/**
	 * @param        $settings
	 * @param string $del
	 *
	 * @return null
	 */
	private function getVal ($settings,$del='.')
	{
		$value = null;

		list($first,$second) = explode($del,strtolower($settings));
		if (isset($this->arSettings[$first][$second]))
		{
			$value = $this->arSettings[$first][$second];
		}

		return $value;
	}

}
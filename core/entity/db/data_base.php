<?php
/**
 * MSergeev\Core\Entity\Db\DataBase
 * Осуществляет подключение к базе данных и посылает запросы к базе
 *
 * @package MSergeev\Core
 * @subpackage Entity\Db
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.1.0
 */

namespace MSergeev\Core\Entity\Db;

use MSergeev\Core\Entity\Db\Query;
use MSergeev\Core\Exception;
use MSergeev\Core\Entity\Application;

class DataBase {

	/**
	 * Hostname Базы данных. Используется для подключения к DB
	 * @var string
	 */
	protected $host;

	/**
	 * Имя базы данных. Используется для подключения к DB
	 * @var string
	 */
	protected $base;

	/**
	 * Пользователь базы данных. Используется для подключения к DB
	 * @var string
	 */
	protected $user;

	/**
	 * Пароль пользователя базы данных. Используется для подключения к DB
	 * @var string
	 */
	protected $pass;

	/**
	 * Ссылка на подключение к базе данных, либо false
	 * @var resource|bool
	 */
	protected $db_conn;

	/**
	 * Отладочная информация по всем запросам сессии
	 * @var array
	 */
	protected $arLog = array();

	/**
	 * Количество запросов к базе данных
	 * @var int
	 */
	protected $db_queries=0;

	/**
	 * Время начала запроса
	 * @var int
	 */
	protected $db_query_start=0;

	/**
	 * Время окончания запроса
	 * @var int
	 */
	protected $db_query_stop=0;

	/**
	 * Время выполнения последнего запроса
	 * @var int
	 */
	protected $db_last_query_time=0;

	/**
	 * Время выполнения всех запросов
	 * @var int
	 */
	protected $db_all_query_time=0;

	/**
	 * Осуществляет подключение к базе данных и передает начальные параметры подключения
	 *
	 * @api
	 * @since 0.1.0
	 */
	public function __construct ()
	{

		$this->host = Application::getInstance()->getSettings()->getDbHost();
		$this->base = Application::getInstance()->getSettings()->getDbName();
		$this->user = Application::getInstance()->getSettings()->getDbUser();
		$this->pass = Application::getInstance()->getSettings()->getDbPass();

		try
		{
			$this->db_conn = @mysql_connect($this->host, $this->user, $this->pass);
			if (!$this->db_conn)
			{
				throw new Exception\Db\ConnectionException('Could not connect',mysql_error());
			}
		}
		catch (Exception\Db\ConnectionException $e)
		{
			die($e->showException());
		}
		try
		{
			$select_db = @mysql_select_db($this->base, $this->db_conn);
			if(!$select_db)
			{
				if (!$this->restoreDB())
				{
					throw new Exception\Db\ConnectionException('Not isset DB '.$this->base,mysql_error());
				}
			}
		}
		catch (Exception\Db\ConnectionException $e)
		{
			die($e->showException());
		}

		if (Application::getInstance()->getSettings()->useUtf())
		{
			mysql_set_charset('utf8',$this->db_conn);
		}
	}

	/**
	 * Восстанавливает базу данных из последнего существующего backup
	 *
	 * @return bool
	 * @since 0.1.0
	 */
	public function restoreDB ()
	{
		$documentRoot = Application::getInstance()->getDocumentRoot();
		$dirBackupDb = Application::getInstance()->getSettings()->getDirBackupDb();

		if (!$documentRoot || !$dirBackupDb || file_exists($documentRoot.'/backup'))
		{
			return false;
		}

		$f1 = fopen($documentRoot.'/backup','w');
		fwrite($f1,date('Y-m-d H:i:s'));
		fclose($f1);

		$comm = $this->getCreateDbCommand($this->base);
		exec($comm);
		$fileTime = null;
		$filePath = null;
		$dir=$dirBackupDb;
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if (!is_dir($dir.$file) && $file != "." && $file != ".." && $file != ".htaccess")
					{
						if (strstr($file,$this->base)!==false)
						{
							if (is_null($fileTime))
							{
								$fileTime = filemtime($dir.$file);
								$filePath = $dir.$file;
							}
							elseif (filemtime($dir.$file) > $fileTime)
							{
								$fileTime = filemtime($dir.$file);
								$filePath = $dir.$file;
							}
						}
					}
				}
				closedir($dh);
			}
		}
		if (!is_null($filePath))
		{
			$comm = $this->getBackupCommand($filePath);
			exec($comm);
			unlink($documentRoot.'/backup');
			return true;
		}

		unlink($documentRoot.'/backup');
		return false;
	}

	/**
	 * Осуществляет запрос к базе данных, используя данные объекта Query
	 *
	 * @api
	 *
	 * @param Query\QueryBase $obQuery Объект, содержащий SQL запрос
	 *
	 * @return DBResult Результат MYSQL запроса
	 * @since 0.1.0
	 */
	public function query (Query\QueryBase $obQuery)
	{
		$sql = $obQuery->getSql();
		$queryHash = $this->getQueryHash($sql);
		$this->setQueryStart($queryHash);
		$db_res = mysql_query($sql, $this->db_conn);
		$this->setQueryStop($queryHash);

		$res = new DBResult($db_res,$obQuery);
		$res->setAffectedRows(mysql_affected_rows($this->db_conn));
		if ($obQuery instanceof Query\QueryInsert)
		{
			$res->setInsertId(mysql_insert_id($this->db_conn));
		}
		if (!$res->getResult())
		{
			$res->setResultErrorNumber(mysql_errno($this->db_conn));
			$res->setResultErrorText(mysql_error($this->db_conn));
		}

		return $res;
	}

	/**
	 * Выполняет произвольный SQL запрос
	 *
	 * @param $sql
	 *
	 * @return resource
	 * @since 0.1.0
	 */
	public function querySQL ($sql)
	{
		mysql_query($sql,$this->db_conn);
		return mysql_affected_rows($this->db_conn);
	}

	/**
	 * Возвращает время выполнения последнего SQL запроса
	 *
	 * @return float
	 * @since 0.1.0
	 */
	public function getLastQueryTime ()
	{
		return floatval($this->db_last_query_time);
	}

	/**
	 * Возвращает общее время всех SQL запросов
	 *
	 * @return float
	 * @since 0.1.0
	 */
	public function getAllQueryTime ()
	{
		return floatval($this->db_all_query_time);
	}

	/**
	 * Возвращает общее количество выполненных SQL запросов
	 *
	 * @return int
	 * @since 0.1.0
	 */
	public function getCountQuery ()
	{
		return intval(count($this->arLog));
	}

	/**
	 * Возвращает команду shell для создания базы данных
	 *
	 * @param string $dbName
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getCreateDbCommand ($dbName)
	{
		//mysqladmin -uUSER -pPASS create msergeev
		$comm = 'sudo mysqladmin -u'
			.$this->user.' -p'
			.$this->pass.' create '
			.$dbName;

		return $comm;
	}

	/**
	 * Возвращает команду shell для восстановления базы из бекапа
	 *
	 * @param string $filePath
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getBackupCommand ($filePath)
	{
		//gunzip < /path/to/filename.sql.gz | mysql -uroot -prootpsw msergeev
		$comm = 'sudo gunzip < '
			.$filePath.' | mysql -u'
			.$this->user.' -p'
			.$this->pass.' '
			.$this->base;

		return $comm;
	}

	/**
	 * Возвращает команду shell для создания бекапа для базы данныъ
	 *
	 * @param string $path      Путь к бекапу
	 * @param string $postfix   Постфикс для файла
	 * @param string $module    Имя модуля
	 * @param array  $arTables  Массив со списком таблиц
	 * @param bool   $useGz     Флаг использования сжития gz
	 * @param bool   $pastDate  Флаг добавления даты в название файла
	 * @param bool   $noData    Флаг необходимости сохранения только структуры таблиц, без данных
	 * @param string $dbName    Имя базы данных, если не указана, будет взята текущая
	 * @param string $dbUser    Пользователь базы данных, если не указан, будет взят текущий
	 * @param string $dbPass    Пароль пользователя базы данных, если не указан, будет взят текущий
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getDumpCommand ($path,$postfix=null,$module=null,$arTables=array(),$useGz=true,$pastDate=true,$noData=false,$dbName=null,$dbUser=null,$dbPass=null)
	{
		//mysqldump -u USER -pPASSWORD DATABASE | gzip > /path/to/outputfile.sql.gz
		//mysqladmin -uUSER -pPASS create msergeev
		//mysqladmin -uroot -prootpsw create msergeev
		//gunzip < /var/www/kuzmahome/backup_db/dump_msergeev_hourly.20170703.160004.sql.gz | mysql -uroot -prootpsw msergeev
		$path .= '/';

		$comm = 'mysqldump ';
		//$comm .= '-Q -c -e ';
		if ($noData === true)
		{
			$comm .= '--no-data ';
		}
		if (is_null($dbName))
		{
			$dbName = $this->base;
		}
		if (is_null($dbUser))
		{
			$dbUser = $this->user;
		}
		if (is_null($dbPass))
		{
			$dbPass = $this->pass;
		}
		$comm .= "-u".$dbUser." -p".$dbPass." ".$dbName." ";
		if (!empty($arTables))
		{
			foreach ($arTables as $tableName)
			{
				$comm .= $tableName.' ';
			}
		}
		if ($useGz === true)
		{
			$comm .= '| gzip ';
		}
		$comm .= '> ';
		/*		if ($pastDate === true)
				{
					$comm .= '`date +';
				}*/
		$comm .= $path.'dump_'.$dbName;
		if (!is_null($module))
		{
			$comm .= '_'.$module;
		}
		if (!empty($arTables))
		{
			$comm .= '_tables';
		}
		if (!is_null($postfix) && $postfix !== false)
		{
			$comm .= '_'.$postfix;
		}
		if ($pastDate === true)
		{
			//$comm .= '.%Y%m%d.%H%M%S';
			$comm .= date('.Ymd.His');
		}
		$comm .= '.sql';
		if ($useGz === true)
		{
			$comm .= '.gz';
		}

		return $comm;
	}

	/**
	 * Возвращает созданный hash SQL запроса
	 *
	 * @param string $sql SQL запрос
	 *
	 * @return string
	 * @since 0.1.0
	 */
	private function getQueryHash ($sql)
	{
		$hash = md5($sql.time().microtime(true));
		$this->arLog[$hash]['SQL'] = $sql;

		return $hash;
	}

	/**
	 * Устанавливает начальное время SQL запроса
	 *
	 * @param string $hash HASH запроса
	 * @since 0.1.0
	 */
	private function setQueryStart ($hash)
	{
		$this->arLog[$hash]['QUERY_START'] = microtime (true);
		$this->arLog[$hash]['QUERY_START_TIME'] = time();
	}

	/**
	 * Устанавливает конечное время SQL запроса, вычисляет время выполнения последнего запроса и время выполнения всех запросов
	 *
	 * @param string $hash HASH запроса
	 * @since 0.1.0
	 */
	private function setQueryStop ($hash)
	{
		$this->arLog[$hash]['QUERY_STOP'] = microtime(true);
		$this->arLog[$hash]['QUERY_STOP_TIME'] = time();

		$this->arLog[$hash]['QUERY_TIME'] = $this->arLog[$hash]['QUERY_STOP'] - $this->arLog[$hash]['QUERY_START'];
		$this->db_last_query_time = $this->arLog[$hash]['QUERY_TIME'];

		$this->db_all_query_time += $this->db_last_query_time;
	}
}
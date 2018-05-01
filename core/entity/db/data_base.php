<?php
/**
 * Ms\Core\Entity\Db\DataBase
 * Осуществляет подключение к базе данных и посылает запросы к базе
 *
 * @package MSergeev\Core
 * @subpackage Entity\Db
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db;

use Ms\Core\Entity\Db\Query;
use Ms\Core\Exception;
use Ms\Core\Entity\Application;

class DataBase {

	/**
	 * @var string Hostname Базы данных. Используется для подключения к DB
	 */
	protected $host;

	/**
	 * @var string Имя базы данных. Используется для подключения к DB
	 */
	protected $base;

	/**
	 * @var string Пользователь базы данных. Используется для подключения к DB
	 */
	protected $user;

	/**
	 * @var string Пароль пользователя базы данных. Используется для подключения к DB
	 */
	protected $pass;

	/**
	 * @var string Используемый драйвер для подключения к БД
	 * @since 0.2.0
	 */
	protected $driver;

	/**
	 * @var resource|\mysqli|bool Ссылка на подключение к базе данных, либо false
	 */
	protected $db_conn;

	/**
	 * @var array Отладочная информация по всем запросам сессии
	 */
	protected $arLog = array();

	/**
	 * @var int Количество запросов к базе данных
	 */
	protected $db_queries=0;

	/**
	 * @var int Время начала последнего запроса
	 */
	protected $db_query_start=0;

	/**
	 * @var int Время окончания последнего запроса
	 */
	protected $db_query_stop=0;

	/**
	 * @var int Время выполнения последнего запроса
	 */
	protected $db_last_query_time=0;

	/**
	 * @var int Время выполнения всех запросов
	 */
	protected $db_all_query_time=0;

	/**
	 * Осуществляет подключение к базе данных и передает начальные параметры подключения
	 *
	 * @api
	 */
	public function __construct ()
	{

		$this->host = Application::getInstance()->getSettings()->getDbHost();
		$this->base = Application::getInstance()->getSettings()->getDbName();
		$this->user = Application::getInstance()->getSettings()->getDbUser();
		$this->pass = Application::getInstance()->getSettings()->getDbPass();
		$this->driver = Application::getInstance()->getSettings()->getDbDriver();

		//Подключаемся к базе данных, используя требуемый драйвер
		$this->mysqlConnect();

		//Устанавливаем необходимые параметры подключения
		$this->setConnectParams();
	}

	/**
	 * Восстанавливает базу данных из последнего существующего backup
	 *
	 * @return bool
	 */
	public function restoreDB ()
	{
		$documentRoot = Application::getInstance()->getDocumentRoot();
		$dirBackupDb = Application::getInstance()->getSettings()->getDirBackupDb();

		if (!$documentRoot || !$dirBackupDb || file_exists($documentRoot.'/backup'))
		{
			return false;
		}

		//Создаем файл backup в корне, чтобы система понимала, что идет процесс восстановления БД
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
			//после завершения восстановления, удаляем файл backup
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
	 */
	public function query (Query\QueryBase $obQuery)
	{
		$sql = $obQuery->getSql();
		$queryHash = $this->getQueryHash($sql);
		$this->setQueryStart($queryHash);
		//$db_res = mysql_query($sql, $this->db_conn);
		$db_res = $this->getConnectionQuery($sql);
		$this->setQueryStop($queryHash);

		$res = new DBResult($db_res,$obQuery);
		//$res->setAffectedRows(mysql_affected_rows($this->db_conn));
		$res->setAffectedRows($this->getConnectionAffectedRows());
		if ($obQuery instanceof Query\QueryInsert)
		{
			//$res->setInsertId(mysql_insert_id($this->db_conn));
			$res->setInsertId($this->getInsertId());
		}
		if (!$res->getResult())
		{
			//$res->setResultErrorNumber(mysql_errno($this->db_conn));
			$res->setResultErrorNumber($this->getConnectionErrorNo());
			//$res->setResultErrorText(mysql_error($this->db_conn));
			$res->setResultErrorText($this->getConnectionError());
		}

		return $res;
	}

	/**
	 * Выполняет произвольный SQL запрос
	 *
	 * @param string $sql
	 *
	 * @return resource
	 */
	public function querySQL ($sql)
	{
		//mysql_query($sql,$this->db_conn);
		$this->getConnectionQuery($sql);
		//return mysql_affected_rows($this->db_conn);
		return $this->getConnectionAffectedRows();
	}

	/**
	 * Возвращает время выполнения последнего SQL запроса
	 *
	 * @return float
	 */
	public function getLastQueryTime ()
	{
		return floatval($this->db_last_query_time);
	}

	/**
	 * Возвращает общее время всех SQL запросов
	 *
	 * @return float
	 */
	public function getAllQueryTime ()
	{
		return floatval($this->db_all_query_time);
	}

	/**
	 * Возвращает общее количество выполненных SQL запросов
	 *
	 * @return int
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
	 * Возвращает массив всех SQL запросов текущей сессии
	 *
	 * @return array
	 * @since 0.2.0
	 */
	public function getSqlLogs()
	{
		return $this->arLog;
	}

	/**
	 * Подключается к базе данных, используя требуемый драйвер
	 *
	 * @since 0.2.0
	 */
	private function mysqlConnect()
	{
		if ($this->driver == 'mysql')
		{
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
		}
		else //если используется mysqli
		{
			try
			{
				$this->db_conn = @new \mysqli($this->host,$this->user,$this->pass,$this->base);
				if (mysqli_connect_error())
				{
					//Если нет БД
					if ((int)$this->db_conn->connect_errno == 1049)
					{
						//Если не получилось восстановить БД
						if (!$this->restoreDB())
						{
							throw new Exception\Db\ConnectionException('Not isset DB '.$this->base,mysqli_connect_error());
						}
					}
					//Если другая ошибка - выводим ошибку
					else
					{
						throw new Exception\Db\ConnectionException('Could not connect ['.$this->db_conn->connect_errno.']',mysqli_connect_error());
					}
				}
			}
			catch (Exception\Db\ConnectionException $e)
			{
				die($e->showException());
			}
		}
	}

	/**
	 * Устанавливает необходимые параметры подключения, используя требуемый драйвер
	 *
	 * @since 0.2.0
	 */
	private function setConnectParams ()
	{
		if ($this->driver == 'mysql')
		{
			if (Application::getInstance()->getSettings()->useUtf())
			{
				mysql_set_charset('utf8',$this->db_conn);
			}
		}
		else //mysqli
		{
			if (Application::getInstance()->getSettings()->useUtf())
			{
				try
				{
					if (!@$this->db_conn->set_charset('utf8'))
					{
						throw new Exception\Db\DbException('Error set charset',$this->db_conn->error);
					}
				}
				catch (Exception\Db\DbException $e)
				{
					die($e->showException());
				}
			}
		}
	}

	/**
	 * Осуществляет запрос к БД, через требуемый драйвер
	 *
	 * @param string $sql Текст SQL запроса
	 *
	 * @return resource|\mysqli_result
	 * @since 0.2.0
	 */
	private function getConnectionQuery($sql)
	{
		if ($this->driver == 'mysql')
		{
			return mysql_query($sql, $this->db_conn);
		}
		else //mysqli
		{
			return $this->db_conn->query($sql);
		}
	}

	/**
	 * Возвращает затронутые запросом ряды, используя требуемый драйвер
	 *
	 * @return int
	 * @since 0.2.0
	 */
	private function getConnectionAffectedRows ()
	{
		if ($this->driver == 'mysql')
		{
			return mysql_affected_rows($this->db_conn);
		}
		else //mysqli
		{
			return $this->db_conn->affected_rows;
		}
	}

	/**
	 * Возвращает ID добавленной записи, используя требуемый драйвер
	 *
	 * @return int
	 * @since 0.2.0
	 */
	private function getInsertId ()
	{
		if ($this->driver == 'mysql')
		{
			return mysql_insert_id($this->db_conn);
		}
		else //mysqli
		{
			return $this->db_conn->insert_id;
		}
	}

	/**
	 * Возвращает номер произошедшей в запросе ошибки, используя требуемый драйвер
	 *
	 * @return int
	 * @since 0.2.0
	 */
	private function getConnectionErrorNo ()
	{
		if ($this->driver == 'mysql')
		{
			return mysql_errno($this->db_conn);
		}
		else //mysqli
		{
			return $this->db_conn->errno;
		}
	}

	/**
	 * Возвращает текст произошедшей в запросе ошибки, используя требуемый драйвер
	 *
	 * @return string
	 */
	private function getConnectionError()
	{
		if ($this->driver == 'mysql')
		{
			return mysql_error($this->db_conn);
		}
		else //mysqli
		{
			return $this->db_conn->error;
		}
	}

	/**
	 * Возвращает число столбцов, затронутых последним запросом, для требуемого драйвера
	 *
	 * @param resource $resource
	 *
	 * @return int
	 * @since 0.2.0
	 */
	public function getConnectionNumFields ($resource)
	{
		if ($this->driver == 'mysql')
		{
			return mysql_num_fields($resource);
		}
		else
		{
			return $this->db_conn->field_count;
		}
	}

	/**
	 * Получает число рядов в результирующей выборке, для требуемого драйвера
	 *
	 * @param resource|\mysqli_result $resource
	 *
	 * @return int
	 * @since 0.2.0
	 */
	public function getConnectionNumRows ($resource)
	{
		if ($this->driver == 'mysql')
		{
			return mysql_num_rows($resource);
		}
		else
		{
			return $resource->num_rows;
		}
	}

	/**
	 * Извлекает результирующий ряд в виде ассоциативного массива, для требуемого драйвера
	 *
	 * @param resource|\mysqli_result $resource
	 *
	 * @return array
	 * @since 0.2.0
	 */
	public function getConnectionFetchArray ($resource)
	{
		if ($this->driver == 'mysql')
		{
			return mysql_fetch_assoc($resource);
		}
		else
		{
			return $resource->fetch_array();
		}
	}

	/**
	 * Возвращает экранированную строку, используя требуемый драйвер
	 *
	 * @param null|string $string Исходная строка
	 *
	 * @return string
	 * @throws Exception\Db\DbException
	 * @since 0.2.0
	 */
	public function getConnectionRealEscapeString ($string)
	{
		if (is_null($string) || $string == '')
		{
			return $string;
		}
		if ($this->driver == 'mysql')
		{
			if (!$res = mysql_real_escape_string($string,$this->db_conn))
			{
				throw new Exception\Db\DbException('Error escape string',mysql_error());
			}
			return $res;
		}
		else //mysqli
		{
			try {
				if (!$res = $this->db_conn->real_escape_string($string))
				{
					throw new Exception\Db\DbException('Error escape string',$this->db_conn->error);
				}
				return $res;
			}
			catch (Exception\Db\DbException $e)
			{
				die($e->showException());
			}
		}
	}

	/**
	 * Возвращает созданный hash SQL запроса
	 *
	 * @param string $sql SQL запрос
	 *
	 * @return string
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
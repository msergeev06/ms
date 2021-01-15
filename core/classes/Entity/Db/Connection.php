<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db;

use Ms\Core\Entity\Db\Query\QueryBase;
use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\System\Application;
use Ms\Core\Exceptions\Db\ConnectionException;
use Ms\Core\Exceptions\Db\DbException;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Interfaces\ISqlDriver;

/**
 * Класс Ms\Core\Entity\Db\Connection
 * Подключение к базе данных
 */
class Connection
{
    /** @var string Имя базы данных. Используется для подключения к БД */
    protected $base;
    /** @var string Имя подключения */
    protected $connectionName = null;
    /** @var ISqlDriver Используемый драйвер для подключения к БД */
    protected $driver;
    /** @var string Хост БД. Используется для подключения к БД */
    protected $host;
    /** @var string Пароль пользователя БД. Используется для подключения к БД */
    protected $pass;
    /** @var QueryStatistics Статистика по всем запросам данного подключения */
    protected $queryStatistics = null;
    /** @var string Пользователь БД. Используется для подключения к БД */
    protected $user;

    /**
     * Конструктор класса Connection
     *
     * @param string $connectionName Имя подключения. По-умолчанию 'default'
     */
    public function __construct (string $connectionName = 'default')
    {
        if (empty($connectionName))
        {
            $connectionName = 'default';
        }
        else
        {
            $connectionName = strtolower($connectionName);
        }
        $this->connectionName = $connectionName;
        if ($this->isStatisticsUsage())
        {
            $this->queryStatistics = new QueryStatistics();
        }
    }

    /**
     * Деструктор класса Connection
     * Закрывает соединение с БД, если оно открыто и удаляет объект драйвера
     */
    public function __destruct ()
    {
        if (!is_null($this->getConnection()))
        {
            $this->close();
        }
        unset($this->driver);
    }

    /**
     * Закрывает открытое соединение с БД
     *
     * @return bool
     */
    public function close ()
    {
        return $this->driver->close();
    }

    /**
     * Подтверждает транзакцию
     *
     * @return mixed
     */
    public function commitTransaction ()
    {
        return $this->driver->commitTransaction();
    }

    /**
     * Осуществляет подключение к БД, используя заданный драйвер
     *
     * @param string|null $host   Хост подключения к БД
     * @param string|null $base   Имя базы данных
     * @param string|null $user   Пользователь БД
     * @param string|null $pass   Пароль пользователя БД
     * @param string|null $driver Имя класса драйвера подключения к БД
     *
     * @return $this
     * @throws ConnectionException
     * @throws DbException
     */
    public function connect (
        string $host = null,
        string $base = null,
        string $user = null,
        string $pass = null,
        string $driver = null
    ) {
        $settings = Application::getInstance()->getSettings();
        //<editor-fold desc=">>> Проверка и обработка параметров подключения">
        if (empty($host))
        {
            $this->host = $settings->getDbHost();
        }
        else
        {
            $this->host = $host;
        }
        if (empty($base))
        {
            $this->base = $settings->getDbName();
        }
        else
        {
            $this->base = $base;
        }
        if (empty($user))
        {
            $this->user = $settings->getDbUser();
        }
        else
        {
            $this->user = $user;
        }
        if (empty($pass))
        {
            $this->pass = $settings->getDbPass();
        }
        else
        {
            $this->pass = $pass;
        }
        if (empty($driver))
        {
            $this->driver = $settings->getDbDriver();
        }
        else
        {
            $this->driver = $driver;
        }
        //</editor-fold>
        try
        {
            $this->driver = new $this->driver();
            if (!($this->driver instanceof ISqlDriver))
            {
                throw new ConnectionException('Driver is not instance of ISqlDriver');
            }
        }
        catch (ConnectionException $e)
        {
            throw $e;
        }
        catch (\Exception $e)
        {
            throw new ConnectionException('Wrong driver use ', $e->getMessage());
        }

        $this->conn();
        $this->setConnectParams();

        return $this;
    }

    /**
     * Извлекает результирующий ряд последнего запроса в виде ассоциативного массива
     *
     * @return array
     */
    public function fetchArray ()
    {
        return $this->driver->fetchArray();
    }

    /**
     * Возвращает затронутые последним запросом ряды
     *
     * @return int
     */
    public function getAffectedRows ()
    {
        return $this->driver->getAffectedRows();
    }

    /**
     * Возвращает ресурс подключения к БД
     *
     * @return mixed|null
     */
    public function getConnection ()
    {
        return $this->driver->getConnection();
    }

    /**
     * Возвращает имя подключения
     *
     * @return string
     */
    public function getConnectionName (): string
    {
        return $this->connectionName;
    }

    /**
     * Возвращает объект драйвера подключения к БД
     *
     * @return ISqlDriver
     */
    public function getDriver ()
    {
        return $this->driver;
    }

    /**
     * Возвращает команду shell для создания бекапа для базы данныъ
     *
     * @param string $path     Путь к бекапу
     * @param string $postfix  Постфикс для файла
     * @param string $module   Имя модуля
     * @param array  $arTables Массив со списком таблиц
     * @param bool   $useGz    Флаг использования сжития gz
     * @param bool   $pastDate Флаг добавления даты в название файла
     * @param bool   $noData   Флаг необходимости сохранения только структуры таблиц, без данных
     * @param string $dbName   Имя базы данных, если не указана, будет взята текущая
     * @param string $dbUser   Пользователь базы данных, если не указан, будет взят текущий
     * @param string $dbPass   Пароль пользователя базы данных, если не указан, будет взят текущий
     *
     * @return string
     */
    public function getDumpCommand (
        string $path,
        string $postfix = null,
        string $module = null,
        array $arTables = [],
        bool $useGz = true,
        bool $pastDate = true,
        bool $noData = false,
        string $dbName = null,
        string $dbUser = null,
        string $dbPass = null
    ) {
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
        $comm .= "-u" . $dbUser . " -p" . $dbPass . " " . $dbName . " ";
        if (!empty($arTables))
        {
            foreach ($arTables as $tableName)
            {
                $comm .= $tableName . ' ';
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
        $comm .= $path . 'dump_' . $dbName;
        if (!is_null($module))
        {
            $comm .= '_' . $module;
        }
        if (!empty($arTables))
        {
            $comm .= '_tables';
        }
        if (!is_null($postfix) && $postfix !== false)
        {
            $comm .= '_' . $postfix;
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
     * Возвращает описание ошибки последнего запроса
     *
     * @return string
     */
    public function getError ()
    {
        return $this->driver->getError();
    }

    /**
     * Возвращает код ошибки последнего запроса
     *
     * @return int
     */
    public function getErrorNo ()
    {
        return $this->driver->getErrorNo();
    }

    /**
     * Возвращает ID добавленного элемента в последнем запросе
     *
     * @return int
     */
    public function getInsertId ()
    {
        return $this->driver->getInsertId();
    }

    /**
     * Возвращает количество полей, затронутых последним запросом
     *
     * @return int
     */
    public function getNumFields ()
    {
        return $this->driver->getNumFields();
    }

    /**
     * Возвращает количество затронутых последним запросом строк
     *
     * @return int
     */
    public function getNumRows ()
    {
        return $this->driver->getNumRows();
    }

    /**
     * Возвращает значение свойства queryStatistics
     *
     * @return QueryStatistics
     */
    public function getQueryStatistics (): QueryStatistics
    {
        return $this->queryStatistics;
    }

    /**
     * Экранирует специальные символы в указанной строке, используя метод используемого драйвера
     *
     * @param string $string Исходная строка
     *
     * @return string
     */
    public function getRealEscapeString (string $string)
    {
        return $this->driver->getRealEscapeString($string);
    }

    /**
     * Возвращает результат последнего запроса
     *
     * @return mixed|null
     */
    public function getResult ()
    {
        return $this->driver->getResult();
    }

    /**
     * Возвращает TRUE, если необходимо показать время выполнения всех запросов на странице, иначе FALSE
     *
     * @return bool
     */
    public function isStatisticsUsage ()
    {
        if (defined('SHOW_SQL_WORK_TIME') && SHOW_SQL_WORK_TIME === true)
        {
            return true;
        }

        return false;
    }

    /**
     * Возвращает TRUE, если последний запрос прошел без ошибок, иначе FALSE
     *
     * @return bool
     */
    public function isSuccess ()
    {
        return $this->driver->isSuccess();
    }

    /**
     * Возвращает TRUE, если текущее соединение не разорвано, иначе FALSE
     *
     * @return mixed
     */
    public function ping ()
    {
        return $this->driver->ping();
    }

    /**
     * Делает SQL запрос к БД получив на вход объект запроса
     * если установлено NO_QUERY_EXEC=TRUE, либо переменная приложения no_query_exec === true,
     * возвращает текст SQL-запроса, не выполняя его
     *
     * @param QueryBase $query
     *
     * @return DBResult|string
     * @throws SqlQueryException
     */
    public function query (QueryBase $query)
    {
        $sql = $query->getSql();
        if (empty($sql))
        {
            throw new SqlQueryException('No SQL Query', '', '');
        }
        if (
            (defined("NO_QUERY_EXEC") && NO_QUERY_EXEC === true)
            || Application::getInstance()->getAppParam('no_query_exec') === true
        )
        {
            return $query->getSql();
        }

        $queryInfo = new QueryInfo($sql);
        if ($this->isStatisticsUsage())
        {
            $queryInfo->start();
        }

        $this->driver->query($sql);

        if ($this->isStatisticsUsage())
        {
            $queryInfo->stop();
            $this->queryStatistics->addQueryInfo($queryInfo);
        }

        $result = new DBResult($this->driver, $query);

        return $result;
    }

    /**
     * Делает SQL запрос к БД создавая объект класса QueryBase
     *
     * @param string $sql SQL текст запроса
     *
     * @return DBResult
     * @throws SqlQueryException
     */
    public function querySQL (string $sql)
    {
        $query = new QueryBase($sql);
        $res = $query->exec();

        return $res;
    }

    /**
     * Переподключается к БД, используя сохраненные ранее настройки подключения
     *
     * @return mixed
     */
    public function reconnect ()
    {
        return $this->driver->reconnect();
    }

    /**
     * Восстанавливает БД из бекапа
     *
     * @return bool
     */
    public function restoreDB ()
    {
        $documentRoot = Application::getInstance()->getDocumentRoot();
        $dirBackupDb = Application::getInstance()->getSettings()->getDirBackupDb();

        if (!$documentRoot || !$dirBackupDb || file_exists($documentRoot . '/backup'))
        {
            return false;
        }

        //Создаем файл backup в корне, чтобы система понимала, что идет процесс восстановления БД
        $f1 = fopen($documentRoot . '/backup', 'w');
        fwrite($f1, date('Y-m-d H:i:s'));
        fclose($f1);

        $comm = $this->getCreateDbCommand($this->base);
        exec($comm);
        $fileTime = null;
        $filePath = null;
        $dir = $dirBackupDb;
        if (is_dir($dir))
        {
            if ($dh = opendir($dir))
            {
                while (($file = readdir($dh)) !== false)
                {
                    if (!is_dir($dir . $file) && $file != "." && $file != ".." && $file != ".htaccess")
                    {
                        if (strstr($file, $this->base) !== false)
                        {
                            if (is_null($fileTime))
                            {
                                $fileTime = filemtime($dir . $file);
                                $filePath = $dir . $file;
                            }
                            elseif (filemtime($dir . $file) > $fileTime)
                            {
                                $fileTime = filemtime($dir . $file);
                                $filePath = $dir . $file;
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
            unlink($documentRoot . '/backup');

            return true;
        }

        unlink($documentRoot . '/backup');

        return false;
    }

    /**
     * Отклоняет транзакцию
     *
     * @return mixed
     */
    public function rollbackTransaction ()
    {
        return $this->driver->rollbackTransaction();
    }

    /**
     * Устанавливает кодировку обмена данными с БД
     *
     * @param string $charset
     *
     * @return mixed
     */
    public function setCharset (string $charset)
    {
        return $this->driver->setCharset($charset);
    }

    /**
     * Устанавливает значение свойства connectionName
     *
     * @param string $connectionName Имя подключения
     *
     * @return Connection
     */
    public function setConnectionName (string $connectionName = 'default'): Connection
    {
        if (empty($connectionName))
        {
            $connectionName = 'default';
        }
        else
        {
            $connectionName = strtolower($connectionName);
        }
        $this->connectionName = $connectionName;

        return $this;
    }

    /**
     * Устанавливает драйвер подключения
     *
     * @param ISqlDriver $driver
     */
    public function setDriver (ISqlDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Стартует транзакцию
     *
     * @return mixed
     */
    public function startTransaction ()
    {
        return $this->driver->startTransaction();
    }

    /**
     * Подключает к серверу БД
     *
     * @return $this
     * @throws ConnectionException
     */
    protected function conn ()
    {
        $this->driver->connect($this->host, $this->base, $this->user, $this->pass);

        return $this;
    }

    /**
     * Возвращает shell команду восстановления БД из backup
     *
     * @param $filePath
     *
     * @return string
     */
    protected function getBackupCommand ($filePath)
    {
        //gunzip < /path/to/filename.sql.gz | mysql -uroot -prootpsw msergeev
        $comm = 'sudo gunzip < '
                . $filePath . ' | mysql -u'
                . $this->user . ' -p'
                . $this->pass . ' '
                . $this->base;

        return $comm;
    }

    /**
     * Возвращает shell команду создания БД
     *
     * @param string $baseName
     *
     * @return string
     */
    protected function getCreateDbCommand (string $baseName)
    {
        //mysqladmin -uUSER -pPASS create msergeev
        $comm = 'sudo mysqladmin -u'
                . $this->user . ' -p'
                . $this->pass . ' create '
                . $baseName;

        return $comm;
    }

    /**
     * Устанавливает необходимые параметры соединения
     *
     * @return $this
     * @throws DbException
     */
    protected function setConnectParams ()
    {
        if (Application::getInstance()->getSettings()->isUseUtf8())
        {
            $this->driver->setCharset('utf8');
            if (!$this->driver->isSuccess())
            {
                throw new DbException('Error set charset', $this->driver->getError());
            }
        }

        return $this;
    }
}
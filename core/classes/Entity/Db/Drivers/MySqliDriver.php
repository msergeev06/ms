<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Drivers;

use Ms\Core\Exceptions\Db\ConnectionException;
use Ms\Core\Exceptions\Db\DbException;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Interfaces\ISqlDriver;

/**
 * Класс Ms\Core\Entity\Db\Drivers\MySqliDriver
 * Драйвер для mysqli для работы с БД
 */
class MySqliDriver implements ISqlDriver
{
    /** @var null|string */
    protected $base = null;
    /** @var null|\mysqli */
    protected $connection = null;
    /** @var null|string */
    protected $host = null;
    /** @var null|string */
    protected $pass = null;
    /** @var null|\mysqli_result */
    protected $result = null;
    /** @var string */
    protected $sql = '';
    /** @var null|string */
    protected $user = null;

    public function __construct()
    {
    }

    /**
     * Магический метод при удалении объекта закрывает подключение к БД
     */
    public function __destruct()
    {
        if(!is_null($this->connection))
        {
            $this->close();
        }
    }

    /**
     * Закрывает подключение к БД
     *
     * @return bool
     * @unittest
     */
    public function close(): bool
    {
        if (!is_null($this->connection))
        {

            if ($this->connection->close())
            {
                $this->connection = null;

                return true;
            }
            else
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Применяет изменения произведенные в транзакции
     *
     * @return bool
     * @unittest
     */
    public function commitTransaction()
    {
        return $this->connection->commit();
    }

    /**
     * Подключается к БД
     *
     * @param string $host Хост
     * @param string $base Имя базы
     * @param string $user Имя пользователя
     * @param string $pass Пароль пользователя
     * @param int    $port Порт
     * @param null|string $socketPath Путь к сокет файлу
     *
     * @return mixed|\mysqli|null
     * @throws ConnectionException
     * @unittest
     */
    public function connect(string $host, string $base, string $user, string $pass, int $port = 3306, string $socketPath = null)
    {
        $this->host = $host;
        $this->base = $base;
        $this->user = $user;
        $this->pass = $pass;
        $this->connection = new \mysqli($host,$user,$pass,$base,$port,$socketPath);
        if(!$this->isSuccess())
        {
            throw new ConnectionException('Could not connect ', $this->getError());
        }

        return $this->connection;
    }

    /**
     * Разбирает результат запроса в ассоциативный массив
     *
     * @return array|null
     * @unittest
     */
    public function fetchArray()
    {
        if (!$this->result)
        {
            return null;
        }
        return $this->result->fetch_assoc();
    }

    /**
     * Возвращает количество строк, затронутых запросом
     *
     * @return int
     * @unittest
     */
    public function getAffectedRows(): int
    {
        $res = $this->connection->affected_rows;
        if (is_null($res))
        {
            $res = 0;
        }
        return $res;
    }

    /**
     * Возвращает объект подключения
     *
     * @return \mysqli|null
     * @unittest
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Возвращает текст ошибки
     *
     * @return string
     * @unittest
     */
    public function getError(): string
    {
        $error = $this->connection->connect_error;
        if (is_null($error))
        {
            $error = '';
        }
        return $error;
    }

    /**
     * Возвращает номер ошибки
     *
     * @return int
     * @unittest
     */
    public function getErrorNo(): int
    {
        return (int)$this->connection->connect_errno;
    }

    /**
     * Возвращает ID добавленной строки
     *
     * @return int
     * @unittest
     */
    public function getInsertId(): int
    {
        $res = $this->connection->insert_id;
        if (is_null($res))
        {
            $res = 0;
        }
        return $res;
    }

    /**
     * Возвращает количество полей в результате запроса
     *
     * @return int
     * @unittest
     */
    public function getNumFields(): int
    {
        $fields = $this->connection->field_count;
        if (is_null($fields))
        {
            $fields = 0;
        }
        return $fields;
    }

    /**
     * Возвращает количество строк в результате запроса
     *
     * @return int
     * @unittest
     */
    public function getNumRows(): int
    {
        $number = $this->result->num_rows;
        if (is_null($number))
        {
            $number = 0;
        }
        return $number;
    }

    /**
     * Экранирует переданную строку для безопасного сохранения в БД
     *
     * @param string $string Экранируемая строка
     *
     * @return string
     * @throws DbException
     * @unittest
     */
    public function getRealEscapeString(string $string): string
    {
        if (!$res = $this->connection->real_escape_string($string))
        {
            throw new DbException('Error escape string ',$this->getError());
        }

        return $res;
    }

    /**
     * Возвращает результат запроса
     *
     * @return \mysqli_result|null
     * @unittest
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Возвращает код запроса SQL
     *
     * @return string
     * @unittest
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * Возвращает TRUE, если нет ошибок в запросе
     *
     * @return bool
     * @unittest
     */
    public function isSuccess (): bool
    {
        if ($this->getErrorNo())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Пингует соединение с БД
     *
     * @return bool
     * @unittest
     */
    public function ping()
    {
        return $this->connection->ping();
    }

    /**
     * Выполняет запрос к БД
     *
     * @param string $sql Код SQL запроса
     *
     * @return MySqliDriver
     * @throws SqlQueryException
     * @unittest
     */
    public function query(string $sql): ISqlDriver
    {
        $this->sql = $sql;
        $this->result = $this->connection->query($sql);
        if (!$this->isSuccess())
        {
            throw new SqlQueryException('Query error',$this->getError(),$sql);
        }

        return $this;
    }

    /**
     * Закрывает подключение к БД и заново подключается
     *
     * @return bool
     * @throws ConnectionException
     * @unittest
     */
    public function reconnect()
    {
        if ($this->close())
        {
            $this->connect($this->host, $this->base, $this->user, $this->pass);
        }

        return $this->isSuccess();
    }

    /**
     * Отклоняет изменения в транзакции
     *
     * @return bool
     * @unittest
     */
    public function rollbackTransaction()
    {
        return $this->connection->rollback();
    }

    /**
     * Устанавливает кодировку подключения к БД
     *
     * @param string $charset
     *
     * @return bool
     * @unittest
     */
    public function setCharset(string $charset)
    {
        return $this->connection->set_charset($charset);
    }

    /**
     * Стартует тразакцию
     *
     * @return bool
     * @unittest
     */
    public function startTransaction()
    {
        return $this->connection->begin_transaction();
    }
}
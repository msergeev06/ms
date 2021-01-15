<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\Db\Tables\FieldsCollection;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Entity\System\Application;
use Ms\Core\Exceptions\Classes\NotImplementedException;
use Ms\Core\Exceptions\Db\SqlQueryException;

/**
 * Класс Ms\Core\Entity\Db\Query\QueryBase
 * Базовая сущность запроса к базе данных
 */
class QueryBase
{
    /**
     * @var string
     */
    protected $sql = null;
    /**
     * @var null|TableAbstract
     */
    protected $table = null;

    /**
     * Конструктор
     *
     * @param string $sql Код SQL запроса
     */
    public function __construct ($sql = null)
    {
        if (!is_null($sql))
        {
            $this->setSql($sql);
        }
    }

    /**
     * Выполняет запрос к базе данных
     *
     * @param bool $debug Флаг, требующий вернуть код SQL запроса вместо его исполнения
     *
     * @return DBResult|string
     * @throws SqlQueryException
     * @unittest
     */
    public function exec ($debug = false)
    {
        if (is_null($this->getSql()))
        {
            throw new SqlQueryException('No SQL Query', '', '');
        }
        if ($this->isReturnSql($debug))
        {
            return $this->getSql();
        }
        else
        {
            return $this->execQuery();
        }
    }

    /**
     * Возвращает имя класса, из которого вызван
     *
     * @return string
     * @unittest
     */
    public function getClassName ()
    {
        return get_called_class();
    }

    /**
     * Возвращает коллекцию сущностей полей таблицы
     *
     * @return FieldsCollection|null
     * @unittest
     */
    public function getFieldsCollection (): FieldsCollection
    {
        return (!is_null($this->getTable()) ? $this->getTable()->getMap() : null);
    }

    /**
     * Возвращает текст SQL запроса
     *
     * @return string
     * @unittest
     */
    public function getSql ()
    {
        return $this->sql;
    }

    /**
     * Возвращает объект таблицы
     *
     * @return TableAbstract|null
     * @unittest
     */
    public function getTable ()
    {
        return $this->table;
    }

    /**
     * Возвращает TRUE, если необходимо вернуть код SQL запроса вместо его исполнения
     *
     * @param bool $debug Флаг необходимости возврата кода SQL запроса
     *
     * @return bool
     * @unittest
     */
    public function isReturnSql (bool $debug = false)
    {
        return (
            $debug
            || (defined("NO_QUERY_EXEC") && NO_QUERY_EXEC === true)
            || Application::getInstance()->getAppParam('no_query_exec') === true
        );
    }

    /**
     * Устанавливает значение SQL запроса
     *
     * @param $sql
     *
     * @return QueryBase
     * @unittest
     */
    public function setSql ($sql)
    {
        $this->sql = $sql;

        return $this;
    }

    /**
     * Устанавливает объект таблицы
     *
     * @param TableAbstract $table Объект таблицы
     *
     * @return $this
     * @unittest
     */
    public function setTable (TableAbstract $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Собирает SQL запрос из параметров
     */
    protected function buildQuery ()
    {
        try
        {
            throw new NotImplementedException($this->getClassName() . '::buildQuery()');
        }
        catch (NotImplementedException $e)
        {
            die($e->showException());
        }
    }

    /**
     * Выполняет SQL запрос
     *
     * @return DBResult|string
     * @throws SqlQueryException
     */
    protected function execQuery ()
    {
        $conn = Application::getInstance()->getConnection();
        $res = $conn->query($this);
        if (!$res->isSuccess())
        {
            throw new SqlQueryException(
                "Error " . $this->getClassName(),
                $res->getResultErrorText(),
                $this->getSql()
            );
        }

        return $res;
    }
}
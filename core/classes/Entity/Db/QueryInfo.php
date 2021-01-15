<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db;

/**
 * Класс Ms\Core\Entity\Db\QueryInfo
 * Статистическая информация по запросу к БД
 */
class QueryInfo
{
    /** @var null|string */
    protected $sql = null;
    /** @var null|float */
    protected $queryStart = null;
    /** @var null|float */
    protected $queryStop = null;
    /** @var string */
    protected $uniqueId = null;

    /**
     * Конструктор класса QueryInfo
     *
     * @param string $sql SQL Запрос
     */
    public function __construct(string $sql)
    {
        $this->sql = $sql;
        $this->generateUniqueId();
    }

    /**
     * Устанавливает текст SQL запроса
     *
     * @param string $sql Текст SQL запроса
     *
     * @return $this
     * @unittest
     */
    public function setQuerySql (string $sql)
    {
        $this->sql = $sql;

        return $this;
    }

    /**
     * Возвращает текст SQL запроса
     *
     * @return string|null
     * @unittest
     */
    public function getQuerySql ()
    {
        return $this->sql;
    }

    /**
     * Генерирует уникальный ID запроса
     *
     * @return $this
     * @unittest
     */
    public function generateUniqueId ()
    {
        $this->uniqueId = uniqid();

        return $this;
    }

    /**
     * Возвращает уникальный ID запроса
     *
     * @return string
     * @unittest
     */
    public function getUniqueId ()
    {
        return $this->uniqueId;
    }

    /**
     * Стартует отсчет времени выполнения запроса
     *
     * @return $this
     * @unittest
     */
    public function start ()
    {
        $this->queryStart = round(microtime(true),4);

        return $this;
    }

    /**
     * Останавливает отсчет времени выполнения запроса
     *
     * @return $this
     * @unittest
     */
    public function stop ()
    {
        $this->queryStop = round(microtime(true),4);

        return $this;
    }

    /**
     * Возвращает время начала выполнения запроса
     *
     * @return int|null
     * @unittest
     */
    public function getQueryStart ()
    {
        return $this->queryStart;
    }

    /**
     * Возвращает время окончания выполнения запроса
     *
     * @return int|null
     * @unittest
     */
    public function getQueryStop ()
    {
        return $this->queryStop;
    }

    /**
     * Возвращает длительность выполнения запроса
     *
     * @return float|null
     * @unittest
     */
    public function getQueryTime ()
    {
        return ($this->queryStop - $this->queryStart);
    }
}
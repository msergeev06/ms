<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Db\QueryStatistics
 * Содержит коллекцию объектов с информацией об отработанных на странице SQL запросах
 */
class QueryStatistics extends Dictionary
{
    /**
     * Добавляет новый объект с информацией по запросу
     *
     * @param QueryInfo $queryInfo
     *
     * @return $this
     * @unittest
     */
    public function addQueryInfo (QueryInfo $queryInfo)
    {
        while ($this->offsetExists($queryInfo->getUniqueId()))
        {
            $queryInfo->generateUniqueId();
        }
        $this->offsetSet($queryInfo->getUniqueId(),$queryInfo);

        return $this;
    }

    /**
     * Возвращает указанный объект с информацией по запросу
     *
     * @param string $uniqueId
     *
     * @return QueryInfo|null
     * @unittest
     */
    public function getQueryInfo (string $uniqueId)
    {
        return $this->offsetGet($uniqueId);
    }

    /**
     * Возвращает количество объектов в коллекции
     *
     * @return int
     * @unittest
     */
    public function getQueryCount ()
    {
        return $this->count();
    }

    /**
     * Возвращает суммарное время выполнения всех запросов в коллекции
     *
     * @return float
     * @unittest
     */
    public function getAllQueryTime ()
    {
        if ($this->isEmpty())
        {
            return 0;
        }

        $sum = 0;
        /**
         * @var string $id
         * @var QueryInfo $queryInfo
         */
        foreach ($this->values as $id => $queryInfo)
        {
            $sum += (float)round($queryInfo->getQueryTime(),5);
        }

        return $sum;
    }
}
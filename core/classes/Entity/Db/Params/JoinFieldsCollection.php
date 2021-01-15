<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Params;

use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Db\Params\JoinFieldsCollection
 * Коллекция LEFT JOIN параметров GetListParams
 */
class JoinFieldsCollection extends Dictionary
{
    /** @var GetListParams */
    protected $objParams = null;

    /**
     * Конструктор класса JoinFieldsCollection
     *
     * @param GetListParams $params
     */
    public function __construct (GetListParams $params)
    {
        parent::__construct(null);
        $this->objParams = $params;
    }

    /**
     * Устанавливает объект GetListParams
     *
     * @return GetListParams
     * @unittest
     */
    public function getParams ()
    {
        return $this->objParams;
    }

    /**
     * Добавляет объект JoinField в коллекцию
     *
     * @param string        $joinFieldName Имя поля связанной таблицы
     * @param TableAbstract $joinTable     Объект связанной таблицы
     * @param string        $baseFieldName Имя поля базовой таблицы
     * @param TableAbstract $baseTable     Объект базовой таблицы
     *
     * @return $this
     * @unittest
     */
    public function addJoin (string $joinFieldName, TableAbstract $joinTable, string $baseFieldName, TableAbstract $baseTable)
    {
        $this->getParams()->getTableAliasCollection()->addAlias($joinTable);
        $this->getParams()->getTableAliasCollection()->addAlias($baseTable);
        $this->offsetSet(
            $joinTable->getTableName(),
            new JoinField(
                $joinFieldName,
                $joinTable,
                $this->getParams()->getTableAliasCollection()->getAlias($joinTable),
                $baseFieldName,
                $baseTable,
                $this->getParams()->getTableAliasCollection()->getAlias($baseTable)
            )
        );

        return $this;
    }

    /**
     * Возвращает ссылку на объект связанной таблицы
     *
     * @param TableAbstract $table Объект таблицы
     *
     * @return mixed|null
     * @unittest
     */
    public function getJoin (TableAbstract $table)
    {
        return $this->getJoinByTableName($table->getTableName());
    }

    /**
     * Возвращает TRUE, если ссылка на таблицу уже есть в коллекции, иначе возвращает FALSE
     *
     * @param TableAbstract $table
     *
     * @return bool
     * @unittest
     */
    public function isExists (TableAbstract $table)
    {
        return $this->offsetExists($table->getTableName());
    }

    /**
     * Возвращает ссылку на объект связанной таблицы
     *
     * @param string $tableName Имя таблицы
     *
     * @return mixed|null
     * @unittest
     */
    public function getJoinByTableName (string $tableName)
    {
        if ($this->offsetExists($tableName))
        {
            return $this->offsetGet($tableName);
        }

        return null;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\Params\GetListParams;
use Ms\Core\Entity\Db\Params\OrderCollection;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Query\QuerySelect
 * Сущность SELECT запроса к базе данных
 */
class QuerySelect extends QueryBase
{
    /**
     * @var GetListParams
     */
    protected $getListParams = null;
    /**
     * @var IField
     */
    protected $primaryKey = null;

    /**
     * Конструктор
     *
     * @param TableAbstract $table
     * @param GetListParams $getListParams
     */
    public function __construct (TableAbstract $table, GetListParams $getListParams)
    {
        parent::__construct(null);
        $this->setTable($table);
        $this->setPrimaryKey($table->getMap()->getPrimaryField());
        $this->setGetListParams($getListParams);

        if ($this->getGetListParams()->getOrderCollection()->isEmpty())
        {
            $this->getGetListParams()->getOrderCollection()->addOrder(
                $this->getPrimaryKey()->getColumnName(),
                OrderCollection::DIRECTION_ASC
            )
            ;
        }

        $sql = $this->buildQuery();
        $this->setSql($sql);
    }

    /**
     * Возвращает объект параметров запроса
     *
     * @return GetListParams
     * @unittest
     */
    public function getGetListParams ()
    {
        return $this->getListParams;
    }

    /**
     * Собирает SQL запрос из параметров
     *
     * @return string
     */
    protected function buildQuery ()
    {
        $builder = SqlQueryBuilder::getInstance();
        $sql = "";

        $sql .= $builder->createSelect($this->getGetListParams());

        $sql .= $builder->createFrom($this->getGetListParams());

        $sql .= $builder->createSqlJoin($this->getGetListParams());

        $sql .= $builder->createSqlWhere($this->getGetListParams());

        // $sql .= $this->createSqlGroup();

        $sql .= $builder->createSqlOrder($this->getGetListParams());

        $sql .= $builder->createSqlLimit($this->getGetListParams());

        return $sql;
    }

    /**
     * Возвращает объект PRIMARY поля
     *
     * @return IField
     */
    protected function getPrimaryKey ()
    {
        return $this->primaryKey;
    }

    /**
     * Возвращает имя таблицы
     *
     * @return string
     */
    protected function getTableName ()
    {
        return $this->getGetListParams()->getTable()->getTableName();
    }

    /**
     * Устанавливает объект параметров запроса
     *
     * @param GetListParams $params
     *
     * @return $this
     */
    protected function setGetListParams (GetListParams $params)
    {
        $this->getListParams = $params;

        return $this;
    }

    /**
     * Устанавливает объект PRIMARY поля
     *
     * @param IField $field
     *
     * @return $this
     */
    protected function setPrimaryKey (IField $field)
    {
        $this->primaryKey = $field;

        return $this;
    }
}
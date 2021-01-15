<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Query\QueryDelete
 * Сущность DELETE запроса к базе данных
 */
class QueryDelete extends QueryBase
{
    /** @var mixed */
    protected $deletePrimary = null;
    /** @var null|string */
    protected $sqlWhere = null;

    /**
     * Конструктор
     *
     * @param mixed         $deletePrimary Значение PRIMARY поля удаляемой записи, либо NULL
     * @param TableAbstract $table         Объект таблицы
     * @param string|null   $sSqlWhere     SQL код условия удаления записей
     *
     * @throws ArgumentNullException
     */
    public function __construct ($deletePrimary, TableAbstract $table, string $sSqlWhere = null)
    {
        if (!is_null($table))
        {
            $this->setTable($table);
        }
        else
        {
            throw new ArgumentNullException('table');
        }

        if (!is_null($deletePrimary))
        {
            $this->setDeletePrimary($deletePrimary);
        }
        elseif (is_null($sSqlWhere))
        {
            throw new ArgumentNullException('deletePrimary');
        }

        $this->setSqlWhere($sSqlWhere);

        $this->setSql($this->buildQuery());
    }

    /**
     * Возвращает SQL код условия удаления записей
     *
     * @return string|null
     * @unittest
     */
    public function getSqlWhere ()
    {
        return $this->sqlWhere;
    }

    /**
     * Устанавливает SQL код условия удаления записей
     *
     * @param string|null $sqlWhere
     *
     * @return $this
     * @unittest
     */
    public function setSqlWhere (string $sqlWhere = null)
    {
        $this->sqlWhere = $sqlWhere;
        if (!is_null($sqlWhere))
        {
            try
            {
                $this->setSql($this->buildQuery());
            }
            catch (ArgumentNullException $e)
            {
            }
        }

        return $this;
    }

    /**
     * Возвращает значение PRIMARY поля удаляемой записи
     *
     * @return mixed
     * @unittest
     */
    public function getDeletePrimary ()
    {
        return $this->deletePrimary;
    }

    /**
     * Устанавливает значение PRIMARY поля удаляемой записи
     *
     * @param mixed $deletePrimary Значение PRIMARY поля удаляемой записи
     *
     * @return QueryDelete
     * @unittest
     */
    public function setDeletePrimary ($deletePrimary)
    {
        $this->deletePrimary = $deletePrimary;
        try
        {
            $this->setSql($this->buildQuery());
        }
        catch (ArgumentNullException $e)
        {
        }

        return $this;
    }

    /**
     * Собирает SQL запрос из параметров
     *
     * @return string
     * @throws ArgumentNullException
     *
     */
    protected function buildQuery ()
    {
        $helper = new SqlHelper($this->getTable()->getTableName());
        $sql = "DELETE FROM \n\t" . $helper->wrapTableQuotes() . "\n";
        $sql .= "WHERE \n\t";

        if (empty($this->sqlWhere))
        {
            /**
             * @var IField $objData
             */
            foreach ($this->getTable()->getMap() as $objData)
            {
                if ($objData->isPrimary())
                {
                    $primaryField = $objData->getColumnName();
                    $primaryObj = $objData;
                    break;
                }
            }
            if (!isset($primaryField))
            {
                throw new ArgumentNullException('primaryField');
            }
            if (!isset($primaryObj))
            {
                throw new ArgumentNullException('primaryObj');
            }
            $sql .= $helper->wrapFieldQuotes($primaryField) . " = ";
            $sql .= $primaryObj->getSqlValue($this->deletePrimary) . "\n";
            $sql .= "LIMIT 1";
        }
        else
        {
            $sql .= $this->sqlWhere;
        }

        return $sql;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Params;

use Ms\Core\Entity\Db\Tables\TableAbstract;

/**
 * Класс Ms\Core\Entity\Db\Params\JoinField
 * Описание LEFT JOIN GetListParams
 */
class JoinField
{
    /** @var string */
    protected $baseFieldName = null;
    /** @var TableAbstract */
    protected $baseTable = null;
    /** @var string */
    protected $baseTableAlias = null;
    /** @var string */
    protected $refFieldName = null;
    /** @var TableAbstract */
    protected $refTable = null;
    /** @var string */
    protected $refTableAlias = null;

    public function __construct (
        string $baseFieldName,
        TableAbstract $baseTable,
        string $baseTableAlias,
        string $refFieldName,
        TableAbstract $refTable,
        string $refTableAlias
    ) {
        $this->baseFieldName = $baseFieldName;
        $this->baseTable = $baseTable;
        $this->baseTableAlias = $baseTableAlias;
        $this->refFieldName = $refFieldName;
        $this->refTable = $refTable;
        $this->refTableAlias = $refTableAlias;
    }

    /**
     * Возвращает значение свойства baseFieldName
     *
     * @return string
     * @unittest
     */
    public function getBaseFieldName (): string
    {
        return $this->baseFieldName;
    }

    /**
     * Возвращает значение свойства baseTable
     *
     * @return TableAbstract
     * @unittest
     */
    public function getBaseTable (): TableAbstract
    {
        return $this->baseTable;
    }

    /**
     * Возвращает значение свойства baseTableAlias
     *
     * @return string
     * @unittest
     */
    public function getBaseTableAlias (): string
    {
        return $this->baseTableAlias;
    }

    /**
     * Возвращает значение свойства refFieldName
     *
     * @return string
     * @unittest
     */
    public function getRefFieldName (): string
    {
        return $this->refFieldName;
    }

    /**
     * Возвращает значение свойства refTable
     *
     * @return TableAbstract
     * @unittest
     */
    public function getRefTable (): TableAbstract
    {
        return $this->refTable;
    }

    /**
     * Возвращает значение свойства refTableAlias
     *
     * @return string
     * @unittest
     */
    public function getRefTableAlias (): string
    {
        return $this->refTableAlias;
    }


}
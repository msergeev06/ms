<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Params;

use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Params\Order
 * Описывает поле для сортировки значений
 */
class Order
{
    /** @var TableAbstract */
    protected $table = null;
    /** @var string */
    protected $tableAlias = null;
    /** @var string */
    protected $fieldName = null;
    /** @var IField */
    protected $field = null;
    /** @var string */
    protected $direction = 'ASC';

    public function __construct (
        TableAbstract $table,
        string $tableAlias,
        string $fieldName,
        IField $field,
        string $direction = OrderCollection::DIRECTION_ASC
    ) {
        $this->table = $table;
        $this->tableAlias = $tableAlias;
        $this->fieldName = $fieldName;
        $this->field = $field;
        $direction = strtoupper($direction);
        if (in_array($direction,[OrderCollection::DIRECTION_ASC,OrderCollection::DIRECTION_DESC]))
        {
            $this->direction = $direction;
        }
        else
        {
            $this->direction = OrderCollection::DIRECTION_ASC;
        }
    }

    /**
     * Возвращает значение свойства table
     *
     * @return TableAbstract
     * @unittest
     */
    public function getTable (): TableAbstract
    {
        return $this->table;
    }

    /**
     * Возвращает значение свойства tableAlias
     *
     * @return string
     * @unittest
     */
    public function getTableAlias (): string
    {
        return $this->tableAlias;
    }

    /**
     * Возвращает значение свойства fieldName
     *
     * @return string
     * @unittest
     */
    public function getFieldName (): string
    {
        return $this->fieldName;
    }

    /**
     * Возвращает значение свойства field
     *
     * @return IField
     * @unittest
     */
    public function getField (): IField
    {
        return $this->field;
    }

    /**
     * Возвращает значение свойства direction
     *
     * @return string
     * @unittest
     */
    public function getDirection (): string
    {
        return $this->direction;
    }


}
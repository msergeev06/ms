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
 * Класс Ms\Core\Entity\Db\Params\Filter
 * Описывает параметр фильтра
 */
class Filter
{
    /** @var TableAbstract */
    protected $table = null;
    /** @var string */
    protected $tableAlias = null;
    /** @var string */
    protected $fieldName = null;
    /** @var IField */
    protected $field = null;
    /** @var mixed */
    protected $value = null;
    /** @var string */
    protected $expression = null;

    public function __construct (
        TableAbstract $table,
        string $tableAlias,
        string $fieldName,
        IField $field,
        $value,
        string $expression = ''
    ) {
        $this->table = $table;
        $this->tableAlias = $tableAlias;
        $this->fieldName = $fieldName;
        $this->field = $field;
        $this->value = $value;
        $this->expression = $expression;
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
     * Возвращает значение свойства value
     *
     * @return mixed
     * @unittest
     */
    public function getValue ()
    {
        return $this->value;
    }

    /**
     * Возвращает значение свойства expression
     *
     * @return string
     * @unittest
     */
    public function getExpression (): string
    {
        return $this->expression;
    }


}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Params;

use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Params\SelectField
 * Поле, отбираемое при SELECT запросе
 */
class SelectField
{
    /** @var TableAbstract */
    protected $table = null;
    /** @var string */
    protected $tableAlias = null;
    /** @var string */
    protected $fieldColumnName = null;
    /** @var string */
    protected $fieldAlias = null;
    /** @var IField */
    protected $field = null;

    /**
     * Конструктор класса SelectField
     *
     * @param TableAbstract $table
     * @param string        $tableAlias
     * @param string        $fieldColumnName
     * @param string|null   $fieldAlias
     *
     * @throws SystemException
     */
    public function __construct (TableAbstract $table, string $tableAlias, string $fieldColumnName, string $fieldAlias = null)
    {
        $this->table = $table;
        $this->tableAlias = $tableAlias;
        $this->fieldColumnName = $fieldColumnName;
        if (is_null($fieldAlias))
        {
            $fieldAlias = $fieldColumnName;
        }
        $this->fieldAlias = $fieldAlias;
        if (!$table->getMap()->isEmpty())
        {
            /**
             * @var string $fieldName
             * @var IField $field
             */
            foreach ($table->getMap() as $fieldName => $field)
            {
                if ($fieldName == $fieldColumnName)
                {
                    $this->field = $field;
                    break;
                }
                elseif ($field->getColumnName() == $fieldColumnName)
                {
                    $this->field = $field;
                    break;
                }
            }
        }
        if (is_null($this->field))
        {
            throw new SystemException(
                'Не удалось определить объект поля "'.$fieldColumnName.'"',
                0,
                __FILE__,
                __LINE__
            );
        }
    }

    /**
     * Возвращает объект, описывающий таблицу
     *
     * @return TableAbstract
     * @unittest
     */
    public function getTable (): TableAbstract
    {
        return $this->table;
    }

    /**
     * Возвращает алиас таблицы
     *
     * @return string
     * @unittest
     */
    public function getTableAlias (): string
    {
        return $this->tableAlias;
    }

    /**
     * Возвращает имя поля в БД
     *
     * @return string
     * @unittest
     */
    public function getFieldColumnName (): string
    {
        return $this->fieldColumnName;
    }

    /**
     * Возвращает алиас поля
     *
     * @return string
     * @unittest
     */
    public function getFieldAlias (): string
    {
        return $this->fieldAlias;
    }

    /**
     * Возвращает объект, описывающий поле таблицы
     *
     * @return IField
     * @unittest
     */
    public function getField (): IField
    {
        return $this->field;
    }
}
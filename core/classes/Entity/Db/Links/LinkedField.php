<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Links;

use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;
use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Entity\Db\Links\LinkedField
 * Содержит данные о связи с полем той же либо другой таблицы
 */
class LinkedField
{
    /** @var TableAbstract */
    protected $table = null;
    /** @var string */
    protected $fieldName = null;
    /** @var bool */
    protected $useForeign = true;
    /** @var null|ForeignKey */
    protected $foreignKeySetup = null;

    /**
     * Конструктор класса LinkedField
     *
     * @param TableAbstract   $table
     * @param string          $fieldName
     * @param ForeignKey|null $foreignKey
     * @param bool            $useForeign
     */
    public function __construct (TableAbstract $table, string $fieldName, ForeignKey $foreignKey = null, bool $useForeign = true)
    {
        try
        {
            $this->setTable($table);
            $this->setFieldName($fieldName);
            $this->setForeignKeySetup($foreignKey);
            $this->setUseForeign($useForeign);
        }
        catch (SystemException $e)
        {
        }
    }

    /**
     * Возвращает флаг того, является ли связанное поле FOREIGN KEY
     *
     * @return bool
     * @unittest
     */
    public function isUseForeign (): bool
    {
        return $this->useForeign;
    }

    /**
     * Устанавливает флаг того, является ли связанное поле FOREIGN KEY
     *
     * @param bool $useForeign
     *
     * @return LinkedField
     * @unittest
     */
    public function setUseForeign (bool $useForeign = true): LinkedField
    {
        $this->useForeign = $useForeign;

        return $this;
    }

    /**
     * Возвращает объект настройки FOREIGN KEY для поля
     *
     * @return ForeignKey|null
     * @unittest
     */
    public function getForeignKeySetup (): ForeignKey
    {
        return $this->foreignKeySetup;
    }

    /**
     * Устанавливает объект настройки FOREIGN KEY для поля
     *
     * @param ForeignKey|null $foreignKeySetup
     *
     * @return LinkedField
     * @unittest
     */
    public function setForeignKeySetup (ForeignKey $foreignKeySetup = null): LinkedField
    {
        if (is_null($foreignKeySetup))
        {
            $this->foreignKeySetup = new ForeignKey();
        }
        else
        {
            $this->foreignKeySetup = $foreignKeySetup;
        }

        return $this;
    }



    /**
     * Возвращает связанную таблицу
     *
     * @return TableAbstract
     * @unittest
     */
    public function getTable (): TableAbstract
    {
        return $this->table;
    }

    /**
     * Устанавливает связанную таблицу
     *
     * @param TableAbstract $table
     *
     * @return $this
     * @unittest
     */
    public function setTable (TableAbstract $table): LinkedField
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Возвращает название связанного поля
     *
     * @return string
     * @unittest
     */
    public function getFieldName (): string
    {
        return $this->fieldName;
    }

    /**
     * Устанавливает название связанного поля
     *
     * @param string $fieldName
     *
     * @return LinkedField
     * @throws ArgumentNullException
     * @unittest
     */
    public function setFieldName (string $fieldName): LinkedField
    {
        if (is_null($this->table))
        {
            throw new ArgumentNullException('table');
        }
        else
        {
            if (empty($fieldName))
            {
                throw new ArgumentNullException('fieldName');
            }
/*
            //Приводит к рекурсии
            elseif (!$this->getTable()->getMap()->isExists($fieldName))
            {
                throw new ArgumentOutOfRangeException('fieldName', implode('|',$this->getTable()->getMap()->getList()));
            }*/
        }

        $this->fieldName = $fieldName;

        return $this;
    }
}
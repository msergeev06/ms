<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\Tables\FieldsCollection;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Exceptions\Arguments\ArgumentException;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Query\QueryUpdate
 * Сущность UPDATE запроса к базе данных
 */
class QueryUpdate extends QueryBase
{
    /**
     * @var null|string
     */
    protected $sSqlWhere = null;
    /**
     * @var array
     */
    private $updateArray = null;
    /**
     * @var mixed
     */
    private $updatePrimary = null;

    /**
     * Конструктор
     * TODO: Переделать updatePrimary на возможность добавлять массив полей со значениями
     *
     * @param mixed         $updatePrimary Значение primary поля таблицы
     * @param array         $updateArray   Массив обновляемых полей
     * @param TableAbstract $table         Объект, описывающий таблицу
     * @param string        $sSqlWhere     SQL код WHERE, если нужно обновить не по primary полю
     *
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ArgumentException
     */
    public function __construct ($updatePrimary, array $updateArray, TableAbstract $table, string $sSqlWhere = null)
    {
        if (!is_null($updatePrimary))
        {
            $this->updatePrimary = $updatePrimary;
        }
        if (empty($updateArray))
        {
            throw new ArgumentNullException('updateArray');
        }
        else
        {
            $this->updateArray = $updateArray;
        }
        if (is_null($table))
        {
            throw new ArgumentNullException('updateArray');
        }
        else
        {
            $this->setTable($table);
        }

        if (!is_null($sSqlWhere))
        {
            $this->sSqlWhere = $sSqlWhere;
        }

        $this->setSql($this->buildQuery());
    }

    /**
     * Возвращает дополнительный SQL код WHERE, если он задан, либо NULL
     *
     * @return null|string
     */
    public function getSqlWhere ()
    {
        return $this->sSqlWhere;
    }

    /**
     * Собирает SQL запрос из параметров
     *
     * @return string
     * @throws ArgumentException
     * @throws ArgumentOutOfRangeException
     */
    protected function buildQuery ()
    {
        $helper = new SqlHelper($this->getTable()->getTableName());

        $this->normalizeUpdatePrimaryValue();
        $collectionDefaultValues = $this
            ->getTable()
            ->getMap()
            ->getFieldsWithDefaultValues(
                Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_UPDATE
            )
        ;

        $sql = "UPDATE \n\t" . $helper->wrapTableQuotes() . "\nSET\n";
        $sql .= $this->createSqlFromUpdateArray($collectionDefaultValues);
        $sql .= $this->createSqlFromDefaultValues($collectionDefaultValues);
        $sql .= "\nWHERE\n\t";
        $sql .= $this->createSqlWhere();
        // $sql .= $this->createSqlLimit(1);
        $sql .= ";";

        return $sql;
    }

    /**
     * @param FieldsCollection $collectionDefaultValues
     *
     * @return string
     */
    private function createSqlFromDefaultValues (FieldsCollection $collectionDefaultValues)
    {
        $helper = new SqlHelper($this->getTable()->getTableName());
        $sql = '';

        if (!$collectionDefaultValues->isEmpty())
        {
            /** @var Fields\ScalarFieldAbstract $objData */
            foreach ($collectionDefaultValues as $objData)
            {
                $sql .= ",\n\t" . $helper->wrapQuotes($objData->getColumnName()) . " = ";
                if ($objData->isDefaultSql(Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_UPDATE))
                {
                    $sql .= $objData->getDefaultValue(Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_UPDATE);
                }
                else
                {
                    $sql .= $objData->getSqlValue(
                        $objData->saveDataModification(
                            $objData->getDefaultValue(
                                Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_UPDATE
                            )
                        )
                    );
                }
            }
        }

        return $sql;
    }

    /**
     * @param FieldsCollection $collectionDefaultValues
     *
     * @return string
     * @throws ArgumentOutOfRangeException
     */
    private function createSqlFromUpdateArray (FieldsCollection $collectionDefaultValues)
    {
        $table = $this->getTable();
        $helper = new SqlHelper($table->getTableName());
        $sql = '';
        $bFirst = true;
        foreach ($this->updateArray as $fieldName => $value)
        {
            if ($table->getMap()->isExists($fieldName))
            {
                if ($bFirst)
                {
                    $bFirst = false;
                }
                else
                {
                    $sql .= ",\n";
                }
                $sql .= "\t" . $helper->wrapQuotes($table->getMap()->getField($fieldName)->getColumnName())
                        . " = ";

                $value = $table->getMap()->getField($fieldName)->saveDataModification($value);
                if (is_null($value))
                {
                    $sql .= 'NULL';
                }
                else
                {
                    $sql .= $table->getMap()->getField($fieldName)->getSqlValue($value);
                }
            }
            else
            {
                throw new ArgumentOutOfRangeException('arUpdate[' . $fieldName . ']');
            }
            if ($collectionDefaultValues->isExists($fieldName))
            {
                $collectionDefaultValues->deleteField($fieldName);
            }
        }

        return $sql;
    }

    /**
     * @return string
     * @throws ArgumentException
     */
    private function createSqlWhere ()
    {
        $helper = new SqlHelper($this->getTable()->getTableName());
        $primaryObj = $this->getPrimaryFieldObject();
        $sql = '';

        if (is_null($this->sSqlWhere))
        {
            $sql .= $helper->wrapFieldQuotes($primaryObj->getColumnName()) . " =";
            $sql .= $primaryObj->getSqlValue($this->updatePrimary);
        }
        else
        {
            $sql .= $this->sSqlWhere;
        }

        return $sql;
    }

    /**
     * @return IField|null
     * @throws ArgumentException
     */
    private function getPrimaryFieldObject ()
    {
        /** @var IField $primaryObj */
        $primaryObj = $this->getTable()->getMap()->getPrimaryField();
        if (is_null($primaryObj))
        {
            throw new ArgumentException(
                'У таблицы "' . $this->getTable()->getTableName() . '" не установлено PRIMARY поле'
            );
        }

        return $primaryObj;
    }

    /**
     * @throws ArgumentException
     * @throws ArgumentNullException
     */
    private function normalizeUpdatePrimaryValue ()
    {
        $primaryObj = $this->getPrimaryFieldObject();

        //Если PRIMARY ключ не был задан явно, но присутствует в массиве - используем его
        if (is_null($this->updatePrimary) && array_key_exists($primaryObj->getName(), $this->updateArray))
        {
            $this->updatePrimary = $this->updateArray[$primaryObj->getName()];
        }
        elseif (is_null($this->updatePrimary))
        {
            throw new ArgumentNullException('updatePrimary');
        }
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Query\QueryCreate
 * Сущность CREATE запроса к базе данных
 */
class QueryCreate extends QueryBase
{
    /** @var array */
    protected $arLinked = [];

    /**
     * Создает SQL запрос типа "create"
     *
     * @param TableAbstract $table
     * @unittest
     */
    public function __construct (TableAbstract $table)
    {
        $this->setTable($table);

        $this->setSql($this->buildQuery());
    }

    protected function buildQuery ()
    {
        $helper = new SqlHelper($this->getTable()->getTableName());

        $primaryFieldName = null;
        $sql = "CREATE TABLE IF NOT EXISTS " . $this->getTable()->getTableName() . " (\n\t";
        $bFirst = true;
        $bAutoIncrement = false;
        $collection = $this->getTable()->getMap();

        /**
         * @var IField $objData
         */
        foreach ($collection as $objData)
        {
            //var_dump ($objData);
            if ($bFirst)
            {
                $bFirst = false;
            }
            else
            {
                $sql .= ",\n\t";
            }
            //Если поле имеет флаг PRIMARY, сохраняем его
            if ($objData->isPrimary())
            {
                $primaryFieldName = $objData->getColumnName();
            }
            //Получаем название поля базы данных
            $fieldName = $objData->getColumnName();
            //Добавляем название поля и его тип
            $sql .= $helper->wrapQuotes($fieldName) . " " . $objData->getDataType();
            //Получаем sql код, устанавливающий размер значения, если необходимо
            $sql .= $objData->getSizeSql() . ' ';
            //Обработка значения по-умолчанию
            $sql .= $this->getDefaultValue($objData);
            //Если значение поля должно быть уникальным, устанавливаем соответствующий праметр
            if ($objData->isUnique())
            {
                $sql .= 'UNIQUE ';
            }
            //Обработка автоинкремента
            if ($objData->isAutocomplete())
            {
                $sql .= "AUTO_INCREMENT ";
                $bAutoIncrement = true;
            }
            //Обработка описания поля таблицы
            if (!is_null($objData->getTitle()))
            {
                $sql .= "COMMENT '" . $objData->getTitle() . "'";
            }
            //Обработка линкованных полей
            if (!is_null($objData->getLink()) && $objData->getLink()->isUseForeign())
            {
                $this->arLinked[] = $objData;
            }
        }
        //Указываем PRIMARY ключ, если существует
        if (!is_null($primaryFieldName))
        {
            $sql .= ",\n\tPRIMARY KEY (" . $helper->wrapQuotes($primaryFieldName) . ")";
        }
        //Если существуют связи таблиц, указываем их
        if (!empty($this->arLinked))
        {
            $sql .= $this->getLinkedFields();
        }
        //Если есть дополнительный код, добавляем его
        if (!is_null($innerSql = $this->getTable()->getInnerCreateSql()))
        {
            $sql .= ",\n\t";
            $sql .= $innerSql;
        }
        $sql .= "\n\t) ENGINE=InnoDB CHARACTER SET=utf8 COMMENT=\"" . $this->getTable()->getTableTitle() . "\" ";
        if ($bAutoIncrement)
        {
            $sql .= "AUTO_INCREMENT=1 ";
        }
        $sql .= ";";

        return $sql;
    }

    /**
     * Возвращает значение по-умолчанию для CREATE запроса
     *
     * @param IField $objData
     *
     * @return string
     */
    private function getDefaultValue (IField $objData)
    {
        $sql = '';
        $isNotNull = ($objData->isPrimary() || ($objData->isRequired() && !$objData->isRequiredNull()));

        if (
            !is_null($objData->getDefaultValue(Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_CREATE))
            && $isNotNull
        )
        {
            $sql .= "NOT NULL ";
            if ($objData->isDefaultSql(Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_CREATE))
            {
                $sql .= "DEFAULT "
                        . $objData->getDefaultValue(Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_CREATE)
                        . " ";
            }
            else
            {
                /** @var Fields\ScalarFieldAbstract $class */
                $value = $objData->saveDataModification(
                    $objData->getDefaultValue(Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_CREATE)
                );
                $sql .= "DEFAULT " . $objData->getSqlValue($value) . " ";
            }
        }
        //Если значение обязательное
        elseif ($isNotNull)
        {
            $sql .= "NOT NULL ";
        }
        //Если значение не обязательное
        elseif (!$isNotNull)
        {
            $sql .= "DEFAULT NULL ";
        }

        return $sql;
    }

    /**
     * Возвращает часть SQL запроса, касающийся FOREIGN KEY
     *
     * @return string
     */
    private function getLinkedFields ()
    {
        $sql = '';
        $helper = new SqlHelper();
        if (!empty($this->arLinked))
        {
            /** @var IField $objLinked */
            foreach ($this->arLinked as $objLinked)
            {
                if (!is_null($link = $objLinked->getLink()))
                {
                    if ($link->isUseForeign())
                    {
                        $sql .= ",\n\tFOREIGN KEY (" . $helper->wrapQuotes($objLinked->getColumnName()) . ")"
                                . " REFERENCES " . $helper->wrapQuotes($link->getTable()->getTableName())
                                . "(" . $helper->wrapQuotes($link->getFieldName())
                                . ")\n\t\t";
                        $sql .= "ON UPDATE " . $link->getForeignKeySetup()->getOnUpdate() . "\n\t\t";
                        $sql .= "ON DELETE " . $link->getForeignKeySetup()->getOnDelete();
                    }
                }
            }
        }

        return $sql;
    }
}
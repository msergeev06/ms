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
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Params\FilterCollection
 * Коллекция параметров фильтра
 */
class FilterCollection extends Dictionary
{
    const FILTER_LOGIC_AND = 'AND';
    const FILTER_LOGIC_OR = 'OR';

    const EXPRESSION_TRIPLE_CHAR = [
        "!><" => "NB",  //not between
        "s!=" => "SNI",  //sql not Identical
        "s!%" => "SNS",  //sql not substring
    ];
    const EXPRESSION_DOUBLE_CHAR = [
        "!=" => "NI",   //not Identical
        "!%" => "NS",   //not substring
        "><" => "B",    //between
        ">=" => "GE",   //greater or equal
        "<=" => "LE",   //less or equal
        "s=" => "SE",   //sql equal
        "s%" => "SS",   //sql LIKE
        "s>" => "SG",   //sql greater
        "s<" => "SL",   //sql less
    ];
    const EXPRESSION_SINGLE_CHAR = [
        "=" => "I",     //Identical
        "%" => "S",     //substring
        "?" => "?",     //logical
        ">" => "G",     //greater
        "<" => "L",     //less
        "!" => "N",     //not field LIKE val
    ];

    /** @var GetListParams */
    protected $objParams = null;
    /** @var string */
    protected $logic = 'AND';

    /**
     * Конструктор класса FilterCollection
     *
     * @param GetListParams $params
     */
    public function __construct (GetListParams $params)
    {
        parent::__construct(null);
        $this->objParams = $params;
    }

    /**
     * Устанавливает логику фильтра ИЛИ (OR)
     *
     * @return $this
     * @unittest
     */
    public function setLogicOr ()
    {
        $this->logic = self::FILTER_LOGIC_OR;

        return $this;
    }

    /**
     * Устанавливает логику фильтра И (AND)
     *
     * @return $this
     * @unittest
     */
    public function setLogicAnd ()
    {
        $this->logic = self::FILTER_LOGIC_AND;

        return $this;
    }

    /**
     * Возвращает тип логики фильтра
     *
     * @return string
     * @unittest
     */
    public function getLogic ()
    {
        return $this->logic;
    }

    /**
     * Возвращает ссылку на объект GetListParams
     *
     * @return GetListParams
     * @unittest
     */
    public function getParams ()
    {
        return $this->objParams;
    }

    /**
     * Устанавливает параметры фильтра из массива
     *
     * @param array $arFilter
     *
     * @return $this
     * @unittest
     */
    public function setFromArray (array $arFilter/*, FilterCollection $link = null*/)
    {
        // if (is_null($link))
        // {
            $link = $this;
        // }
        if (empty($arFilter))
        {
            return $link;
        }

        $bFirst = true;
        foreach ($arFilter as $key => $value)
        {
            if ($bFirst)
            {
                if (strtoupper($key) == 'LOGIC' && in_array(strtoupper($value),[self::FILTER_LOGIC_AND,self::FILTER_LOGIC_OR]))
                {
                    switch (strtoupper($value))
                    {
                        case self::FILTER_LOGIC_AND:
                            $link->setLogicAnd();
                            break;
                        case self::FILTER_LOGIC_OR:
                            $link->setLogicOr();
                            break;
                    }
                    $bFirst = false;
                    continue;
                }
                else
                {
                    $bFirst = false;
                }
            }
            if (is_numeric($key) && is_array($value))
            {
                $new = (new FilterCollection($link->getParams()));
                $new->setFromArray($value/*, $new*/);
                $this->offsetSet($link->count(),$new);
                continue;
            }
            $this->addFilter($key,$value);
        }

        return $this;
    }

    /**
     * Добавляет параметр фильтра
     *
     * @param string $fieldName
     * @param        $value
     *
     * @return $this
     * @unittest
     */
    public function addFilter (string $fieldName, $value)
    {
        $clearFieldName = $this->clearFieldName($fieldName);
        $fieldTable = $this->getParams()->getTable();
        $field = $this->getFieldByName($clearFieldName, $fieldTable);
        if (is_null($field))
        {
            return $this;
        }
        $expression = $this->getExpression ($fieldName);

        $filter = new Filter(
            $fieldTable,
            $this->getParams()->getTableAliasCollection()->getAlias($fieldTable),
            $clearFieldName,
            $field,
            $value,
            $expression
        );
        $this->offsetSet($this->count(),$filter);

        return $this;
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    protected function getExpression (string $fieldName)
    {
        $op = substr($fieldName, 0, 3);
        if ($op && isset(self::EXPRESSION_TRIPLE_CHAR[$op]))
        {
            return $op;
        }
        $op = substr($fieldName, 0, 2);
        if ($op && isset(self::EXPRESSION_DOUBLE_CHAR[$op]))
        {
            return $op;
        }
        $op = substr($fieldName, 0, 1);
        if ($op && isset(self::EXPRESSION_SINGLE_CHAR[$op]))
        {
            return $op;
        }

        return '';
    }

    /**
     * @param string        $fieldName
     * @param TableAbstract $table
     *
     * @return IField|null
     */
    protected function getFieldByName (string &$fieldName, TableAbstract &$table)
    {
        $fieldsCollection = $this->getParams()->getSelectFieldsCollection();
        if (!$fieldsCollection->isEmpty())
        {
            /**
             * @var string $fieldAlias
             * @var SelectField $field
             */
            foreach ($fieldsCollection as $fieldAlias => $field)
            {
                if ($fieldAlias == $fieldName)
                {
                    $table = $field->getTable();
                    $fieldName = $field->getFieldColumnName();
                    return $field->getField();
                }
                elseif ($field->getFieldColumnName() == $fieldName)
                {
                    $table = $field->getTable();
                    $fieldName = $field->getFieldColumnName();
                    return $field->getField();
                }
            }
        }
        $tableFieldsCollection = $table->getMap();
        if (!$tableFieldsCollection->isEmpty())
        {
            /**
             * @var string $name
             * @var IField $field
             */
            foreach ($tableFieldsCollection as $name => $field)
            {
                if ($fieldName == $name)
                {
                    return $field;
                }
                elseif ($fieldName == $field->getColumnName())
                {
                    return $field;
                }
            }
        }

        return null;
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    protected function clearFieldName (string $fieldName)
    {
        $fieldName = str_replace(array_keys(self::EXPRESSION_TRIPLE_CHAR),'', $fieldName);
        $fieldName = str_replace(array_keys(self::EXPRESSION_DOUBLE_CHAR),'', $fieldName);
        $fieldName = str_replace(array_keys(self::EXPRESSION_SINGLE_CHAR),'', $fieldName);

        return $fieldName;
    }
}
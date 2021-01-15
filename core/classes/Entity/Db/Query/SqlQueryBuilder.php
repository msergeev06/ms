<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\Params\Filter;
use Ms\Core\Entity\Db\Params\FilterCollection;
use Ms\Core\Entity\Db\Params\GetListParams;
use Ms\Core\Entity\Db\Params\JoinField;
use Ms\Core\Entity\Db\Params\Order;
use Ms\Core\Entity\Db\Params\SelectField;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;

/**
 * Класс Ms\Core\Entity\Db\Query\SqlQueryBuilder
 * Создает части SQL запросов
 */
class SqlQueryBuilder extends Multiton
{
    /**
     * Собирает SELECT часть запроса в БД
     *
     * @param GetListParams $params Объект параметров запроса
     *
     * @return string
     * @unittest
     */
    public function createSelect (GetListParams $params)
    {
        $helper = new SqlHelper();
        $sql = "SELECT ";
        $selectFieldsCollection = $params->getSelectFieldsCollection();
        $aliasTableCollection = $params->getTableAliasCollection();
        $sql .= "\n\t";

        if ($selectFieldsCollection->isEmpty())
        {
            $sql .= "*\n";
        }
        else
        {
            $bFirst = true;
            /** @var SelectField $selectField */
            foreach ($selectFieldsCollection as $selectField)
            {
                if ($bFirst)
                {
                    $bFirst = false;
                }
                else
                {
                    $sql .= ",\n\t";
                }
                $aliasTable = $aliasTableCollection->getAlias($selectField->getTable());
                if (is_null($aliasTable))
                {
                    continue;
                }
                $sql .= $helper->wrapQuotes($aliasTable) . '.'
                        . $helper->wrapQuotes($selectField->getFieldColumnName());
                if ($selectField->getFieldColumnName() != $selectField->getFieldAlias())
                {
                    $sql .= ' as ' . $helper->wrapQuotes($selectField->getFieldAlias());
                }
            }
        }
        $sql .= "\n";

        return $sql;
    }

    /**
     * Собирает FROM часть запроса в БД
     *
     * @param GetListParams $params Объект параметров запроса
     *
     * @return string
     * @unittest
     */
    public function createFrom (GetListParams $params)
    {
        $helper = new SqlHelper();
        $sql = "FROM\n\t"
            . $helper->wrapQuotes($params->getTable()->getTableName()) . ' '
            . $helper->wrapQuotes($params->getTableAliasCollection()->getAlias($params->getTable()))
            . "\n"
        ;

        return $sql;
    }

    /**
     * Собирает часть JOIN (если необходимо) запроса в БД
     *
     * @param GetListParams $params Объект параметров запроса
     *
     * @return string
     * @unittest
     */
    public function createSqlJoin (GetListParams $params)
    {
        $helper = new SqlHelper();
        $sql = "";
        $joinCollection = $params->getJoinFieldsCollection();
        if (!$joinCollection->isEmpty())
        {
            /**
             * @var string $alias
             * @var JoinField $joinField
             */
            foreach ($joinCollection as $alias => $joinField)
            {
                $sql .= "\tLEFT JOIN \n\t\t"
                    . $helper->wrapQuotes($joinField->getBaseTable()->getTableName()) . ' '
                    . $helper->wrapQuotes($joinField->getBaseTableAlias())
                    . "\n\tON\n\t\t"
                    . $helper->wrapQuotes($joinField->getBaseTableAlias()) . '.'
                    . $helper->wrapQuotes($joinField->getBaseFieldName()) . ' = '
                    . $helper->wrapQuotes($joinField->getRefTableAlias()) . '.'
                    . $helper->wrapQuotes($joinField->getRefFieldName()) . "\n"
                ;
            }
        }

        return $sql;
    }

    /**
     * Собирает часть WHERE запроса в БД
     *
     * @param GetListParams $params Объект параметров запроса
     *
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     * @unittest
     */
    public function createSqlWhere (GetListParams $params)
    {
        $sql = "WHERE\n";
        $filterCollection = $params->getFilterCollection();
        $sql .= $this->addSqlFromFilterCollection($filterCollection, "\t");
        $sql .= "\n";

        return $sql;
    }

    /**
     * Собирает часть ORDER BY запроса в БД
     *
     * @param GetListParams $params Объект параметров запроса
     *
     * @return string
     * @unittest
     */
    public function createSqlOrder (GetListParams $params)
    {
        $helper = new SqlHelper();
        $sql = "ORDER BY\n\t";
        $orderCollection = $params->getOrderCollection();
        if ($orderCollection->isEmpty())
        {
            return '';
        }
        /**
         * @var string $fieldName
         * @var Order $order
         */
        foreach ($orderCollection as $fieldName => $order)
        {
            $sql .= $helper->wrapQuotes($order->getTableAlias()) . '.'
                . $helper->wrapQuotes($order->getFieldName()) . ' '
                . $order->getDirection() . "\n\t"
            ;
        }
        $sql .= "\n";

        return $sql;
    }

    /**
     * Собирает часть LIMIT запроса в БД
     *
     * @param GetListParams $params Объект параметров запроса
     *
     * @return string
     * @unittest
     */
    public function createSqlLimit (GetListParams $params)
    {
        $sql = "";
        $limit = (int)$params->getLimit();
        $offset = (int)$params->getOffset();
        if ($limit > 0)
        {
            $sql .= "LIMIT ";
            if ($offset > 0)
            {
                $sql .= $offset;
            }
            else
            {
                $sql .= 0;
            }
            $sql .= ', ' . $limit;
            $sql .= "\n";
        }

        return $sql;
    }

    /**
     * Дополняет код SQL запроса из коллекции фильтра
     *
     * @param FilterCollection $collection Коллекция фильтра
     * @param string           $tabs       Начальный уровень табов (отступ)
     *
     * @return string
     * @throws ArgumentTypeException
     * @throws ArgumentNullException
     */
    protected function addSqlFromFilterCollection (FilterCollection $collection, string $tabs = "")
    {
        $sql = '';
        $logic = $collection->getLogic();

        $bFirst = true;
        /**
         * @var int $key
         * @var Filter|FilterCollection $filter
         */
        foreach ($collection as $key => $filter)
        {
            if ($bFirst)
            {
                $sql .= $tabs;
                $bFirst = false;
            }
            else
            {
                $sql .= $tabs;
                $sql .= $logic." ";
            }
            if ($filter instanceof FilterCollection)
            {
                $sql .= "(\n";
                $sql .= $this->addSqlFromFilterCollection($filter,$tabs."\t");
                $sql .= $tabs . ")\n";
            }
            else //$filter instanceof Filter
            {
                $sql .= $this->addFilterExpression ($filter) . "\n";
            }
        }

        return $sql;
    }

    /**
     * Добавляет выражение фильтра в SQL код
     *
     * @param Filter $filter Объект параметров фильтра
     *
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     */
    protected function addFilterExpression (Filter $filter)
    {
        $helper = new SqlHelper();
        $sql = '';
        $arMask = $this->getArMask($filter);

        $value = $filter->getValue();
        //Флаг того, что значением является другое поле таблицы
        $bValueField = false;
        //Если значение не является массивом и значением является другое поле таблицы
        if (!is_array($value) && strpos($value, 'FIELD_') !== false)
        {
            $bValueField = true;
            $value = str_replace('FIELD_', '', $value);
        }
        //Собираем sql запрос
        if ($arMask['op'] == 'NOT')
        {
            $sql .= 'NOT ';
        }
        $sql .= $helper->wrapQuotes($filter->getTableAlias()) . '.'
                . $helper->wrapQuotes($filter->getFieldName()) . " "
                . $arMask['op']
        ;
        //Если значением является другое поле этой таблицы, указываем поле
        if ($bValueField)
        {
            $sql .= " " . $helper->wrapQuotes($filter->getTableAlias()) . '.' . $helper->wrapQuotes($value);
        }
        //Если значение не равно NULL
        elseif (!is_null($value))
        {
            //Если значение является массивом
            if (is_array($value))
            {
                //Если используется функция BETWEEN или NOT BETWEEN
                if (strpos($arMask['op'], 'BETWEEN') !== false)
                {
                    //Если в массиве есть значения с индексами 0 и 1
                    if (isset($value[0]) && isset($value[1]))
                    {
                        $sql .= ' ' . $filter->getField()->getSqlValue($value[0]) . ' AND '
                                . $filter->getField()->getSqlValue($value[1]);
                    }
                    //Если нет значений с индексами 0 и 1, выбрасываем исключение
                    else
                    {
                        throw new ArgumentTypeException($filter->getFieldName(), 'array[0,1]');
                    }
                }
                //В противном случае, обрабатываем значения в массиве
                else
                {
                    $arVal = [];
                    foreach ($value as $val)
                    {
                        $arVal[] = $filter->getField()->getSqlValue($val);
                    }
                    $sql .= ' (' . implode(',', $arVal) . ')';
                }
            }
            //Обрабатываем значение, которое явялется SQL кодом
            elseif ($arMask['sql'])
            {
                $sql .= ' ' . $value;
            }
            //Обрабатываем все остальные значения
            else
            {
                $sql .= ' ' . $filter->getField()->getSqlValue($value);
            }
        }



        return $sql;
    }

    /**
     * Возвращает массив с параметрами маски
     *
     * @param Filter $filter Объект параметров фильтра
     *
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     */
    protected function getArMask (Filter $filter)
    {
        $field = $filter->getFieldName();
        if (is_null($filter->getValue()))
        {
            $isArrValue = null;
        }
        else
        {
            $isArrValue = is_array($filter->getValue());
        }
        $op = $filter->getExpression();
        if (array_key_exists($op,FilterCollection::EXPRESSION_TRIPLE_CHAR))
        {
            $arr = [
                "field"     => $field,
                "mask"      => $op,
                "operation" => FilterCollection::EXPRESSION_TRIPLE_CHAR[$op],
                'op'        => $op,
                'sql'       => false
            ];
            //"!><" => "NB",  //not between
            //"s!=" => "SNI",  //sql not Identical
            //"s!%" => "SNS",  //sql not substring
            if ($op == '!><')
            {
                if (!$isArrValue)
                {
                    throw new ArgumentTypeException($field, 'array');
                }
                $arr['op'] = 'NOT BETWEEN';
            }
            elseif ($op == 's!=')
            {
                if ($isArrValue)
                {
                    $arr['op'] = 'NOT IN';
                }
                elseif (!is_null($isArrValue))
                {
                    $arr['op'] = '!=';
                }
                else
                {
                    throw new ArgumentNullException($field);
                }
                $arr['sql'] = true;
            }
            elseif ($op == 's!%')
            {
                if ($isArrValue)
                {
                    throw new ArgumentTypeException($field, 'string');
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
                $arr['op'] = 'NOT LIKE';
                $arr['sql'] = true;
            }

            return $arr;
        }
        elseif (array_key_exists($op, FilterCollection::EXPRESSION_DOUBLE_CHAR))
        {
            $arr = [
                "field"     => $field,
                "mask"      => $op,
                "operation" => FilterCollection::EXPRESSION_DOUBLE_CHAR[$op],
                'op'        => $op,
                'sql'       => false
            ];

            //"!=" => "NI",   //not Identical
            //"!%" => "NS",   //not substring
            //"><" => "B",    //between
            //">=" => "GE",   //greater or equal
            //"<=" => "LE",   //less or equal
            //"s=" => "SE",   //sql equal
            //"s%" => "SS",   //sql LIKE
            //"s>" => "SG",   //sql greater
            //"s<" => "SL",   //sql less
            if ($op == '!=')
            {
                if ($isArrValue)
                {
                    $arr['op'] = 'NOT IN';
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
            }
            elseif ($op == '!%')
            {
                if ($isArrValue)
                {
                    throw new ArgumentTypeException($field, 'string');
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
                $arr['op'] = 'NOT LIKE';
            }
            elseif ($op == '><')
            {
                if (!$isArrValue)
                {
                    throw new ArgumentTypeException($field, 'array');
                }
                $arr['op'] = 'BETWEEN';
            }
            elseif ($op == '>=')
            {
                if ($isArrValue)
                {
                    throw new ArgumentTypeException($field, 'string|int|float');
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
            }
            elseif ($op == '<=')
            {
                if ($isArrValue)
                {
                    throw new ArgumentTypeException($field, 'string|int|float');
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
            }
            elseif ($op == 's=')
            {
                if ($isArrValue)
                {
                    $arr['op'] = 'IN';
                }
                elseif (!is_null($isArrValue))
                {
                    $arr['op'] = '=';
                }
                else
                {
                    throw new ArgumentNullException($field);
                }
                $arr['sql'] = true;
            }
            elseif ($op == 's%')
            {
                if ($isArrValue)
                {
                    throw new ArgumentTypeException($field, 'string');
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
                $arr['op'] = 'LIKE';
                $arr['sql'] = true;
            }
            elseif ($op == 's>')
            {
                if ($isArrValue)
                {
                    throw new ArgumentTypeException($field, 'string');
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
                $arr['op'] = '>';
                $arr['sql'] = true;
            }
            elseif ($op == 's<')
            {
                if ($isArrValue)
                {
                    throw new ArgumentTypeException($field, 'string');
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
                $arr['op'] = '<';
                $arr['sql'] = true;
            }

            return $arr;
        }
        elseif (array_key_exists($op, FilterCollection::EXPRESSION_SINGLE_CHAR))
        {
            $arr = [
                "field"     => $field,
                "mask"      => $op,
                "operation" => FilterCollection::EXPRESSION_SINGLE_CHAR[$op],
                'op'        => $op,
                'sql'       => false
            ];

            //"=" => "I",     //Identical
            //"%" => "S",     //substring
            //"?" => "?",     //logical
            //">" => "G",     //greater
            //"<" => "L",     //less
            //"!" => "N",     //not field LIKE val
            if ($op == '=')
            {
                if ($isArrValue)
                {
                    $arr['op'] = 'IN';
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
            }
            elseif ($op == '%')
            {
                if ($isArrValue)
                {
                    throw new ArgumentTypeException('$value', 'string');
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
                $arr['op'] = 'LIKE';
            }
            elseif ($op == '>')
            {
                if ($isArrValue)
                {
                    throw new ArgumentTypeException($field, 'string|int|float');
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
            }
            elseif ($op == '<')
            {
                if ($isArrValue)
                {
                    throw new ArgumentTypeException($field, 'string|int|float');
                }
                elseif (is_null($isArrValue))
                {
                    throw new ArgumentNullException($field);
                }
            }
            elseif ($op == '!')
            {
                if (is_null($isArrValue))
                {
                    $arr['op'] = 'IS NOT NULL';
                }
                elseif ($isArrValue)
                {
                    throw new ArgumentTypeException($field, 'NULL|string|int|float');
                }
                else
                {
                    $arr['op'] = 'NOT';
                }
            }

            return $arr;
        }

        $arr = [
            "field"     => $field,
            "mask"      => null,
            "operation" => null,
            'op'        => '=',
            'sql'       => false
        ];
        if (is_null($isArrValue))
        {
            $arr['op'] = 'IS NULL';
        }
        elseif ($isArrValue)
        {
            $arr['op'] = 'IN';
        }

        return $arr;
    }
}
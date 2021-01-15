<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Params;

use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Params\GetListParams
 * Параметры, переданные в метод getList класса ORMController
 */
class GetListParams
{
    /** @var ORMController */
    protected $controller = null;
    /** @var TableAbstract */
    protected $table = null;
    /** @var SelectFieldsCollection */
    protected $selectFieldsCollection = null;
    /** @var JoinFieldsCollection */
    protected $joinFieldsCollection = null;
    /** @var TableAliasCollection */
    protected $tableAliasCollection = null;
    /** @var FilterCollection */
    protected $filterCollection = null;
    /** @var OrderCollection */
    protected $orderCollection = null;
    /** @var int */
    protected $limit = 0;
    /** @var int */
    protected $offset = 0;

    /**
     * Конструктор класса GetListParams
     *
     * @param ORMController $controller
     */
    public function __construct (ORMController $controller)
    {
        $this->controller = $controller;
        $this->table = $controller->getTable();
        $this->selectFieldsCollection = new SelectFieldsCollection($this);
        $this->joinFieldsCollection = new JoinFieldsCollection($this);
        $this->tableAliasCollection = new TableAliasCollection();
        $this->tableAliasCollection->addAlias($this->table);
        $this->filterCollection = new FilterCollection($this);
        $this->orderCollection = new OrderCollection($this);
    }

    /**
     * Устанавливает LIMIT, если =0, без лимита
     *
     * @param int $limit
     *
     * @return $this
     * @unittest
     */
    public function setLimit (int $limit = 0)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    /**
     * Возвращает значение параметра LIMIT
     *
     * @return int
     * @unittest
     */
    public function getLimit ()
    {
        return $this->limit;
    }

    /**
     * Устанавливает смешение результатов OFFSET
     *
     * @param int $offset
     *
     * @return $this
     * @unittest
     */
    public function setOffset (int $offset = 0)
    {
        $this->offset = (int)$offset;

        return $this;
    }

    /**
     * Возвращает значение параметра OFFSET
     *
     * @return int
     * @unittest
     */
    public function getOffset ()
    {
        return $this->offset;
    }

    /**
     * Возвращает ссылку на объект ORMController
     *
     * @return ORMController
     * @unittest
     */
    public function getORMController ()
    {
        return $this->controller;
    }

    /**
     * Возвращает ссылку на объект базовой таблицы
     *
     * @return TableAbstract
     * @unittest
     */
    public function getTable ()
    {
        return $this->table;
    }

    /**
     * Возвращает ссылку на объект коллекции полей
     *
     * @return SelectFieldsCollection
     * @unittest
     */
    public function getSelectFieldsCollection ()
    {
        return $this->selectFieldsCollection;
    }

    /**
     * Возвращает ссылку на объект с коллекцией алиасов таблиц
     *
     * @return TableAliasCollection
     * @unittest
     */
    public function getTableAliasCollection ()
    {
        return $this->tableAliasCollection;
    }

    /**
     * Возвращает ссылку на объект с коллекцией LEFT JOIN
     *
     * @return JoinFieldsCollection
     * @unittest
     */
    public function getJoinFieldsCollection ()
    {
        return $this->joinFieldsCollection;
    }

    /**
     * Возвращает объект коллекции фильтра
     *
     * @return FilterCollection
     * @unittest
     */
    public function getFilterCollection ()
    {
        return $this->filterCollection;
    }

    /**
     * Возвращает объект коллекции сортировки
     *
     * @return OrderCollection
     * @unittest
     */
    public function getOrderCollection ()
    {
        return $this->orderCollection;
    }

    /**
     * Устанавливает фильтр из полученного массива
     *
     * @param array $arFilter Массив фильтра
     *
     * @return $this
     * @unittest
     */
    public function setFilterFromArray (array $arFilter)
    {
        if (empty($arFilter))
        {
            return $this;
        }
        $this->getFilterCollection()->setFromArray ($arFilter);

        return $this;
    }

    /**
     * Устанавливает правила сортировки из переданного массива
     *
     * @param array $arOrder Массив правил сортировки
     *
     * @return $this
     * @unittest
     */
    public function setOrderFromArray (array $arOrder)
    {
        if (empty($arOrder))
        {
            return $this;
        }
        $this->getOrderCollection()->setFromArray($arOrder);

        return $this;
    }

    /**
     * Обрабатывает массив и собирает список полей, получаемых при запросе
     *
     * @param array $arSelect Массив настроек выбираемых полей
     *
     * @return $this
     * @throws \Ms\Core\Exceptions\SystemException
     * @unittest
     */
    public function parseGetListSelect (array $arSelect)
    {
        if (empty($arSelect))
        {
            return $this;
        }

        foreach ($arSelect as $key => $value)
        {
            if (is_numeric($key))
            {
                if ($value == '*')
                {
                    $this->addAllFields ($this->getTable());
                }
                elseif (strpos($value,'.') !== false)
                {
                    list($baseFieldName, $joinFieldName) = explode('.',$value,2);
                    $alias = $baseFieldName . '_' . $joinFieldName;
                    $this->addJoinField($alias, $joinFieldName, $baseFieldName, $this->getTable());
                }
                else
                {
                    $this->getSelectFieldsCollection()->addField($value, $value, $this->getTable());
                }
            }
            else
            {
                if (strpos($key,'.') !== false)
                {
                    list($baseFieldName, $joinFieldName) = explode('.',$key,2);
                    $this->addJoinField($value, $joinFieldName, $baseFieldName, $this->getTable());
                }
                else
                {
                    $this->getSelectFieldsCollection()->addField($key, $value, $this->getTable());
                }
            }
        }

        return $this;
    }

    protected function addAllFields (TableAbstract $table, string $fieldPrefix = null)
    {
        if (is_null($fieldPrefix))
        {
            $fieldPrefix = '';
        }
        else
        {
            $fieldPrefix .= '_';
        }
        $this->getTableAliasCollection()->addAlias($table);
        if ($table->getMap()->isEmpty())
        {
            return $this;
        }
        /**
         * @var string $fieldName
         * @var IField $field
         */
        foreach ($table->getMap() as $fieldName => $field)
        {
            $this->getSelectFieldsCollection()->addField(
                $field->getColumnName(),
                $fieldPrefix . $field->getColumnName(),
                $table
            );
        }

        return $this;
    }

    protected function addJoinField (string $alias, string $joinFieldName, string $baseFieldName, TableAbstract $baseTable)
    {
        $link = $this->getLinkByFieldName($baseTable, $baseFieldName);
        if (is_null($link))
        {
            return;
        }
        $linkFieldName = $link->getFieldName();
        $linkTable = $link->getTable();
        $this->getTableAliasCollection()->addAlias($linkTable);
        if (!$this->getJoinFieldsCollection()->isExists($linkTable))
        {
            $this->getJoinFieldsCollection()->addJoin(
                $linkFieldName,
                $linkTable,
                $baseFieldName,
                $baseTable
            );
        }
        if ($linkFieldName == $joinFieldName)
        {
            $this->getSelectFieldsCollection()->addField(
                $joinFieldName,
                $alias,
                $linkTable
            );
        }
        else
        {
            if ($joinFieldName == '*')
            {
                return $this->addAllFields($linkTable, $baseFieldName);
            }

            $this->getSelectFieldsCollection()->addField(
                $linkFieldName,
                $baseFieldName . '_' . $linkFieldName,
                $linkTable
            );
            $this->getSelectFieldsCollection()->addField(
                $joinFieldName,
                $alias,
                $linkTable
            );
        }

    }

    protected function getLinkByFieldName (TableAbstract $table, string $baseFieldName)
    {
        $field = $this->getFieldFromTable($baseFieldName, $table);
        if (is_null($field))
        {
            return null;
        }

        return $field->getLink();
    }

    protected function getFieldFromTable (string $fieldName, TableAbstract $table)
    {
        if ($table->getMap()->isEmpty())
        {
            return null;
        }
        /**
         * @var string $name
         * @var IField $field
         */
        foreach ($table->getMap() as $name => $field)
        {
            if ($field->getColumnName() == $fieldName)
            {
                return $field;
            }
        }

        return null;
    }
}
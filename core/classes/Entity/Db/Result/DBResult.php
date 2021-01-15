<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2017 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Result;

use Ms\Core\Entity\Db\Fields\ScalarFieldAbstract;
use Ms\Core\Entity\Db\Params\GetListParams;
use Ms\Core\Entity\Db\Query;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Interfaces\Db\IField;
use Ms\Core\Interfaces\ISqlDriver;

/**
 * Класс Ms\Core\Entity\Db\Result\DBResult
 * Осуществляет обработку результата запроса к базе данных
 */
class DBResult
{
    /**
     * Массив сущностей полей таблицы
     *
     * @var array
     */
    protected $arFieldsEntity;
    /**
     * Драйвер подключения к БД
     *
     * @var ISqlDriver
     */
    protected $driver = null;
    /** @var GetListParams|null */
    protected $getListParams = null;
    /**
     * Массив последнего разобранного результата mysql запроса
     *
     * @var array
     */
    protected $last_res;
    /**
     * Массив последнего разобранного и обработанного mysql запроса
     *
     * @var array
     */
    protected $last_result;
    /**
     * @var Query\QueryBase
     */
    protected $obQuery = null;
    /**
     * Результат mysql запроса
     *
     * @var resource
     */
    protected $result;
    /**
     * Текст SQL запроса
     *
     * @var string
     */
    protected $sql = '';
    /**
     * @var bool <Описание>
     */
    protected $success = false;

    /** @var null|int */
    protected $insertID = null;

    /**
     * Создает объект при получении результата mysql запроса
     *
     * @param ISqlDriver      $driver  Драйвер подключения к БД
     * @param Query\QueryBase $obQuery Объект Query, содержащий mysql запрос
     */
    public function __construct (ISqlDriver $driver = null, Query\QueryBase $obQuery = null)
    {
        if (!is_null($driver))
        {
            $this->driver = $driver;
            $this->sql = (string)$this->getDriver()->getSql();
            $this->success = $this->getDriver()->isSuccess();
        }
        if (!is_null($obQuery))
        {
            $this->setObQuery($obQuery);
            if ($obQuery instanceof Query\QuerySelect)
            {
                $this->getListParams = $obQuery->getGetListParams();
            }
        }
    }

    /**
     * Разбирает результат mysql запроса и возвращает массив обработанных значений
     *
     * @return array Массив обработанных значений
     * @unittest
     */
    public function fetch ()
    {
        if (($this->obQuery instanceof Query\QuerySelect) || ($this->obQuery instanceof Query\QueryBase))
        {
            $ar_res = $this->getDriver()->fetchArray();
            $this->last_res = $ar_res;
            $arResult = $arLast = [];
            if (is_array($ar_res))
            {
                foreach ($ar_res as $k => $v)
                {
                    if (!is_numeric($k))
                    {
                        $arResult[$k] = $arLast['~' . $k] = $v;
                        $arResult[$k] = $this->getFetchValue($k, $v);
                    }
                }
                $arResult = array_merge($arResult, $arLast);
            }
            else
            {
                $arResult = $ar_res;
            }
        }
        else
        {
            $arResult = $this->result;
        }
        $this->last_result = $arResult;

        return $arResult;
    }

    /**
     * Возвращает количество затронутых строк, при "select" запросе
     *
     * @return null|number
     * @unittest
     */
    public function getAffectedRows ()
    {
        if (!is_null($this->driver))
        {
            return $this->getDriver()->getAffectedRows();
        }
        else
        {
            return null;
        }
    }

    /**
     * Возвращает драйвер подключения к БД
     *
     * @return ISqlDriver
     * @unittest
     */
    public function getDriver ()
    {
        return $this->driver;
    }

    /**
     * Устанавливает ID добавленного элемента для текущего результата
     *
     * @param null|int $insertID ID добавленного элемента
     *
     * @return $this
     */
    public function setInsertID (int $insertID = null)
    {
        if (
            (!is_null($insertID) && (int)$insertID > 0)
            || is_null($insertID)
        ) {
            $this->insertID = $insertID;
        }

        return $this;
    }

    /**
     * Возвращает ID добавленной записи при "insert" запросе
     *
     * @return null|int
     * @unittest
     */
    public function getInsertId ()
    {
        if (is_null($this->insertID))
        {
            if (!is_null($this->driver))
            {
                return $this->getDriver()->getInsertId();
            }
            else
            {
                return null;
            }
        }
        else
        {
            return $this->insertID;
        }
    }

    /**
     * Возвращает массив последнего разобранного результата mysql запроса
     *
     * @return array
     * @unittest
     */
    public function getLastRes ()
    {
        return $this->last_res;
    }

    /**
     * Возвращает массив последнего разобранного и обработанного mysql запроса
     *
     * @return array
     * @unittest
     */
    public function getLastResult ()
    {
        return $this->last_result;
    }

    /**
     * Возвращает количество полей в результате
     *
     * @return null|int
     * @unittest
     */
    public function getNumFields ()
    {
        if (!is_null($this->driver))
        {
            return $this->getDriver()->getNumFields();
        }
        else
        {
            return null;
        }
    }

    /**
     * Возвращает количество строк в результате
     *
     * @return null|int
     * @unittest
     */
    public function getNumRows ()
    {
        if (!is_null($this->driver))
        {
            return $this->getDriver()->getNumRows();
        }
        else
        {
            return null;
        }
    }

    /**
     * Возвращает объект запроса
     *
     * @return Query\QueryBase
     * @unittest
     */
    public function getObQuery (): Query\QueryBase
    {
        return $this->obQuery;
    }

    /**
     * Возвращает объект результата запроса
     *
     * @return null|mixed
     * @unittest
     */
    public function getResult ()
    {
        if (!is_null($this->driver))
        {
            return $this->getDriver()->getResult();
        }
        else
        {
            return null;
        }
    }

    /**
     * Возвращает номер ошибки mysql запроса
     *
     * @return null|number
     * @unittest
     */
    public function getResultErrorNumber ()
    {
        if (!is_null($this->driver))
        {
            return $this->getDriver()->getErrorNo();
        }
        else
        {
            return null;
        }
    }

    /**
     * Возвращает текст ошибки mysql запроса
     *
     * @return null|string
     * @unittest
     */
    public function getResultErrorText ()
    {
        if (!is_null($this->driver))
        {
            return $this->getDriver()->getError();
        }
        else
        {
            return null;
        }
    }

    /**
     * Возвращает текст SQL запроса
     *
     * @return string Текст SQL запроса
     * @unittest
     */
    public function getSql ()
    {
        return $this->sql;
    }

    /**
     * Возвращает TRUE, если при запросе не было ошибок
     *
     * @return bool
     * @unittest
     */
    public function isSuccess ()
    {
        return $this->success;
    }

    /**
     * Возвращает объект таблицы запроса, если она была задана, иначе возвращает NULL
     *
     * @return TableAbstract|null
     * @unittest
     */
    public function getTable ()
    {
        return $this->obQuery->getTable();
    }

    /**
     * Устанавливает объект запроса в БД
     *
     * @param Query\QueryBase $obQuery Объект запроса в БД
     *
     * @return $this
     */
    protected function setObQuery (Query\QueryBase $obQuery)
    {
        $this->obQuery = $obQuery;

        return $this;
    }

    /**
     * Возвращает обработанное значение из базы
     *
     * @param string $fieldName  Имя полученного поля
     * @param mixed  $fieldValue Значение полученного поля
     *
     * @return array|mixed
     */
    private function getFetchValue ($fieldName, $fieldValue)
    {
        $field = null;
        $obQuery = $this->getObQuery();
        if (($obQuery instanceof Query\QuerySelect))
        {
            $selectField = $obQuery->getGetListParams()->getSelectFieldsCollection()->getField($fieldName);
            if (!is_null($selectField))
            {
                $field = $selectField->getField();
            }
            elseif ($obQuery->getGetListParams()->getTable()->getMap()->isExists($fieldName))
            {
                $field = $obQuery->getGetListParams()->getTable()->getMap()->getField($fieldName);
            }
        }
        if (is_null($field) && !is_null($this->getTable()) && $this->getTable()->getMap()->isExists($fieldName))
        {
            $field = $this->getTable()->getMap()->getField($fieldName);
        }

        if (!is_null($field) && $field instanceof IField)
        {
            $fieldValue = $field->fetchDataModification($fieldValue);
        }

        return $fieldValue;
    }
}
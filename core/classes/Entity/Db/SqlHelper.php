<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db;

/**
 * Класс Ms\Core\Entity\Db\SqlHelper
 * Помощник обработки SQL запросов
 */
class SqlHelper
{
    /**
     * Константа, содержащая кавычку, используемую в SQL запросах
     */
    const QUOTES = '`';

    /**
     * Имя таблицы
     *
     * @var string
     */
    protected $tableName = '';

    /**
     * Создает объект SQL помощника
     *
     * @param string $tableName Имя таблицы
     */
    public function __construct ($tableName = '')
    {
        $this->tableName = $tableName;
    }

    /**
     * Возвращает строковое представление SQL функции AVG
     *
     * @param string $column    Имя поля
     * @param null   $newColumn Алиас значения
     *
     * @return string
     * @unittest
     */
    public function getAvgFunction ($column = "", $newColumn = null)
    {
        if ($column == "")
        {
            return "";
        }

        if (is_null($newColumn))
        {
            $newColumn = 'AVG_' . $column;
        }
        $column = $this->wrapFieldQuotes($column);

        return 'AVG(' . $column . ') ' . $this->wrapQuotes($newColumn);
    }

    /**
     * Возвращает строковое представление SQL функции COUNT
     *
     * @param string $params    Поле или *
     * @param null   $newColumn Алиас значения
     *
     * @return string
     * @unittest
     */
    public function getCountFunction ($params = "*", $newColumn = null)
    {
        if (is_null($newColumn))
        {
            if ($params == "*")
            {
                $newColumn = 'COUNT';
            }
            else
            {
                $newColumn = 'COUNT_' . $params;
            }
        }
        if ($params != '*' && $this->tableName != '')
        {
            $params = $this->wrapFieldQuotes($params);
        }

        return 'COUNT(' . $params . ') ' . $this->wrapQuotes($newColumn);
    }

    /**
     * Возвращает строковое представление SQL функции MAX
     *
     * @param string $column    Имя поля
     * @param string $newColumn Алиас значения
     *
     * @return string
     * @unittest
     */
    public function getMaxFunction ($column = "", $newColumn = null)
    {
        if ($column == "")
        {
            return "";
        }

        if (is_null($newColumn))
        {
            $newColumn = 'MAX_' . $column;
        }
        $column = $this->wrapFieldQuotes($column);

        return 'MAX(' . $column . ') ' . $this->wrapQuotes($newColumn);
    }

    /**
     * Возвращает строковое представление SQL функции MIN
     *
     * @param string $column    Имя поля
     * @param string $newColumn Алиас значения
     *
     * @return string
     * @unittest
     */
    public function getMinFunction ($column = "", $newColumn = null)
    {
        if ($column == "")
        {
            return "";
        }

        if (is_null($newColumn))
        {
            $newColumn = 'MIN_' . $column;
        }
        $column = $this->wrapFieldQuotes($column);

        return 'MIN(' . $column . ') ' . $this->wrapQuotes($newColumn);
    }

    /**
     * Функция возвращает одиночную кавычку
     *
     * @return string
     * @unittest
     */
    public function getQuote ()
    {
        return self::QUOTES;
    }

    /**
     * Возвращает строковое представление SQL функции SUM
     *
     * @param string $column    Имя поля
     * @param string $newColumn Алиас значения
     *
     * @return string
     * @unittest
     */
    public function getSumFunction ($column = "", $newColumn = null)
    {
        if ($column == "")
        {
            return "";
        }

        if (is_null($newColumn))
        {
            $newColumn = 'SUM_' . $column;
        }
        $column = $this->wrapFieldQuotes($column);

        return 'SUM(' . $column . ') ' . $this->wrapQuotes($newColumn);
    }

    /**
     * Возвращает название таблицы, для которой создавался объект
     *
     * @return string
     * @unittest
     */
    public function getTableName ()
    {
        return $this->tableName;
    }

    /**
     * Возвращает поле обернутое кавычками.
     * Если имя таблицы не пустое, имя таблицы оборачивается кавычками и добавляется к полю.
     *
     * @param string $field Поле, которое необходимо обернуть кавычками
     *
     * @return string
     * @unittest
     */
    public function wrapFieldQuotes ($field)
    {
        $return = '';
        if ($this->tableName != '')
        {
            $return .= self::QUOTES . $this->tableName . self::QUOTES . '.';
        }
        $return .= self::QUOTES . $field . self::QUOTES;

        return $return;
    }

    /**
     * Возвращает оборнутое кавычками значение str
     *
     * @param string $str Строка, которую нужно обернуть кавычками
     *
     * @return string
     * @unittest
     */
    public function wrapQuotes ($str)
    {
        return self::QUOTES . $str . self::QUOTES;
    }

    /**
     * Возвращает обернутое кавычками значение tableName переданное в параметре,
     * либо взятое из свойства объекта
     *
     * @param string $tableName Имя таблицы
     *
     * @return string
     * @unittest
     */
    public function wrapTableQuotes ($tableName = '')
    {
        if ($tableName == '')
        {
            if ($this->tableName != '')
            {
                $tableName = $this->tableName;
            }
            else
            {
                return '';
            }
        }

        return self::QUOTES . $tableName . self::QUOTES;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Tables\TableAbstract;

/**
 * Класс Ms\Core\Entity\Db\Query\QueryDrop
 * Сущность DROP запроса к базе данных
 */
class QueryDrop extends QueryBase
{
    /** @var bool */
    protected $bIgnoreForeignKeys = false;

    /**
     * Конструктор
     *
     * @param TableAbstract $table              Объект таблицы
     * @param bool          $bIgnoreForeignKeys Флаг, означающий необходимость игнорировать ограничения внешнего ключа
     */
    public function __construct (TableAbstract $table, bool $bIgnoreForeignKeys = false)
    {
        $this->setTable($table);
        $this->setBIgnoreForeignKeys($bIgnoreForeignKeys);

        $this->setSql($this->buildQuery());
    }

    /**
     * Возвращает значение флага, означающего необходимость игнорировать ограничения внешнего ключа
     *
     * @return bool
     * @unittest
     */
    public function isIgnoreForeignKeys (): bool
    {
        return $this->bIgnoreForeignKeys;
    }

    /**
     * Устанавливает флаг, означающий необходимость игнорировать ограничения внешнего ключа
     *
     * @param bool $bIgnoreForeignKeys
     *
     * @return $this
     * @unittest
     */
    public function setBIgnoreForeignKeys (bool $bIgnoreForeignKeys = false)
    {
        $this->bIgnoreForeignKeys = (bool)$bIgnoreForeignKeys;

        return $this;
    }

    protected function buildQuery ()
    {
        /** @var TableAbstract $tableClass */
        $helper = new SqlHelper($this->getTable()->getTableName());

        $sql = '';
        if ($this->isIgnoreForeignKeys())
        {
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        }
        $sql .= "DROP TABLE IF EXISTS " . $helper->wrapTableQuotes() . ';';
        if ($this->isIgnoreForeignKeys())
        {
            $sql .= "\nSET FOREIGN_KEY_CHECKS=1;";
        }

        return $sql;
    }
}
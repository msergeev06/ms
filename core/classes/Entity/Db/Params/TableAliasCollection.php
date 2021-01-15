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

/**
 * Класс Ms\Core\Entity\Db\Params\TableAliasCollection
 * Коллекция алиасов таблиц getList
 */
class TableAliasCollection extends Dictionary
{
    /**
     * Добавляет алиас в таблицу
     *
     * @param TableAbstract $table
     *
     * @return $this
     * @unittest
     */
    public function addAlias (TableAbstract $table)
    {
        if (!$this->isAliasExists($table))
        {
            $alias = $this->generateTableAlias($table);
        }
        else
        {
            $alias = $this->getAlias($table);
        }
        $this->offsetSet($table->getTableName(),$alias);

        return $this;
    }

    /**
     * Возвращает TRUE, если в коллекции есть алиас для указанной таблицы, иначе возвращает FALSE
     *
     * @param TableAbstract $table Объект с описанием таблицы
     *
     * @return bool
     * @unittest
     */
    public function isAliasExists (TableAbstract $table)
    {
        return $this->isAliasExistsByTableName($table->getTableName());
    }

    /**
     * Возвращает TRUE, если в коллекции есть алиас для указанной таблицы, иначе возвращает FALSE
     *
     * @param string $tableName Имя таблицы
     *
     * @return bool
     * @unittest
     */
    public function isAliasExistsByTableName (string $tableName)
    {
        return $this->offsetExists($tableName);
    }

    /**
     * Возвращает алиас таблицы, если он существует в коллекции, либо NULL
     *
     * @param TableAbstract $table Объект таблицы
     *
     * @return string|null
     * @unittest
     */
    public function getAlias (TableAbstract $table)
    {
        return $this->getAliasByTableName($table->getTableName());
    }

    /**
     * Возвращает алиас таблицы, если он существует в коллекции, либо NULL
     *
     * @param string $tableName Название таблицы
     *
     * @return string|null
     * @unittest
     */
    public function getAliasByTableName (string $tableName)
    {
        if ($this->offsetExists($tableName))
        {
            return $this->offsetGet($tableName);
        }

        return null;
    }

    /**
     * Генерирует алиас для таблицы по первым буквам слов, при совпадении алиасов разных таблиц,
     * добавляет порядковый номер, начиная с 2
     *
     * @param TableAbstract $table
     *
     * @return string
     */
    protected function generateTableAlias (TableAbstract $table)
    {
        $alias = '';
        $arName = explode('_',strtolower($table->getTableName()));
        foreach ($arName as $name)
        {
            $alias .= mb_substr($name,0,1);
        }
        if ($this->offsetExists($alias))
        {
            if ($this->offsetGet($alias) != $table)
            {
                $i = 2;
                while ($this->offsetExists($alias . $i) && $this->offsetGet($alias . $i) != $table)
                {
                    $i++;
                }

                $alias .= $i;

                return $alias;
            }
        }

        return $alias;
    }

}
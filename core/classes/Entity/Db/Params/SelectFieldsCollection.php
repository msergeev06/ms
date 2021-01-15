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
 * Класс Ms\Core\Entity\Db\Params\SelectFieldsCollection
 * Коллекция полей
 */
class SelectFieldsCollection extends Dictionary
{
    /** @var GetListParams */
    protected $objParams = null;

    /**
     * Конструктор класса SelectFieldsCollection
     *
     * @param GetListParams $params
     */
    public function __construct (GetListParams $params)
    {
        parent::__construct(null);
        $this->objParams = $params;
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
     * Добавляет поле в коллекцию
     *
     * @param string             $fieldColumnName Имя поля в БД
     * @param string             $fieldAlias      Алиас поля
     * @param TableAbstract|null $table           Объект с описанием таблицы
     *
     * @return $this
     * @throws \Ms\Core\Exceptions\SystemException
     * @unittest
     */
    public function addField (string $fieldColumnName, string $fieldAlias, TableAbstract $table = null)
    {
        if ($this->getParams()->getTableAliasCollection()->isAliasExists($table))
        {
            $tableAlias = $this->getParams()->getTableAliasCollection()->getAlias($table);
        }
        else
        {
            $this->getParams()->getTableAliasCollection()->addAlias($table);
            $tableAlias = $this->getParams()->getTableAliasCollection()->getAlias($table);
        }

        $this->offsetSet($fieldAlias, new SelectField($table,$tableAlias,$fieldColumnName,$fieldAlias));

        return $this;
    }

    /**
     * Возвращает объект поля, если он существует в коллекции, либо NULL
     *
     * @param string $fieldAlias Алиас поля
     *
     * @return SelectField|null
     * @unittest
     */
    public function getField (string $fieldAlias)
    {
        if ($this->offsetExists($fieldAlias))
        {
            return $this->offsetGet($fieldAlias);
        }
        elseif (!$this->isEmpty())
        {
            /**
             * @var string $alias
             * @var SelectField $selectField
             */
            foreach ($this->values as $alias => $selectField)
            {
                if ($fieldAlias == $alias)
                {
                    return $selectField;
                }
                elseif ($selectField->getFieldAlias() == $fieldAlias)
                {
                    return $selectField;
                }
                elseif ($selectField->getFieldColumnName() == $fieldAlias)
                {
                    return $selectField;
                }
            }
        }

        return null;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Params;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Db\Params\OrderCollection
 * Коллекция настроек сортировки выборки
 */
class OrderCollection extends Dictionary
{
    const DIRECTION_ASC = 'ASC';
    const DIRECTION_DESC = 'DESC';

    /** @var GetListParams */
    protected $objParams = null;

    /**
     * Конструктор класса OrderCollection
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
     * Устанавливает параметры сортировки из полученного массива
     *
     * @param array $arOrder Массив с настройками сортировки
     *
     * @return $this
     * @unittest
     */
    public function setFromArray (array $arOrder)
    {
        if (empty($arOrder))
        {
            return $this;
        }
        foreach ($arOrder as $fieldName => $direction)
        {
            $direction = strtoupper($direction);
            $this->addOrder($fieldName, $direction);
        }

        return $this;
    }

    /**
     * Добавляет правило сортировки
     *
     * @param string $fieldAlias Алиас или имя поля
     * @param string $direction  Направление сортировки
     *
     * @return $this
     * @unittest
     */
    public function addOrder (string $fieldAlias, string $direction = self::DIRECTION_ASC)
    {
        $order = null;
        $selectField = $this->objParams->getSelectFieldsCollection()->getField($fieldAlias);
        if (!is_null($selectField))
        {
            $order = new Order(
                $selectField->getTable(),
                $this->getParams()->getTableAliasCollection()->getAlias($selectField->getTable()),
                $selectField->getFieldColumnName(),
                $selectField->getField(),
                $direction
            );
        }
        else
        {
            $field = $this->getParams()->getTable()->getMap()->getField($fieldAlias);
            if (!is_null($field))
            {
                $order = new Order(
                    $this->getParams()->getTable(),
                    $this->getParams()->getTableAliasCollection()->getAlias($this->getParams()->getTable()),
                    $field->getColumnName(),
                    $field,
                    $direction
                );
            }
        }
        if (!is_null($order))
        {
            $this->offsetSet($order->getFieldName(),$order);
        }

        return $this;
    }

    /**
     * Возвращает TRUE, если в коллекции существует правило для заданного поля, иначе возвращает FALSE
     *
     * @param string $fieldName Имя поля
     *
     * @return bool
     * @unittest
     */
    public function isExists (string $fieldName)
    {
        return $this->offsetExists($fieldName);
    }

    /**
     * Возвращает объект, описывающий правило сортировки, либо NULL
     *
     * @param string $fieldName Имя поля
     *
     * @return mixed|null
     * @unittest
     */
    public function getOrder (string $fieldName)
    {
        if ($this->isExists($fieldName))
        {
            return $this->offsetGet($fieldName);
        }

        return null;
    }
}
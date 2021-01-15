<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Type;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Type\AssociativeCollection
 * Коллекция объектов типа Associative
 */
class AssociativeCollection extends Dictionary
{
    /**
     * Добавляет объект в коллекцию
     *
     * @param Associative $associative Объект типа Associative
     *
     * @return $this
     */
    public function addData (Associative $associative)
    {
        $this->values[$associative->getName()] = $associative;

        return $this;
    }

    /**
     * Возвращает объект по его имени
     *
     * @param string $name Имя объекта
     *
     * @return Associative|null
     */
    public function getData (string $name)
    {
        if (!$this->offsetExists($name))
        {
            return null;
        }

        return $this->offsetGet($name);
    }

    /**
     * Возвращает значения в виде массива
     *
     * @return array
     */
    public function toArray ()
    {
        $arReturn = [];
        if (!$this->isEmpty())
        {
            /** @var Associative $assoc */
            foreach ($this->values as $assoc)
            {
                $arReturn[] = [
                    'NAME' => $assoc->getName(),
                    'VALUE' => $assoc->getValue()
                ];
            }
        }

        return $arReturn;
    }

    /**
     * Возвращает массив ключей значений
     *
     * @return array
     */
    public function getKeysArray ()
    {
        $arReturn = [];
        $arData = $this->toArray();
        if (!empty($arData))
        {
            foreach ($arData as $data)
            {
                $arReturn[] = $data['NAME'];
            }
        }

        return $arReturn;
    }

    /**
     * Возвращает массив значений значений
     *
     * @return array
     */
    public function getValuesArray ()
    {
        $arReturn = [];
        $arData = $this->toArray();
        if (!empty($arData))
        {
            foreach ($arData as $data)
            {
                $arReturn[] = $data['VALUE'];
            }
        }

        return $arReturn;
    }
}
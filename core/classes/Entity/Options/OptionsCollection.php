<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Options;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Options\OptionsCollection
 * Коллекция известных в данной сессии значений опций
 */
class OptionsCollection extends Dictionary
{
    /**
     * Добавляет/обновляет опцию в коллекцию
     *
     * @param Option $option Объект опции
     *
     * @return $this
     * @unittest
     */
    public function setOption (Option $option)
    {
        $this->offsetSet($option->getOptionFullName(), $option);

        return $this;
    }

    /**
     * Добавляет/обновляет опцию в коллекцию
     *
     * @param string $moduleName  Имя модуля, установившего опцию
     * @param string $optionName  Имя опции
     * @param null   $optionValue Значение опции
     *
     * @return $this
     * @unittest
     */
    public function setOptionValue (string $moduleName, string $optionName, $optionValue = null)
    {
        $option = new Option($moduleName, $optionName, $optionValue);

        return $this->setOption($option);
    }

    /**
     * Возвращает объект опции по ее полному имени вида БРЕНД_ИМЯМОДУЛЯ_ИМЯ_ОПЦИИ, либо NULL
     *
     * @param string $optionFullName Полное имя опции
     *
     * @return Option|null
     * @unittest
     */
    public function getOptionByFullName (string $optionFullName)
    {
        $optionFullName = strtolower($optionFullName);
        if (!$this->offsetExists($optionFullName))
        {
            return null;
        }

        return $this->offsetGet($optionFullName);
    }

    /**
     * Возвращает объект опции по имени модуля и имени опции, либо NULL
     *
     * @param string $moduleName Имя модуля, установившего опцию
     * @param string $optionName Имя опции
     *
     * @return Option|null
     * @unittest
     */
    public function getOption (string $moduleName, string $optionName)
    {
        $fullName = Options::getInstance()->getOptionFullName($moduleName, $optionName);

        return $this->getOptionByFullName($fullName);
    }

    /**
     * Удаляет опцию из коллекции
     *
     * @param string $moduleName Имя модуля, который установил опцию
     * @param string $optionName Имя опции
     *
     * @return $this
     * @unittest
     */
    public function unsetOption (string $moduleName, string $optionName)
    {
        $fullName = Options::getInstance()->getOptionFullName($moduleName, $optionName);
        if (!$this->offsetExists($fullName))
        {
            return $this;
        }

        $this->offsetUnset($fullName);

        return $this;
    }
}
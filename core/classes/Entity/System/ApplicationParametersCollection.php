<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

/**
 * Класс Ms\Core\Entity\System\ApplicationParametersCollection
 * Коллекция параметров приложения
 */
class ApplicationParametersCollection extends Dictionary
{
    /**
     * Добавляет параметр и его значение в коллекцию
     *
     * @param string $sParameterName Имя параметра
     * @param mixed  $value Значение параметра
     *
     * @return $this
     */
    public function addParameter (string $sParameterName, $value)
    {
        $this->offsetSet($sParameterName, $value);

        return $this;
    }

    /**
     * Возвращает значение параметра, либо NULL
     *
     * @param string $sParameterName Имя параметра
     *
     * @return mixed|null
     */
    public function getParameter (string $sParameterName)
    {
        if ($this->issetParameter($sParameterName))
        {
            return $this->offsetGet($sParameterName);
        }

        return null;
    }

    /**
     * Возвращает TRUE, если в коллекции существует параметр с указанным именем, иначе возвращает FALSE
     *
     * @param string $sParameterName имя параметра
     *
     * @return bool
     */
    public function issetParameter ($sParameterName)
    {
        return $this->offsetExists($sParameterName);
    }

    /**
     * Удаляет указанный параметр из коллекции
     *
     * @param string $sParameterName Имя параметра
     *
     * @return $this
     */
    public function unsetParameter ($sParameterName)
    {
        $this->offsetUnset($sParameterName);

        return $this;
    }

    /**
     * Очищает коллекцию параметров
     *
     * @return $this
     */
    public function clearAllParameters ()
    {
        $this->clear();

        return $this;
    }
}
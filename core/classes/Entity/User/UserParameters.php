<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\User;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\User\UserParameters
 * Параметры пользователя
 */
class UserParameters extends Dictionary
{
    /**
     * Добавляет параметр пользователя.
     *
     * @param string $parameterName  Имя параметра. Будет преобразовано к нижнему регистру
     * @param mixed  $parameterValue Произвольное значение параметра
     * @param bool   $bRewrite       Флаг - перезаписывать значение при существовании параметра
     *
     * @return UserParameters
     */
    public function addParameter (string $parameterName, $parameterValue, bool $bRewrite = true): UserParameters
    {
        $parameterName = strtolower($parameterName);
        if (!$this->offsetExists($parameterName) || $bRewrite)
        {
            $this->offsetSet($parameterName, $parameterValue);
        }

        return $this;
    }

    /**
     * Возвращает значение указанного параметра, если он задан, либо NULL
     *
     * @param string $parameterName Имя параметра. Будет преобразовано к нижнему регистру
     *
     * @return mixed|null
     */
    public function getParameter (string $parameterName)
    {
        $parameterName = strtolower($parameterName);
        if ($this->offsetExists($parameterName))
        {
            return $this->offsetGet($parameterName);
        }

        return null;
    }

    /**
     * Возвращает TRUE, если указанный параметр существует, иначе вернет FALSE
     *
     * @param string $parameterName Имя параметра. Будет преобразовано к нижнему регистру
     *
     * @return bool
     */
    public function isset(string $parameterName): bool
    {
        $parameterName = strtolower($parameterName);

        return $this->offsetExists($parameterName);
    }

    /**
     * Удаляет значение параметра, если оно было установлено
     *
     * @param string $parameterName Имя параметра. Будет преобразовано к нижнему регистру
     *
     * @return UserParameters
     */
    public function unset(string $parameterName): UserParameters
    {
        $parameterName = strtolower($parameterName);
        if ($this->offsetExists($parameterName))
        {
            $this->offsetUnset($parameterName);
        }

        return $this;
    }
}
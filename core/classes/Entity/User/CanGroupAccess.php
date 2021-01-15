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
 * Класс Ms\Core\Entity\User\CanGroupAccess
 * Список существующих прав доступа группы
 */
class CanGroupAccess extends Dictionary
{
    /**
     * Добавляет флаг доступа группы пользователей
     *
     * @param string $accessName Имя права доступа. Будет преобразовано к нижнему регистру
     * @param bool   $bCan       Флаг наличия доступа
     *
     * @return CanGroupAccess
     */
    public function addCanAccess (string $accessName, bool $bCan): CanGroupAccess
    {
        $this->offsetSet(strtolower($accessName),$bCan);

        return $this;
    }

    /**
     * Возвращает флаг наличия доступа для заданного права
     *
     * @param string $accessName Имя права доступа. Будет преобразовано к нижнему регистру
     *
     * @return bool
     */
    public function isCanAccess (string $accessName): bool
    {
        $accessName = strtolower($accessName);
        if ($this->offsetExists($accessName))
        {
            return (bool)$this->offsetGet($accessName);
        }

        return false;
    }

    /**
     * Возвращает TRUE, если право доступа с заданным именем существует
     *
     * @param string $accessName Имя права доступа. Будет преобразовано к нижнему регистру
     *
     * @return bool
     */
    public function isset(string $accessName): bool
    {
        return $this->offsetExists(strtolower($accessName));
    }

    /**
     * Удаляет существующий флаг указанно права доступа, если он существует
     *
     * @param string $accessName Имя права доступа. Будет преобразовано к нижнему регистру
     *
     * @return CanGroupAccess
     */
    public function unset(string $accessName): CanGroupAccess
    {
        $accessName = strtolower($accessName);
        if ($this->offsetExists($accessName))
        {
            $this->offsetUnset($accessName);
        }

        return $this;
    }
}
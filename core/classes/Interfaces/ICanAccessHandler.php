<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Interfaces;

use Ms\Core\Entity\User\User;

/**
 * Интерфейс Ms\Core\Interfaces\ICanAccessHandler
 * Интерфейс обработчика прав доступа групп пользователей
 */
interface ICanAccessHandler
{
    /**
     * Возвращает флаг права пользователя на совершение определенных действий
     *
     * @param User   $user         Пользователь, для которого происходит проверка прав доступа
     * @param string $moduleName   Имя модуля, для которого проверяются права доступа
     * @param string $accessName   Имя права доступа в нижнем регистре
     * @param array  $arParams     Массив дополнительных параметров, для определения прав
     * @param bool   $bIgnoreAdmin Флаг, обозначающий необходимость игнорировать абсолютные права администратора
     *
     * @return bool
     */
    public function can (User $user, string $moduleName, string $accessName, array $arParams = [], bool $bIgnoreAdmin = false): bool;

    /**
     * Возвращает имя класса
     *
     * @see get_called_class()
     *
     * @return string
     */
    public function getClassName (): string;
}
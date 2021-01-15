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
 * Класс Ms\Core\Entity\User\UserGroupCollection
 * Коллекция групп пользователей
 */
class UserGroupCollection extends Dictionary
{
    /**
     * Добавляет группу пользователей в коллекцию
     *
     * @param UserGroup $userGroup Объект группы пользователей
     *
     * @return UserGroupCollection
     */
    public function addGroup (UserGroup $userGroup): UserGroupCollection
    {
        $this->offsetSet($userGroup->getID(), $userGroup);

        return $this;
    }

    /**
     * Возвращает группу пользователей по ее ID, либо NULL
     *
     * @param int $groupID ID группы пользователей
     *
     * @return UserGroup|null
     */
    public function getGroup (int $groupID): UserGroup
    {
        if ($this->offsetExists($groupID))
        {
            return $this->offsetGet($groupID);
        }

        return null;
    }

    /**
     * Проверяет, существует ли в коллекции группа пользователей с указанным ID
     *
     * @param int $groupID ID группы пользователей
     *
     * @return bool
     */
    public function isset(int $groupID): bool
    {
        return $this->offsetExists($groupID);
    }

    /**
     * Удаляет группу пользователей из коллекции, если она существует в ней
     *
     * @param int $groupID ID группы пользователей
     *
     * @return UserGroupCollection
     */
    public function unset(int $groupID): UserGroupCollection
    {
        if ($this->offsetExists($groupID))
        {
            $this->offsetUnset($groupID);
        }

        return $this;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\User;

/**
 * Класс Ms\Core\Entity\User\UserGroup
 * Объект группы пользователей
 */
class UserGroup
{
    /**
     * ID группы пользователей
     *
     * @var int
     */
    protected $ID = null;
    /**
     * Имя группы пользователей
     *
     * @var null|string
     */
    protected $name = null;
    /**
     * Код группы пользователей
     *
     * @var null|string
     */
    protected $code = null;
    /**
     * Список прав доступа
     *
     * @var CanGroupAccess
     */
    protected $can = null;

    public function __construct (int $groupID)
    {
        $this->ID = (int)$groupID;
        $this->can = new CanGroupAccess();
    }

    /**
     * Возвращает ID группы пользователей
     *
     * @return int
     */
    public function getID (): int
    {
        return $this->ID;
    }

    /**
     * Возвращает название группы пользователей
     *
     * @return string|null
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * Устанавливает название группы пользователей
     *
     * @param string|null $name
     *
     * @return UserGroup
     */
    public function setName (string $name = null): UserGroup
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Возвращает код группы пользователей
     *
     * @return string|null
     */
    public function getCode ()
    {
        return $this->code;
    }

    /**
     * Устанавливает код группы пользователей
     *
     * @param string|null $code
     *
     * @return UserGroup
     */
    public function setCode (string $code = null): UserGroup
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Возвращает объект прав доступа группы
     *
     * @return CanGroupAccess
     */
    public function getCanGroupAccess ()
    {
        return $this->can;
    }

    /**
     * Возвращает флаг наличия у группы указанного права доступа
     *
     * @param string $accessName Имя права доступа
     *
     * @return bool
     */
    public function can (string $accessName)
    {
        $accessName = strtolower($accessName);

        return $this->can->isCanAccess($accessName);
    }
}
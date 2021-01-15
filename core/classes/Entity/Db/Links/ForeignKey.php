<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Links;

/**
 * Класс Ms\Core\Entity\Db\Links\ForeignKey
 * Связи таблиц БД при помощи FOREIGN KEY
 */
class ForeignKey
{
    const FOREIGN_CASCADE     = 'CASCADE';

    const FOREIGN_SET_NULL    = 'SET NULL';

    const FOREIGN_NO_ACTION   = 'NO ACTION';

    const FOREIGN_RESTRICT    = 'RESTRICT';

    const FOREIGN_SET         = 'SET';

    const FOREIGN_LIST        = [
        self::FOREIGN_CASCADE,
        self::FOREIGN_SET_NULL,
        self::FOREIGN_NO_ACTION,
        self::FOREIGN_RESTRICT,
        self::FOREIGN_SET
    ];

    /** @var string */
    protected $onUpdate = null;
    /** @var string */
    protected $onDelete = null;

    /**
     * Конструктор класса ForeignKey
     * По умолчанию устанавливает действие onUpdate = CASCADE и onDelete = CASCADE
     */
    public function __construct ()
    {
        $this->setOnUpdateCascade();
        $this->setOnDeleteCascade();
    }

    /**
     * Возвращает тип действия при обновлении поля
     *
     * @return string
     * @unittest
     */
    public function getOnUpdate ()
    {
        return $this->onUpdate;
    }

    /**
     * Возвращает тип действия при удалении поля
     *
     * @return string
     * @unittest
     */
    public function getOnDelete ()
    {
        return $this->onDelete;
    }

    /**
     * Устанавливает действие CASCADE для FOREIGN KEY при обновлении связанного поля
     *
     * @return $this
     * @unittest
     */
    public function setOnUpdateCascade ()
    {
        $this->onUpdate = self::FOREIGN_CASCADE;

        return $this;
    }

    /**
     * Устанавливает действие SET_NULL для FOREIGN KEY при обновлении связанного поля
     *
     * @return $this
     * @unittest
     */
    public function setOnUpdateSetNull()
    {
        $this->onUpdate = self::FOREIGN_SET_NULL;

        return $this;
    }

    /**
     * Устанавливает действие NO_ACTION для FOREIGN KEY при обновлении связанного поля
     *
     * @return $this
     * @unittest
     */
    public function setOnUpdateNoAction ()
    {
        $this->onUpdate = self::FOREIGN_NO_ACTION;

        return $this;
    }

    /**
     * Устанавливает действие RESTRICT для FOREIGN KEY при обновлении связанного поля
     *
     * @return $this
     * @unittest
     */
    public function setOnUpdateRestrict ()
    {
        $this->onUpdate = self::FOREIGN_RESTRICT;

        return $this;
    }

    /**
     * Устанавливает действие SET [значение] для FOREIGN KEY при обновлении связанного поля
     *
     * @param null|string $value Значение, которое будет установлено
     *
     * @return $this
     * @unittest
     */
    public function setOnUpdateSet (string $value = null)
    {
        $this->onUpdate = self::FOREIGN_SET . ' ' . is_null($value) ? 'NULL' : (string)$value;

        return $this;
    }

    /**
     * Устанавливает действие CASCADE для FOREIGN KEY при удалении связанного поля
     *
     * @return $this
     * @unittest
     */
    public function setOnDeleteCascade ()
    {
        $this->onDelete = self::FOREIGN_CASCADE;

        return $this;
    }

    /**
     * Устанавливает действие SET_NULL для FOREIGN KEY при удалении связанного поля
     *
     * @return $this
     * @unittest
     */
    public function setOnDeleteSetNull ()
    {
        $this->onDelete = self::FOREIGN_SET_NULL;

        return $this;
    }

    /**
     * Устанавливает действие NO_ACTION для FOREIGN KEY при удалении связанного поля
     *
     * @return $this
     * @unittest
     */
    public function setOnDeleteNoAction ()
    {
        $this->onDelete = self::FOREIGN_NO_ACTION;

        return $this;
    }

    /**
     * Устанавливает действие RESTRICT для FOREIGN KEY при удалении связанного поля
     *
     * @return $this
     * @unittest
     */
    public function setOnDeleteRestrict ()
    {
        $this->onDelete = self::FOREIGN_RESTRICT;

        return $this;
    }

    /**
     * Устанавливает действие SET [значение] для FOREIGN KEY при удалении связанного поля
     *
     * @param string|null $value Значение, которое будет установлено
     *
     * @return $this
     * @unittest
     */
    public function setOnDeleteSet (string $value = null)
    {
        $this->onDelete = self::FOREIGN_SET . ' ' . is_null($value) ? 'NULL' : (string)$value;

        return $this;
    }
}
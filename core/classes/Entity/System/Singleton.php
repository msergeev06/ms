<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

/**
 * Класс Ms\Core\Entity\System\Singleton
 * Реализация прототипа "Одиночка"
 */
class Singleton
{
    /** @var null|$this */
    protected static $instance = null;

    /**
     * Возвращает единый объект
     *
     * @return Singleton|null
     */
    public static function getInstance ()
    {
        if (is_null(static::$instance))
        {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Защищенный конструктор класса Singleton
     */
    protected function __construct ()
    {

    }
}
<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

/**
 * Класс Ms\Core\Entity\System\Multiton
 * Мультитон. Упрощает создание Синглтонов
 */
abstract class Multiton
{
    /**
     * @var array
     */
    protected static $instances = [];

    /**
     * Возвращает экземпляр класса, из которого вызван
     *
     * @return static
     */
    public static function getInstance()
    {
        $className = static::getClassName();
        if (!isset(self::$instances[$className]) || !(self::$instances[$className] instanceof $className))
        {
            self::$instances[$className] = new $className();
        }

        return self::$instances[$className];
    }

    /**
     * Удаляет экземпляр класса, из которого вызван
     *
     * @return void
     */
    public static function removeInstance()
    {
        $className = static::getClassName();
        if (array_key_exists($className, self::$instances))
        {
            unset(self::$instances[$className]);
        }
    }

    /**
     * Возвращает имя экземпляра класса
     *
     * @return string
     */
    final protected static function getClassName()
    {
        return get_called_class();
    }

    /**
     * Коструктор закрыт
     */
    protected function __construct()
    {
    }

    /**
     * Клонирование запрещено
     */
    final protected function __clone()
    {
    }

    /**
     * Сериализация запрещена
     */
    final protected function __sleep()
    {
    }

    /**
     * Десериализация запрещена
     */
    final protected function __wakeup()
    {
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2017 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

/**
 * Класс Ms\Core\Entity\System\Session
 * Класс для работы с сессией
 */
class Session extends Multiton
{
    /**
     * Идентификатор сессии
     *
     * @var string
     */
    private $SID;

    protected function __construct ()
    {
        $this->start();
        $this->SID = session_id();
    }

    /**
     * Возвращает значение переменной сессии
     *
     * @param string $sName Имя переменной сессии
     *
     * @return mixed
     */
    public function get ($sName)
    {
        if (isset($_SESSION[$sName]))
        {
            return $_SESSION[$sName];
        }
        else
        {
            return null;
        }
    }

    /**
     * Возвращает идентификатор сессии
     *
     * @return string
     */
    public function getSID ()
    {
        return $this->SID;
    }

    /**
     * Проверяет установлена ли переменная сессии
     *
     * @param string $sName Имя переменной сессии
     *
     * @return bool
     */
    public function isset ($sName)
    {
        return isset($_SESSION[$sName]);
    }

    /**
     * Устанавливает переменную сессии.
     * Возвращает ссылку на объект
     *
     * @param string $sName  Имя переменной сессии
     * @param mixed  $mValue Значение переменной сессии
     *
     * @return $this
     */
    public function set ($sName, $mValue)
    {
        $_SESSION[$sName] = $mValue;

        return $this;
    }

    /**
     * Стартует новую или существующую сессию
     *
     * @param array $arOptions Опции
     *
     * @return bool
     * @see session_start()
     *
     */
    protected function start ($arOptions = [])
    {
        return @session_start($arOptions);
    }
}

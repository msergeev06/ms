<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

/**
 * Класс Ms\Core\Entity\System\Debugger
 * Отладчик
 * TODO: Доделать
 */
class Debugger extends Multiton
{
    /**
     * Возвращает флаг включенной отладки
     *
     * @return bool
     */
    public function isDebug()
    {
        //получение системного флага отладки
        return true;
    }

    /**
     * Выводит на экран значения переменных
     *
     * @return void
     */
    public function show()
    {
        if (!$this->isDebug())
        {
            return;
        }
        //функционал msDebug
    }
}

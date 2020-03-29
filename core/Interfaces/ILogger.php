<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Interfaces;

/**
 * Интерфейс Ms\Core\Interfaces\ILogger
 * Интерфейс, описывающий логеры
 */
interface ILogger
{
    /**
     * Добавляет сообщение в лог
     *
     * @param string $strMessage Сообщение для логирования
     * @param array  $arReplace  Массив замен для текста сообщения
     *
     * @return $this
     */
    public function addMessage(string $strMessage, array $arReplace = []);

    /**
     * Позволяет добавить в лог сообщение иного типа, чем того, с которым создан
     * объект логера
     *
     * @param string $type       Тип сообщения лога
     * @param string $strMessage Сообщение для логирования
     * @param array  $arReplace  Массив замен для сообщения лога
     *
     * @return mixed
     */
    public function addMessageOtherType(string $type, string $strMessage, array $arReplace = []);
}
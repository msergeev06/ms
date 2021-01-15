<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Interfaces;

/**
 * Интерфейс Ms\Core\Interfaces\INode
 * Узел дерева каталогов
 */
interface INode
{
    /**
     * Возвращает значение ключа узла
     *
     * @return mixed
     */
    public function getPrimary ();

    /**
     * Устанавливает значение ключа узла
     *
     * @param mixed $primary Значение ключа
     *
     * @return INode
     */
    public function setPrimary ($primary = null);

    /**
     * Возвращает TRUE, если узел активен, иначе возвращает FALSE
     *
     * @return bool
     */
    public function isActive (): bool;

    /**
     * Устанавливает флаг активности/неактивности узла
     *
     * @param bool $isActive Флаг активности
     *
     * @return INode
     */
    public function setActive (bool $isActive = true);

    /**
     * Возвращает левую границу узла
     *
     * @return int
     */
    public function getLeftMargin (): int;

    /**
     * Устанавливает левую границу узла
     *
     * @param int $leftMargin Левая граница
     *
     * @return INode
     */
    public function setLeftMargin (int $leftMargin);

    /**
     * Возвращает правую границу узла
     *
     * @return int
     */
    public function getRightMargin (): int;

    /**
     * Устанавливает правую границу узла
     *
     * @param int $rightMargin Правая граница
     *
     * @return INode
     */
    public function setRightMargin (int $rightMargin);

    /**
     * Возвращает уровень вложенности узла
     *
     * @return int
     */
    public function getDepthLevel (): int;

    /**
     * Устанавливает уровень вложенности узла
     *
     * @param int $depthLevel Уровень вложенности
     *
     * @return INode
     */
    public function setDepthLevel (int $depthLevel);

    /**
     * Возвращает ключ родительского узла
     *
     * @return mixed
     */
    public function getParent ();

    /**
     * Устанавливает ключ родительского узла
     *
     * @param mixed $parent Ключ родительского узла
     *
     * @return INode
     */
    public function setParent ($parent);
}
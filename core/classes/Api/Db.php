<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Api;

use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Entity\System\Multiton;

/**
 * Класс Ms\Core\Api\Db
 * API методы для работы с базой данных и таблицами
 */
class Db extends Multiton
{
    /**
     * Возвращает объект класса ORMController, для переданной таблицы
     *
     * @param TableAbstract $table Объект таблицы
     *
     * @return ORMController
     * @unittest
     */
    public function getTableOrm (TableAbstract $table)
    {
        return ORMController::getInstance($table);
    }

    /**
     * Возвращает объект класса ORMController, для переданного класса таблицы
     *
     * @param string $tableClass     Класс таблицы
     * @param string $additionalName Расширение имени таблицы
     *
     * @return ORMController
     * @unittest
     */
    public function getTableOrmByClass (string $tableClass, string $additionalName = '')
    {
        return ORMController::getInstance(new $tableClass($additionalName));
    }

    /**
     * Возвращает объект помощника описания полей таблицы БД
     *
     * @return \Ms\Core\Entity\Helpers\TableHelper
     * @unittest
     */
    public function getTableHelper ()
    {
        return \Ms\Core\Entity\Helpers\TableHelper::getInstance();
    }
}

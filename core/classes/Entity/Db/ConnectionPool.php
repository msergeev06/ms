<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Db\ConnectionPool
 * Коллекция подключений к базам данных
 */
class ConnectionPool extends Dictionary
{
    /**
     * Возвращает подключение с указанным именем. Если такого не существует в коллекции - создает его
     *
     * @param string $sConnectionName
     *
     * @return Connection
     */
    public function getConnection (string $sConnectionName = 'default')
    {
        if ($this->offsetExists($sConnectionName))
        {
            return $this->offsetGet($sConnectionName);
        }
        else
        {
            $this->offsetSet($sConnectionName, (new Connection($sConnectionName)));

            return $this->offsetGet($sConnectionName);
        }
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Modules\ModulesCollection
 * Коллекция существующих модулей (загруженных, подключенных, инициированных)
 */
class ModulesCollection extends Dictionary
{
    /**
     * Добавляет описание модуля в коллекцию
     *
     * @param string $moduleName
     *
     * @return $this
     * @unittest
     */
    public function addModule (string $moduleName)
    {
        $moduleName = strtolower($moduleName);
        $module = new Module($moduleName);
        $this->offsetSet($moduleName, $module);

        return $this;
    }

    /**
     * Возвращает TRUE, если модуль существует в коллекции
     *
     * @param string $moduleName Имя модуля
     *
     * @return bool
     * @unittest
     */
    public function isExists (string $moduleName)
    {
        $moduleName = strtolower($moduleName);

        return $this->offsetExists($moduleName);
    }

    /**
     * Возвращает объект, описывающий модуль, если он существует в коллекции, иначе возвращает NULL
     *
     * @param string $moduleName Имя модуля
     *
     * @return Module|null
     * @unittest
     */
    public function getModule (string $moduleName)
    {
        $moduleName = strtolower($moduleName);
        if ($this->isExists($moduleName))
        {
            return $this->offsetGet($moduleName);
        }

        return null;
    }
}
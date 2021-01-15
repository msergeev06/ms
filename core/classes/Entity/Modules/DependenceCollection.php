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
 * Класс Ms\Core\Entity\Modules\DependenceCollection
 * Коллекция зависимостей модулей
 */
class DependenceCollection extends Dictionary
{
    /**
     * Добавляет зависимость модуля
     *
     * @param Dependence $dependence
     *
     * @return $this
     * @unittest
     */
    public function addDependence (Dependence $dependence)
    {
        $this->offsetSet($dependence->getModuleName(),$dependence);

        return $this;
    }

    /**
     * Возвращает зависимость по имени модуля
     *
     * @param string $moduleName Имя модуля
     *
     * @return Dependence|null
     * @unittest
     */
    public function getDependence (string $moduleName)
    {
        $moduleName = strtolower($moduleName);

        if ($this->offsetExists($moduleName))
        {
            return $this->offsetGet($moduleName);
        }

        return null;
    }

    /**
     * Проверяет существование зависимости модуля в коллекции
     *
     * @param string $moduleName Имя модуля
     *
     * @return bool
     * @unittest
     */
    public function issetDependence (string $moduleName)
    {
        $moduleName = strtolower($moduleName);

        return $this->offsetExists($moduleName);
    }
}
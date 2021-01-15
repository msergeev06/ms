<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules;

/**
 * Класс Ms\Core\Entity\Modules\ModuleDependencies
 * Служит для проверки зависимостей одного модуля от других
 */
class ModuleDependencies
{
    /** @var DependenceCollection */
    protected $requiredDependenciesCollection = null;
    /** @var DependenceCollection */
    protected $additionalDependenciesCollection = null;

    public function __construct()
    {
        $this->requiredDependenciesCollection = new DependenceCollection();
        $this->additionalDependenciesCollection = new DependenceCollection();
    }

    /**
     * Возвращает коллекцию обязательных зависимостей
     *
     * @return DependenceCollection
     * @unittest
     */
    public function getRequiredDependenciesCollection()
    {
        return $this->requiredDependenciesCollection;
    }

    /**
     * Устанавливает коллекцию обязательных зависимостей
     *
     * @param DependenceCollection|null $requiredDependenciesCollection Коллекция обязательных зависимостей
     *
     * @return ModuleDependencies
     * @unittest
     */
    public function setRequiredDependenciesCollection(
        DependenceCollection $requiredDependenciesCollection = null
    ): ModuleDependencies
    {
        $this->requiredDependenciesCollection = $requiredDependenciesCollection;

        return $this;
    }

    /**
     * Возвращает коллекцию дополнительных зависимостей
     *
     * @return DependenceCollection
     * @unittest
     */
    public function getAdditionalDependenciesCollection()
    {
        return $this->additionalDependenciesCollection;
    }

    /**
     * Устанавливает коллекцию дополнительных зависимостей
     *
     * @param DependenceCollection|null $additionalDependenciesCollection Коллекция дополнительных зависимостей
     *
     * @return ModuleDependencies
     * @unittest
     */
    public function setAdditionalDependenciesCollection(
        DependenceCollection $additionalDependenciesCollection = null
    ): ModuleDependencies
    {
        $this->additionalDependenciesCollection = $additionalDependenciesCollection;

        return $this;
    }


}
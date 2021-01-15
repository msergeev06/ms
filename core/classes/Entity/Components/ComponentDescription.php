<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Components;

use Ms\Core\Exceptions\Classes\ClassNotFoundException;
use Ms\Core\Entity\Modules\Modules;

/**
 * Класс Ms\Core\Entity\Components\ComponentDescription
 * Объект, в котором описываются компоненты
 */
class ComponentDescription
{
    /** @var string */
    protected $className = null;
    protected $name = '';
    protected $description = '';
    protected $moduleName = '';

    /**
     * Конструктор класса ComponentDescription
     *
     * @param string $className Имя класса
     * @param string $dir       Откуда вызван
     *
     * @throws ClassNotFoundException
     * @unittest
     */
    public function __construct (string $className, string $dir = null)
    {
        if (is_null($dir))
        {
            $back = debug_backtrace();
            $first = array_shift($back);
            $dir = dirname($first['file']);
        }
        $file = $dir . '/class.php';
        if (file_exists($file))
        {
            include_once ($file);
        }
        if (!class_exists($className))
        {
            throw new ClassNotFoundException($className);
        }

        $this->className = $className;
    }

    /**
     * Возвращает имя класса компонента
     *
     * @return string
     * @unittest
     */
    public function getClassName ()
    {
        return $this->className;
    }

    /**
     * Возвращает имя компонента
     *
     * @return string
     * @unittest
     */
    public function getName (): string
    {
        return $this->name;
    }

    /**
     * Устанавливает имя компонента
     *
     * @param string $name
     *
     * @return ComponentDescription
     * @unittest
     */
    public function setName (string $name): ComponentDescription
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Возвращает описание компонента
     *
     * @return string
     * @unittest
     */
    public function getDescription (): string
    {
        return $this->description;
    }

    /**
     * Устанавливает описание компонента
     *
     * @param string $description
     *
     * @return ComponentDescription
     * @unittest
     */
    public function setDescription (string $description): ComponentDescription
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Возвращает имя модуля, добавившего компонент
     *
     * @return string
     * @unittest
     */
    public function getModuleName (): string
    {
        return $this->moduleName;
    }

    /**
     * Устанавливает имя модуля, добавившего компонент
     *
     * @param string $moduleName
     *
     * @return ComponentDescription
     * @unittest
     */
    public function setModuleName (string $moduleName): ComponentDescription
    {
        if (Modules::getInstance()->checkModuleName($moduleName))
        {
            $this->moduleName = $moduleName;
        }

        return $this;
    }
}
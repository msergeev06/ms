<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules;

use Ms\Core\Exceptions\Arguments\ArgumentException;

/**
 * Класс Ms\Core\Entity\Modules\Dependence
 * Описывает зависимость одного модуля от другого
 */
class Dependence
{
    /**
     * Имя модуля
     *
     * @var string
     */
    protected $moduleName = null;
    /**
     * Выражение требуемой версии, либо null
     *
     * @var null|string
     */
    protected $needVersion = null;
    /**
     * Флаг необходимости инициализации зависимости при подключении модуля
     *
     * @var bool
     */
    protected $needInclude = false;
    /**
     * Флаг необходимости проверки что зависимый модуль установлен
     *
     * @var bool
     */
    protected $needInstalled = false;

    /**
     * Конструктор класса Dependence
     *
     * @param string $moduleName Полное имя зависимого модуля
     *
     * @throws ArgumentException
     */
    public function __construct(string $moduleName)
    {
        $this->setModuleName($moduleName);
    }

    /**
     * Возвращает имя модуля зависимости
     *
     * @return string
     * @unittest
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * Устанавливает имя модуля зависимости
     *
     * @param string $moduleName
     *
     * @return Dependence
     * @throws ArgumentException
     * @unittest
     */
    public function setModuleName(string $moduleName): Dependence
    {
        if (!Modules::getInstance()->checkModuleName($moduleName))
        {
            throw new ArgumentException('Неверное имя модуля','moduleName');
        }

        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * Возвращает выражение требуемой версии модуля, если оно задано, иначе вернет NULL
     *
     * @return null|string
     * @unittest
     */
    public function getNeedVersion()
    {
        return $this->needVersion;
    }

    /**
     * Устанавливает выражение требуемой версии модуля
     *
     * @param string $needVersion
     *
     * @return Dependence
     * @throws ArgumentException
     * @unittest
     */
    public function setNeedVersion(string $needVersion): Dependence
    {
        if (Modules::getInstance()->checkVersionExpression($needVersion))
        {
            $this->needVersion = $needVersion;
        }
        else
        {
            throw new ArgumentException(
                'Выражениее требуемой версии указано в неверном формате',
                'needVersion'
            );
        }

        return $this;
    }

    /**
     * Возвращает TRUE, если необходимо при инициализации модуля подключать зависимый
     *
     * @return bool
     * @unittest
     */
    public function isNeedInclude(): bool
    {
        return $this->needInclude;
    }

    /**
     * Устанавливает флаг необходимость подключения зависимого модуля, при инициализации
     *
     * @param bool $needInclude
     *
     * @return Dependence
     * @unittest
     */
    public function setNeedInclude(bool $needInclude): Dependence
    {
        $this->needInclude = $needInclude;

        return $this;
    }

    /**
     * Возвращает TRUE, если зависимый модуль должен быть установлен
     *
     * @return bool
     * @unittest
     */
    public function isNeedInstalled(): bool
    {
        return $this->needInstalled;
    }

    /**
     * Устанавливает флаг необходимости проверки, что зависимый модуль установлен
     *
     * @param bool $needInstalled
     *
     * @return Dependence
     * @unittest
     */
    public function setNeedInstalled(bool $needInstalled): Dependence
    {
        $this->needInstalled = $needInstalled;

        return $this;
    }


}
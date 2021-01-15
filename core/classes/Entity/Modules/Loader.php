<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules;

use Ms\Core\Entity\Modules\Versions\ModuleVersion;
use Ms\Core\Entity\Modules\Versions\Version;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Entity\System\Settings;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Arguments\ArgumentException;
use Ms\Core\Exceptions\Modules\LoaderException;
use Ms\Core\Exceptions\Modules\ModuleDoesNotExistsException;
use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Entity\System\Loader
 * Загрузчик модулей
 */
class Loader extends Multiton
{
    const IGNORED_DIRS = ['.', '..', '.readme'];

    /** @var ModulesCollection */
    protected $modulesCollection = null;
    /** @var string Путь к локальной папке с модулями */
    protected $modulesLocalRoot = null;
    /** @var string Путь к системной папке с модулями */
    protected $modulesRoot = null;
    /** @var string Путь к папке upload */
    protected $uploadRoot = null;

    protected function __construct ()
    {
        $this->modulesCollection = new ModulesCollection();
        $settings = Settings::getInstance();
        $this->modulesRoot = $settings->getModulesRoot();
        $this->modulesLocalRoot = $settings->getLocalModulesRoot();
        $this->uploadRoot = $settings->getUploadDir();
        $this->initModulesList($this->modulesRoot);
        $this->initModulesList($this->modulesLocalRoot);
    }

    /**
     * Проверяет соответствие имени модуля установленным правилам
     *
     * @param string $moduleName Имя модуля
     *
     * @return bool
     * @unittest
     */
    public function checkModuleName (string $moduleName)
    {
        return Modules::getInstance()->checkModuleName($moduleName);
    }

    /**
     * Возвращает массив установленных модулей и их версии.
     * Если версия не указана, для модуля устанавливается версия 0.1.0
     *
     * @return array|bool
     * @unittest
     */
    public function getArrayModulesVersions ()
    {
        $arVersions = [];
        if ($this->getModulesCollection()->isEmpty())
        {
            return false;
        }
        /**
         * @var string $moduleName
         * @var Module $module
         */
        foreach ($this->modulesCollection as $moduleName => $module)
        {
            $arVersions[$moduleName] = [];
            $arVersions[$moduleName]['VERSION'] = $module->getModuleVersionNumber();
            $arVersions[$moduleName]['VERSION_DATE'] = $module->getModuleVersionDate();
        }

        if (!empty($arVersions))
        {
            return $arVersions;
        }

        return false;
    }

    /**
     * Возвращает информацию о модуле, если модуль описан в коллекции, либо false
     *
     * @param string $moduleName Имя модуля
     *
     * @return array
     * @throws ArgumentException
     * @unittest
     */
    public function getModuleInfo (string $moduleName)
    {
        $moduleName = strtolower($moduleName);
        if (!static::getInstance()->checkModuleName($moduleName))
        {
            throw new ArgumentException('Неверное имя модуля', 'moduleName', __FILE__, __LINE__);
        }
        if ($this->isExists($moduleName))
        {
            return $this->getModule($moduleName)->getModuleInfo();
        }

        return [];
    }

    /**
     * Возвращает корневую директорию модуля
     *
     * @param string $moduleName Имя модуля
     *
     * @return bool|mixed
     * @unittest
     */
    public function getModulePath (string $moduleName)
    {
        if ($this->isExists($moduleName))
        {
            return $this->getModule($moduleName)->getModulePath();
        }

        return false;
    }

    /**
     * Возвращает версию указанного модуля, либо NULL, если она не задана
     *
     * @param string $moduleName Полное имя модуля
     *
     * @return ModuleVersion|null
     * @throws ArgumentException
     * @unittest
     */
    public function getModuleVersion (string $moduleName)
    {
        if (!Modules::getInstance()->checkModuleName($moduleName))
        {
            throw new ArgumentException('Неверное имя модуля', $moduleName, __FILE__, __LINE__);
        }

        if ($this->isExists($moduleName))
        {
            return $this->getModule($moduleName)->getModuleVersion();
        }

        return null;
    }

    /**
     * Возвращает дату установленной версии указанного модуля
     *
     * @param string $moduleName Имя модуля
     *
     * @return bool|mixed
     * @throws ArgumentException
     * @unittest
     */
    public function getModuleVersionDate (string $moduleName)
    {
        $moduleName = strtolower($moduleName);
        if (!static::getInstance()->checkModuleName($moduleName))
        {
            throw new ArgumentException('Неверное имя модуля', 'moduleName', __FILE__, __LINE__);
        }
        if ($this->isExists($moduleName))
        {
            return $this->getModule($moduleName)->getModuleVersionDate();
        }

        return false;
    }

    /**
     * Возвращает номер версии модуля, если он описан в коллекции, либо FALSE
     *
     * @param string $moduleName
     *
     * @return bool|string
     * @throws ArgumentException
     * @unittest
     */
    public function getModuleVersionNumber (string $moduleName)
    {
        $moduleName = strtolower($moduleName);
        if (!static::getInstance()->checkModuleName($moduleName))
        {
            throw new ArgumentException('Неверное имя модуля', 'moduleName', __FILE__, __LINE__);
        }
        if ($this->isExists($moduleName))
        {
            return $this->getModule($moduleName)->getModuleVersionNumber();
        }

        return false;
    }

    /**
     * Подключает модуль
     *
     * @param string $moduleName
     *
     * @return bool
     * @throws ModuleDoesNotExistsException
     * @throws LoaderException
     * @unittest
     */
    public static function includeModule (string $moduleName)
    {
        $moduleName = strtolower($moduleName);
        //1. Если модуль нет, выдаем исключение
        if (!static::getInstance()->isExists($moduleName))
        {
            if (class_exists('\Ms\Core\Exceptions\Modules\ModuleDoesNotExistsException'))
            {
                throw new ModuleDoesNotExistsException($moduleName);
            }

            return false;
        }
        //1. Иначе сохраняем путь к модулю
        else
        {
            $modulePath = static::getInstance()->getModule($moduleName)->getModulePath();
        }

        //2. Проверяем, не был ли модуль подключен ранее
        if (static::getInstance()->getModule($moduleName)->isIncluded())
        {
            return true;
        }

        //3. Проверяем существование файла зависимостей модуля
        if (file_exists($modulePath . '/required.php'))
        {
            //Подключаем файл зависимостей модулей
            $obModuleDependencies = include($modulePath . '/required.php');
            //Если файл вернул объект класса зависимостей, обрабатываем его
            if ($obModuleDependencies instanceof ModuleDependencies)
            {
                //Если обработчик вернул FALSE, не удалось удовлетворить обязательным зависимостям,
                // модуль подключать нельзя
                if (!static::getInstance()->includeModuleDependencies($obModuleDependencies))
                {
                    if (class_exists('\Ms\Core\Exceptions\Modules\LoaderException'))
                    {
                        throw new LoaderException (
                            'Не удалось подключить обязательные зависимости модуля "' . $moduleName . '"'
                        );
                    }

                    return false;
                }
            }
        }

        //4. Если подключаемый файл модуля существует, подключаем его
        if (file_exists($modulePath . '/include.php'))
        {
            include($modulePath . '/include.php');
        }

        //5. Обрабатываем значения опций по-умолчанию, если они заданы
        static::getInstance()->prepareDefaultOptions($moduleName);

        //6. Помечаем успешную загрузку модуля
        static::getInstance()->getModule($moduleName)->setIncluded();

        return true;
    }

    /**
     * Возвращает TRUE, если модуль уже был подключен
     *
     * @param string $moduleName Имя модуля
     *
     * @return bool
     * @unittest
     */
    public function isIncludedModule (string $moduleName)
    {
        $moduleName = strtolower($moduleName);
        if ($this->isExists($moduleName))
        {
            return $this->getModule($moduleName)->isIncluded();
        }

        return false;
    }

    /**
     * Если модуль найден и установлен, возвращает TRUE, иначе FALSE
     *
     * @param string $moduleName Полное имя модуля
     *
     * @return bool
     * @unittest
     */
    public static function isInstalled (string $moduleName)
    {
        $moduleName = strtolower($moduleName);
        if (!Modules::getInstance()->checkModuleName($moduleName))
        {
            return false;
        }
        if (static::getInstance()->isExists($moduleName))
        {
            return static::getInstance()->getModule($moduleName)->isInstalled();
        }

        return false;
    }

    /**
     * Возвращает TRUE, если модуль существует и его версия соответствует условию, иначе FALSE
     *
     * @param string      $moduleName
     * @param string|null $needVersionExpression
     *
     * @return bool
     * @unittest
     */
    public static function issetModule (string $moduleName, string $needVersionExpression = null)
    {
        $moduleName = strtolower($moduleName);
        if (static::getInstance()->isExists($moduleName))
        {
            if (is_null($needVersionExpression))
            {
                return true;
            }

            try
            {
                return Modules::getInstance()->isCorrectVersion(
                    $needVersionExpression,
                    static::getInstance()->getModuleVersionNumber($moduleName)
                )
                    ;
            }
            catch (ArgumentException $e)
            {
                return false;
            }
        }

        return false;
    }

    /**
     * Возвращает объект модуля, если он описан в коллекции, иначе возвращает NULL
     *
     * @param string $moduleName Имя модуля
     *
     * @return \Ms\Core\Entity\Modules\Module|null
     */
    protected function getModule (string $moduleName)
    {
        if ($this->isExists($moduleName))
        {
            return $this->getModulesCollection()->getModule($moduleName);
        }

        return null;
    }

    /**
     * Возвращает коллекцию модулей
     *
     * @return ModulesCollection
     */
    protected function getModulesCollection ()
    {
        return $this->modulesCollection;
    }

    /**
     * Обрабатывает список зависимостей модуля и подключает, если необходимо зависимости
     *
     * @param DependenceCollection $dependenceCollection Коллекция зависимостей модуля
     * @param bool                 $required             Флаг подключения обязательных зависимостей
     *
     * @return bool
     */
    protected function includeDependencies (DependenceCollection $dependenceCollection, bool $required = false)
    {
        if ($dependenceCollection->isEmpty())
        {
            return true;
        }

        /** @var Dependence $dependence */
        foreach ($dependenceCollection as $dependence)
        {
            //1. Если модуля не существует
            if (!static::issetModule($dependence->getModuleName()))
            {
                //Если зависимость обязательная - ошибка
                if ($required)
                {
                    return false;
                }
            }

            //2. Плохо, если необходимо чтобы модуль был установлен, а он не установлен
            if ($dependence->isNeedInstalled() && !static::isInstalled($dependence->getModuleName()))
            {
                //Если зависимость обязательная - ошибка
                if ($required)
                {
                    return false;
                }
            }

            //3. Если задана требуемая версия модуля, проверяем ее
            try
            {
                if (
                    !is_null($dependence->getNeedVersion())
                    && !Modules::getInstance()->isCorrectVersion(
                        $dependence->getNeedVersion(),
                        static::getModuleVersionNumber(
                            $dependence->getModuleName()
                        )
                    )
                )
                {
                    if ($required)
                    {
                        return false;
                    }
                }
            }
            catch (ArgumentException $e)
            {
                if ($required)
                {
                    return false;
                }
            }

            //4. Если необходимо подключать зависимый модуль при инициализации, пробуем подключить
            if ($dependence->isNeedInclude())
            {
                try
                {
                    if (!static::includeModule($dependence->getModuleName()))
                    {
                        //Если подключить не удалось и зависимость обязательная - ошибка
                        if ($required)
                        {
                            return false;
                        }
                    }
                }
                catch (SystemException $e)
                {
                    if ($required)
                    {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Подключает зависимости модуля
     *
     * @param ModuleDependencies $moduleDependencies
     *
     * @return bool
     */
    protected function includeModuleDependencies (ModuleDependencies $moduleDependencies)
    {
        //1. Обрабатываем обязательные зависимости
        $collection = $moduleDependencies->getRequiredDependenciesCollection();
        if (!is_null($collection) && !$collection->isEmpty())
        {
            $bOk = $this->includeDependencies($collection, true);
            //Если какая-то из обязательных зависимостей не была удовлетворена, возвращаем FALSE
            if (!$bOk)
            {
                return false;
            }
        }

        //2. Обрабатываем дополнительные зависимости
        $collection = $moduleDependencies->getAdditionalDependenciesCollection();
        $this->includeDependencies($collection);

        return true;
    }

    /**
     * Обновляет список установленных модулей в указанной директории
     *
     * @param string $modulesRoot Путь к установленным модулям
     *
     * @return bool
     */
    protected function initModulesList ($modulesRoot)
    {
        if (!is_dir($modulesRoot))
        {
            return false;
        }

        if ($dh = opendir($modulesRoot))
        {
            //Смотрим все папки модулей
            while (($file = @readdir($dh)) !== false)
            {
                if (!in_array($file, self::IGNORED_DIRS))
                {
                    //Если папка соответствует требованиям названия модуля
                    if ($this->checkModuleName($file))
                    {
                        //Сохраняем название модуля
                        $this->getModulesCollection()->addModule($file);
                        $module = $this->getModule($file);
                        $module->setModulePath($modulesRoot . '/' . $file);

                        //Если существует файл версии модуля, обрабатываем
                        if (file_exists($modulesRoot . '/' . $file . '/version.php'))
                        {
                            $arVersion = include($modulesRoot . '/' . $file . '/version.php');
                            if ($arVersion instanceof ModuleVersion)
                            {
                                $module->setModuleVersion($arVersion);
                            }
                            elseif (is_array($arVersion))
                            {
                                $moduleVersion = new ModuleVersion();
                                if (!empty($arVersion['VERSION']))
                                {
                                    if ($arVersion['VERSION'] instanceof Version)
                                    {
                                        $moduleVersion->setVersion($arVersion['VERSION']);
                                    }
                                    else
                                    {
                                        $moduleVersion->setVersion(new Version($arVersion['VERSION']));
                                    }
                                }
                                if (!empty($arVersion['VERSION_DATE']))
                                {
                                    if ($arVersion['VERSION_DATE'] instanceof Date)
                                    {
                                        $moduleVersion->setVersionDate($arVersion['VERSION_DATE']);
                                    }
                                    elseif (Date::checkDate($arVersion['VERSION_DATE']))
                                    {
                                        try
                                        {
                                            $moduleVersion->setVersionDate(new Date($arVersion['VERSION_DATE']));
                                        }
                                        catch (SystemException $e)
                                        {
                                        }
                                    }
                                }
                                $module->setModuleVersion($moduleVersion);
                            }
                        }

                        //Если модуль установлен, отмечаем это
                        if (file_exists($modulesRoot . '/' . $file . '/installed'))
                        {
                            $module->setInstalled();
                        }
                    }
                }
            }
            @closedir($dh);
        }

        return true;
    }

    /**
     * Возвращает TRUE, если модуль описан в коллекции, иначе возвращает FALSE
     *
     * @param string $moduleName Имя модуля
     *
     * @return bool
     */
    protected function isExists (string $moduleName)
    {
        return $this->getModulesCollection()->isExists($moduleName);
    }

    /**
     * Обрабатывает значения опций по-умолчанию, если они заданы
     *
     * @param string $moduleName Полное имя модуля
     *
     * @return bool
     */
    protected function prepareDefaultOptions (string $moduleName)
    {
        $moduleName = strtolower($moduleName);
        if (!Modules::getInstance()->checkModuleName($moduleName))
        {
            return false;
        }

        if ($modulePath = $this->getModulePath($moduleName))
        {
            if (!file_exists($modulePath . '/default_options.php'))
            {
                //Если файла нет, то все нормально. Он не обязательно должен существовать
                return true;
            }

            //Сохраняем их
            \Ms\Core\Entity\Options\Options::getInstance()->setDefaultOptionsFromFile(
                $moduleName,
                $modulePath . '/default_options.php'
            )
            ;

            return true;
        }

        return false;
    }
}
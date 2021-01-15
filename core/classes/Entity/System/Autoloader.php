<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

use Ms\Core\Entity\Modules\Loader;
use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Entity\System\Autoloader
 * Автоматический загрузчик PHP классов
 */
class Autoloader extends Multiton
{
    /** @var array Список классов с их файлами для загрузки */
    protected $arAutoLoadClasses = [];

    /**
     * Защищенный конструктор класса Autoloader
     * Регистрирует обработчик автозагрузки файлов с классами
     */
    protected function __construct()
    {
        spl_autoload_register([$this, 'lazyLoad'],false,true);
    }

    /**
     * Обработчик автозагрузки файлов классов
     *
     * @param string $className Имя класса с пространством имен
     */
    public function lazyLoad ($className)
    {
        $this->normalizeClassName($className);
        if (isset($this->arAutoLoadClasses[$className]))
        {
            include_once($this->arAutoLoadClasses[$className]);
            return;
        }
        $className = str_replace ('\\\\', '\\', $className);

        $arClassName = explode('\\',$className);

        if (in_array('Components',$arClassName))
        {
            // msDebugNoAdmin($arClassName);
            if ($this->componentsAutoload ($className))
            {
                return;
            }
        }

        $settings = Settings::getInstance();

        if ($arClassName[0] == 'Ms' && $arClassName[1] == 'Core')
        {
            //Если нужен класс ядра
            $startPath = $settings->getCoreRoot() . '/classes';
            $this->psr4Autoload($startPath, str_replace('Ms\\Core\\','', $className));
        }
        else
        {
            $moduleName = strtolower($arClassName[0]) . '.' . $this->convertPascalCaseToSnakeCase($arClassName[1]);

            //Сначала пытаемся загрузить из локального расположения модулей
            $startPath = $settings->getLocalModulesRoot() . '/' . $moduleName . '/classes';
            $isLoaded = $this->psr4Autoload(
                $startPath,
                str_replace(
                    $arClassName[0] . '\\' . $arClassName[1] . '\\',
                    '',
                    $className
                ),
                $moduleName
            );

            if (!$isLoaded)
            {
                //Если класс не найден в локальном расположении, ищем его в системном
                $startPath = $settings->getModulesRoot() . '/' . $moduleName . '/classes';
                $this->psr4Autoload(
                    $startPath,
                    str_replace(
                        $arClassName[0] . '\\' . $arClassName[1] . '\\',
                        '',
                        $className
                    ),
                    $moduleName
                );
            }
        }
    }

    /**
     * Конвертирует строку из PascalCase в snake_case
     *
     * @param string $strPascalCase Строка в формате PascalCase
     *
     * @return string Строка в формате snake_case
     */
    public function convertPascalCaseToSnakeCase (string $strPascalCase)
    {
        return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $strPascalCase));
    }

    /**
     * Позволяет модулям дополнительно указывать какие классы в каких файлах лежат (для классов, не придерживающихся
     * стандарту PSR-4
     *
     * @param array $arClasses Массив классов и их файлов вида:
     *                         [
     *                             'Пространство имен и имя класса' => 'путь к файлу класса от корня',
     *                             'Пространство имен и имя класса 2' => 'путь к файлу класса 2 от корня'
     *                         ]
     */
    public function addAutoLoadClasses (array $arClasses)
    {
        if (empty($arClasses))
        {
            return;
        }
        foreach ($arClasses as $className => $path)
        {
            $this->normalizeClassName($className);
            if (file_exists($path))
            {
                $this->arAutoLoadClasses[$className] = $path;
            }
        }
    }

    /**
     * Загружает классы компонентов
     *
     * @param string $className Имя класса компонента
     *
     * @return bool
     */
    protected function componentsAutoload (string $className)
    {
        $arClassName = explode('\\',$className);
        if (!in_array('Components',$arClassName))
        {
            return false;
        }
        // msDebugNoAdmin($arClassName);

        $brand = strtolower($arClassName[0]);
        $module = $this->convertPascalCaseToSnakeCase($arClassName[1]);
        $n = count($arClassName) - 1;
        $arSysComponentClasses = [
            'ComponentDescription',
            'Component',
        ];
        if (in_array($arClassName[$n],$arSysComponentClasses) || $arClassName[$n-1] == 'Parameters')
        {
            return false;
        }
       $componentName = str_replace(
            '_',
            '.',
            $this->convertPascalCaseToSnakeCase(
                str_replace(
                    'Component',
                    '',
                    $arClassName[$n]
                )
            )
        );

        $settings = Settings::getInstance();
        $componentCorePath = $settings->getComponentsRoot();
        $componentLocalPath = $settings->getLocalComponentsRoot();
        $componentPath = $brand . '/' . $module . '.' . $componentName . '/class.php';
        // msDebugNoAdmin($componentLocalPath . '/' . $componentPath);
        // msDebugNoAdmin(file_exists($componentLocalPath . '/' . $componentPath));
        // msDebugNoAdmin($componentCorePath . '/' . $componentPath);
        // msDebugNoAdmin(file_exists($componentCorePath . '/' . $componentPath));

        if (file_exists($componentLocalPath . '/' . $componentPath))
        {
            include_once ($componentLocalPath . '/' . $componentPath);
            return true;
        }

        if (file_exists($componentCorePath . '/' . $componentPath))
        {
            include_once ($componentCorePath . '/' . $componentPath);
            return true;
        }

        return false;
    }

    /**
     * Загрузка файлов классов, придерживающихся стандарта PSR-4
     *
     * @param string      $startPath    Начальный путь загрузки
     * @param string      $className    Имя класса с пространством имен
     * @param string|null $moduleName   Имя модуля, если класс принадлежит модулю
     *
     * @return bool
     */
    protected function psr4Autoload (string $startPath, string $className, string $moduleName = null)
    {
        if (!is_null($moduleName))
        {
            try
            {
                if (!Loader::includeModule($moduleName))
                {
                    return false;
                }
            }
            catch (SystemException $e)
            {
                return false;
            }
        }

        $filePath = $startPath . '/' . str_replace('\\','/', $className) . '.php';
        if (file_exists($filePath))
        {
            include_once ($filePath);
            return true;
        }

        return false;
    }

    /**
     * Нормализует имя класса с пространством имен, убирая первый символ, если он равен '\'
     *
     * @param string &$className Имя класса с пространством имен
     *
     * @return void
     */
    protected function normalizeClassName (string &$className)
    {
        //Удаляем возможный первый символ \ перед namespace
        if ($className[0] == '\\')
        {
            $className = substr ($className, 1, strlen ($className));
        }
    }
}
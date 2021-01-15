<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

if (!class_exists('MsCoreUnitTestsAutoloader'))
{
    class MsCoreUnitTestsAutoloader
    {
        protected $arSettings = [];

        public function __construct ()
        {
            $this->arSettings['BASE_PATH'] = dirname(__FILE__) . '/../../..';
            $this->arSettings['MS_PATH'] = $this->arSettings['BASE_PATH'] . '/ms';
            $this->arSettings['MS_CORE_PATH'] = $this->arSettings['MS_PATH'] . '/core';
            $this->arSettings['MS_MODULES_PATH'] = $this->arSettings['MS_PATH'] . '/modules';
            $this->arSettings['MS_COMPONENTS_PATH'] = $this->arSettings['MS_PATH'] . '/components';
            $this->arSettings['LOCAL_PATH'] = $this->arSettings['BASE_PATH'] . '/local';
            $this->arSettings['LOCAL_MODULES_PATH'] = $this->arSettings['LOCAL_PATH'] . '/modules';
            $this->arSettings['LOCAL_COMPONENTS_PATH'] = $this->arSettings['LOCAL_PATH'] . '/components';

            spl_autoload_register([$this, 'autoloader'],false,true);
        }

        public function autoloader ($className)
        {
            if (mb_substr($className,0, 1, 'UTF-8') == "\\")
            {
                $className = mb_substr ($className, 1, null, 'UTF-8');
            }

            $className = str_replace ('\\\\', '\\', $className);

            $arClassName = explode('\\',$className);

            if ($arClassName[3] == 'Components')
            {
                // msDebugNoAdmin($arClassName);
                if ($this->componentsAutoload ($className))
                {
                    return;
                }
            }

            if ($arClassName[0] == 'Ms' && $arClassName[1] == 'Core')
            {
                //Если нужен класс ядра
                $startPath = $this->arSettings['MS_CORE_PATH'] . '/classes';
                $this->psr4Autoload($startPath, str_replace('Ms\\Core\\','', $className));
            }
            else
            {
                $moduleName = strtolower($arClassName[0]) . '.' . $this->convertPascalCaseToSnakeCase($arClassName[1]);

                //Сначала пытаемся загрузить из локального расположения модулей
                $startPath = $this->arSettings['LOCAL_MODULES_PATH'] . '/' . $moduleName . '/classes';
                $isLoaded = $this->psr4Autoload(
                    $startPath,
                    str_replace(
                        $arClassName[0] . '\\' . $arClassName[1] . '\\',
                        '',
                        $className
                    )
                );

                if (!$isLoaded)
                {
                    //Если класс не найден в локальном расположении, ищем его в системном
                    $startPath = $this->arSettings['MS_MODULES_PATH'] . '/' . $moduleName . '/classes';
                    $this->psr4Autoload(
                        $startPath,
                        str_replace(
                            $arClassName[0] . '\\' . $arClassName[1] . '\\',
                            '',
                            $className
                        )
                    );
                }
            }

        }

        protected function psr4Autoload (string $startPath, string $className)
        {
            $filePath = $startPath . '/' . str_replace('\\','/', $className) . '.php';
            if (file_exists($filePath))
            {
                include_once ($filePath);
                return true;
            }

            return false;
        }

        protected function componentsAutoload (string $className)
        {
            $arClassName = explode('\\',$className);
            if ($arClassName[3] != 'Components')
            {
                return false;
            }

            $brand = strtolower($arClassName[0]);
            $module = $this->convertPascalCaseToSnakeCase($arClassName[1]);
            $componentName = str_replace(
                '_',
                '.',
                $this->convertPascalCaseToSnakeCase(
                    str_replace(
                        'Component',
                        '',
                        $arClassName[4]
                    )
                )
            );

            $componentPath = $brand . '/' . $module . '.' . $componentName . '/class.php';

            if (file_exists($this->arSettings['MS_COMPONENTS_PATH'] . '/' . $componentPath))
            {
                include_once ($this->arSettings['MS_COMPONENTS_PATH'] . '/' . $componentPath);
                return true;
            }

            if (file_exists($this->arSettings['LOCAL_COMPONENTS_PATH'] . '/' . $componentPath))
            {
                include_once ($this->arSettings['LOCAL_COMPONENTS_PATH'] . '/' . $componentPath);
                return true;
            }

            return false;
        }

        protected function convertPascalCaseToSnakeCase ($string)
        {
            return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $string));
        }
    }
}

new MsCoreUnitTestsAutoloader();

require_once (dirname(__FILE__).'/../tools/tools.main.php');


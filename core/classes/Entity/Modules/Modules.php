<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules;

use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\Errors\ErrorCollection;
use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Entity\System\Settings;
use Ms\Core\Exceptions\Arguments\ArgumentException;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\Modules\ModuleDoesNotExistsException;
use Ms\Core\Entity\Modules\Versions\Version;
use Ms\Core\Entity\Modules\Versions\VersionComparator;
use Ms\Core\Lib\IO\Files;
use Ms\Core\Lib\Tools;

/**
 * Класс Ms\Core\Lib\Modules
 * Класс для работы с модулями
 */
class Modules extends Multiton
{
    protected $errorCollection = null;

    protected function __construct ()
    {
        $this->errorCollection = new ErrorCollection();
    }

    /**
     * Проверяет соответствие имени модуля установленным правилам
     *
     * Правила:<br><ol>
     * <li>Имя модуля должно быть указано в нижнем регистре</li>
     * <li>Имя модуля представляет собой вид brand.name, где brand - код разработчика, а name код модуля</li>
     * <li>Имя модуля может состоять не более, чем из 100 символов, включая brand и '.' (точку-разделитель)</li>
     * <li>Правила наименования бренда модуля:<br><ul>
     *  <li>Код бренда должен начинаться с символа</li>
     *  <li>В коде бренда должен быть хотя бы 1 символ</li>
     *  <li>В коде бренда разрешено использовать латинские символы и цифры</li></ul></li>
     * <li>Правила наименования кода модуля:<br><ul>
     *  <li>Код модуля должен начинаться с символа</li>
     *  <li>В коде модуля должен быть хотя бы 1 символ</li>
     *  <li>В коде модуля разрешено использовать латинские символы, цифры и знак подчеркивания '_'</li></ul></li></ol>
     *
     * @param string &$moduleName Имя модуля
     *
     * @return bool
     * @unittest
     */
    public function checkModuleName (string &$moduleName)
    {
        $moduleName = strtolower($moduleName);

        //Если ядро, все в порядке
        if ($moduleName === 'core' || $moduleName === 'ms.core')
        {
            return true;
        }

        //Проверяем на наличае бренда
        if (strpos($moduleName, '.') === false)
        {
            return false;
        }

        //Проверяем на допустимую длинну
        if (strlen($moduleName) > Settings::MAX_LENGTH_MODULE_NAME)
        {
            return false;
        }

        //Проверяем на использование только разрешенных символов и верный синтаксис
        if (!preg_match(Settings::MODULE_NAME_REGULAR_EXPRESSION, $moduleName))
        {
            return false;
        }

        return true;
    }

    /**
     * Возвращает TRUE если текущая версия больше или равна требуемой
     * $versionCurrent >= $versionRequired
     *
     * @param string $versionCurrent  Текущая версия модуля. Формат "XX.XX.XX"
     * @param string $versionRequired Требуемая версия модуля. Формат "XX.XX.XX"
     *
     * @return bool
     * @unittest
     */
    public function checkVersion ($versionCurrent, $versionRequired)
    {
        try
        {
            return $this->isCorrectVersion('>=' . $versionRequired, $versionCurrent, false);
        }
        catch (ArgumentException $e)
        {
            return true;
        }
        /*        if (function_exists('version_compare'))
                {
                    $res = version_compare($versionCurrent, $versionRequired);
                    if ($res >= 0)
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    $arr1 = explode(".", $versionCurrent);
                    $arr2 = explode(".", $versionRequired);
                    if (intval($arr2[0]) > intval($arr1[0]))
                    {
                        return false;
                    }
                    elseif (intval($arr2[0]) < intval($arr1[0]))
                    {
                        return true;
                    }
                    else
                    {
                        if (intval($arr2[1]) > intval($arr1[1]))
                        {
                            return false;
                        }
                        elseif (intval($arr2[1]) < intval($arr1[1]))
                        {
                            return true;
                        }
                        else
                        {
                            if (intval($arr2[2]) > intval($arr1[2]))
                            {
                                return false;
                            }
                            elseif (intval($arr2[2]) < intval($arr1[2]))
                            {
                                return true;
                            }
                            else
                            {
                                return true;
                            }
                        }
                    }
                }*/
    }

    /**
     * Проверяет синтаксис выражения версии модуля
     *
     * @param string  $versionExpression Выражение версии модуля
     * @param array  &$arCheck           Распарсенное значение версии
     *
     * @return bool
     * @unittest
     */
    public function checkVersionExpression (string $versionExpression, array &$arCheck = [])
    {
        $versionExpression = str_replace(', ', ',', $versionExpression);
        //Если строка содержит запятые, нужно каждое выражение проверить отдельно
        if (strpos($versionExpression, ',') !== false)
        {
            $arVersionExpression = explode(',', $versionExpression);
            if (!empty($arVersionExpression))
            {
                $bOk = false;
                foreach ($arVersionExpression as $checkVersion)
                {
                    $bOk = $this->checkVersionExpression($checkVersion, $arCheck);
                    if (!$bOk)
                    {
                        return false;
                    }
                }

                if ($bOk)
                {
                    return true;
                }
            }

            return false;
        }
        //Если строка содержит пробел, нужно каждое выражение проверить отдельно
        if (strpos($versionExpression, ' ') !== false)
        {
            $arVersionExpression = explode(' ', $versionExpression);
            if (!empty($arVersionExpression))
            {
                $bOk = false;
                foreach ($arVersionExpression as $checkVersion)
                {
                    $bOk = $this->checkVersionExpression($checkVersion, $arCheck);
                    if (!$bOk)
                    {
                        return false;
                    }
                }

                if ($bOk)
                {
                    return true;
                }
            }

            return false;
        }
        //Если строка содержит лишь 1 выражение, проверяем его

        $arChecked = [];
        $versionExpression = str_replace('v.', '', $versionExpression);
        $versionExpression = str_replace('v', '', $versionExpression);

        //<editor-fold desc="Ищем двухсимвольные операторы: >=, <=, !=">
        $temp = substr($versionExpression, 0, 2);
        switch (strtolower($temp))
        {
            case '>=':
            case '<=':
            case '!=':
                $arChecked['MODIFIER'] = strtolower($temp);
                $versionExpression = str_replace($temp, '', $versionExpression);
                break;
        }
        //</editor-fold>

        //<editor-fold desc="Ищем односимвольные операторы: >, <, =, ~, ^, !, пропуская цифры и возвращая false, если другие символы">
        $temp = substr($versionExpression, 0, 1);
        switch (strtolower($temp))
        {
            case '>':
            case '<':
            case '=':
            case '~':
            case '^':
            case '!':
                $arChecked['MODIFIER'] = strtolower($temp);
                $versionExpression = str_replace($temp, '', $versionExpression);
                break;
            case '0':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
            case '9':
                break;
            default://Если какой-то другой символ - ошибка: возвращаем false
                $this->errorCollection->addErrorEasy(
                    'В выражении "' . $versionExpression . '" использован запрещенный символ "' . $temp . '"',
                    'CHECK_VERSION_WRONG_SYMBOL'
                );

                return false;
        }
        //</editor-fold>

        if (!array_key_exists('MODIFIER', $arChecked))
        {
            $arChecked['MODIFIER'] = '';
        }

        //<editor-fold desc="Old code">
        /*        $arVersion = explode('.', $versionExpression);
                if (!is_array($arVersion) || empty($arVersion) || strlen($arVersion[0]) == 0)
                {
                    print_r($arVersion);
                    return false;
                }
                //Первый символ может быть: >, <, =, ~, ^, ! или отсутствовать
                $arChecked['MODIFIER'] = '';
                $sFirstSecond = substr($arVersion[0], 0, 2);
                $sFirst = substr($arVersion[0], 0, 1);
                switch (strtolower($sFirstSecond))
                {
                    case '>=':
                    case '<=':
                        $arChecked['MODIFIER'] = $sFirstSecond;
                        $arVersion[0] = str_replace($sFirstSecond, '', $arVersion[0]);
                        break;
                    case '!=':
                        $arChecked['MODIFIER'] = '!';
                        break;
                }
                switch (strtolower($sFirst))
                {
                    case '>':
                    case '<':
                    case '=':
                    case '~':
                    case '^':
                    case '!':
                        $arChecked['MODIFIER'] = $sFirst;
                        $arVersion[0] = str_replace($sFirst, '', $arVersion[0]);
                        break;
                    case '0':
                    case '1':
                    case '2':
                    case '3':
                    case '4':
                    case '5':
                    case '6':
                    case '7':
                    case '8':
                    case '9':
                        break;
                    default: //Если какой-то другой символ - ошибка: возвращаем false
                        return false;
                }*/
        //</editor-fold>

        //Разбираем версию на части
        $arVersion = explode('.', $versionExpression);

        if (array_key_exists(0, $arVersion))
        {
            if ($arVersion[0] == '*' || $arVersion[0] == '')
            {
                $this->errorCollection->addErrorEasy(
                    'Недопустимо, чтобы мажорная версия "' . $arVersion[0] . '" была пустая, либо '
                    . 'был использован символ подстановки "*"',
                    'CHECK_VERSION_MAJOR_WRONG_SYMBOL'
                );

                return false;
            }
            else
            {
                $arChecked['MAJOR'] = (string)$arVersion[0];
            }
        }
        else
        {
            $this->errorCollection->addErrorEasy(
                'Недопустимо, чтобы мажорная версия "' . $arVersion[0] . '" была пустая',
                'CHECK_VERSION_MAJOR_EMPTY'
            );

            return false;
        }

        if (array_key_exists(1, $arVersion))
        {
            if ($arVersion[1] == '')
            {
                $this->errorCollection->addErrorEasy(
                    'Недопустимо, чтобы минорная версия "' . $arVersion[1] . '" была пустая',
                    'CHECK_VERSION_MINOR_EMPTY'
                );

                return false;
            }
            else
            {
                $arChecked['MINOR'] = (string)$arVersion[1];
            }
        }
        else
        {
            $this->errorCollection->addErrorEasy(
                'Недопустимо, чтобы минорная версия "' . $arVersion[1] . '" была пустая',
                'CHECK_VERSION_MINOR_EMPTY'
            );

            return false;
        }

        if (array_key_exists(2, $arVersion))
        {
            $arChecked['PATCH'] = (string)$arVersion[2];
        }
        else
        {
            $arChecked['PATCH'] = '';
        }


        /*
         * Варианты указания версий:
         * 1. Точное соответствие (1.2.3)
         * 2. Диапазоны с операторами сравнения (<1.2.3)
         * 3. Комбинации этих операторов (>1.2.3 <1.3)
         * 4. Последняя доступная (1.2.*)
         * 5. Символ тильды (~1.2.3) включает все версии до 1.3 не включительно
         * 6. Знак вставки (^1.2.3) означает "опасаться глобальных изменений" и включает все версии
         * вплоть до 2.0 не включительно
         *
         * Отсюда следуют следующие правила определения ошибочного написания выражений версий:
         * 1. Если МАЖОРНАЯ версия равна 0 и версия ПАТЧА отсутствует, равна 0 или *, МИНОРНАЯ версия не может быть равна 0
         * 2. Если МАЖОРНАЯ версия равна 0 МИНОРНАЯ версия не может быть равна *
         * 3. Если МИНОРНАЯ версия равна *, версия ПАТЧА не должна быть указана
         * 4. Если установлен ОПЕРАТОР, то МИНОРНАЯ версия и версия ПАТЧА не могут быть равны *
         */

        //1. Если МАЖОРНАЯ версия равна 0 и версия ПАТЧА отсутствует, равна 0 или *, МИНОРНАЯ версия не может быть равна 0
        if (
            $arChecked['MAJOR'] === '0'
            && (
                $arChecked['PATCH'] === ''
                || $arChecked['PATCH'] === '0'
                || $arChecked['PATCH'] === '*'
            )
            && $arChecked['MINOR'] === '0'
        )
        {
            $this->errorCollection->addErrorEasy(
                'Если МАЖОРНАЯ версия равна 0 и версия ПАТЧА отсутствует, равна 0 или *, '
                . 'МИНОРНАЯ версия не может быть равна 0',
                'CHECK_VERSION_RULE_1'
            );

            return false;
        }
        //2. Если МАЖОРНАЯ версия равна 0 МИНОРНАЯ версия не может быть равна *
        if (
            $arChecked['MAJOR'] === '0'
            && $arChecked['MINOR'] === '*'
        )
        {
            $this->errorCollection->addErrorEasy(
                'Если МАЖОРНАЯ версия равна 0 МИНОРНАЯ версия не может быть равна *',
                'CHECK_VERSION_RULE_2'
            );

            return false;
        }
        //3. Если МИНОРНАЯ версия равна *, версия ПАТЧА не должна быть указана
        if (
            $arChecked['MINOR'] === '*'
            && $arChecked['PATCH'] !== ''
        )
        {
            $this->errorCollection->addErrorEasy(
                'Если МИНОРНАЯ версия равна *, версия ПАТЧА не должна быть указана',
                'CHECK_VERSION_RULE_3'
            );

            return false;
        }
        //4. Если установлен ОПЕРАТОР, то МИНОРНАЯ версия и версия ПАТЧА не могут быть равны *
        if (
            $arChecked['MODIFIER'] !== ''
            && (
                $arChecked['MINOR'] === '*'
                || $arChecked['PATCH'] === '*'
            )
        )
        {
            $this->errorCollection->addErrorEasy(
                'Если установлен ОПЕРАТОР, то МИНОРНАЯ версия и версия ПАТЧА не могут быть равны *',
                'CHECK_VERSION_RULE_4'
            );

            return false;
        }

        $arCheck[] = $arChecked;

        return true;
    }

    /**
     * Очищает коллекцию ошибок
     *
     * @return $this
     * @unittest
     */
    public function clearErrorCollection ()
    {
        $this->errorCollection = new ErrorCollection();

        return $this;
    }

    /**
     * Возвращает коллекцию ошибок
     *
     * @return ErrorCollection
     * @unittest
     */
    public function getErrorCollection ()
    {
        return $this->errorCollection;
    }

    /**
     * Возвращает имя файла класса по его Пространству имен
     *
     * @param string $sClassNamespace Пространство имен класса
     *
     * @return bool|string
     * @unittest
     */
    public function getFilePathByClassNamespace (string $sClassNamespace)
    {
        $filename = Application::getInstance()->getDocumentRoot() . '/ms';
        //Удаляем возможный первый символ \ перед namespace
        if (substr($sClassNamespace, 0, 1) == '\\')
        {
            $sClassNamespace = substr($sClassNamespace, 1, strlen($sClassNamespace));
        }
        $sClassNamespace = str_replace('\\\\', '\\', $sClassNamespace);
        $arName = explode('\\', $sClassNamespace);
        if (strtolower($arName[1]) == 'core')
        {
            $filename .= '/core';
        }
        elseif (isset($arName[0]) && isset($arName[1]))
        {
            $filename .= '/modules/' . strtolower($arName[0]) . '.'
                         . Application::getInstance()->convertPascalCaseToSnakeCase($arName[1]);
        }
        else
        {
            return false;
        }
        unset($arName[0]);
        unset($arName[1]);
        $filename .= '/classes';
        foreach ($arName as $name)
        {
            $filename .= '/' . $name;
        }

        $filename .= '.php';

        if (file_exists($filename))
        {
            return $filename;
        }
        else
        {
            return false;
        }
    }

    /**
     * Возвращает имя модуля по namespace
     *
     * @param string $namespace
     *
     * @return null|string
     * @unittest
     */
    public function getModuleFromNamespace (string $namespace)
    {
        //Удаляем возможный первый символ \ перед namespace
        if ($namespace[0] == '\\')
        {
            $namespace = substr($namespace, 1, strlen($namespace));
        }
        $namespace = str_replace('\\\\', '\\', $namespace);
        $arName = explode('\\', $namespace);
        if (strtolower($arName[1]) == 'core')
        {
            return 'core';
        }
        elseif (isset($arName[0]) && isset($arName[1]))
        {
            return strtolower($arName[0]) . '.' . Application::getInstance()->convertPascalCaseToSnakeCase($arName[1]);
        }
        else
        {
            return null;
        }
    }

    /**
     * Возвращает имя модуля по Пространству имен класса
     *
     * @param string $sClassNamespace Пространство имен класса
     *
     * @return bool|string
     * @unittest
     */
    public function getModuleNameByClassNamespace (string $sClassNamespace)
    {
        //Удаляем возможный первый символ \ перед namespace
        if ($sClassNamespace[0] == '\\')
        {
            $sClassNamespace = substr($sClassNamespace, 1, strlen($sClassNamespace));
        }
        $sClassNamespace = str_replace('\\\\', '\\', $sClassNamespace);
        $arName = explode('\\', $sClassNamespace);
        if (strtolower($arName[1]) == 'core')
        {
            return 'core';
        }
        elseif (isset($arName[0]) && isset($arName[1]))
        {
            return strtolower($arName[0]) . '.' . Application::getInstance()->convertPascalCaseToSnakeCase($arName[1]);
        }
        else
        {
            return false;
        }
    }

    /**
     * Возвращает Namespace модуля вида [Brand]\[ModuleName]\
     *
     * @param string $moduleName - полное имя модуля [brand].[code]
     *
     * @return bool|string
     * @unittest
     */
    public function getModuleNamespace (string $moduleName)
    {
        $moduleName = strtolower($moduleName);

        if ($moduleName == 'core' || $moduleName == 'ms.core' || $moduleName == '')
        {
            return 'Ms\Core\\';
        }

        //Если имя модуля соответствует стандартам и нормально разобралось
        if ($arModule = $this->parseModuleName($moduleName))
        {
            $namespace = Tools::setFirstCharToBig($arModule['BRAND']) . '\\';
            $module = Application::getInstance()->convertSnakeCaseToPascalCase($arModule['MODULE']);
            $namespace .= $module . '\\';

            return $namespace;
        }

        return false;
    }

    /**
     * Возвращает namespace для таблиц модуля
     *
     * @param string $moduleName
     *
     * @return bool|string
     * @unittest
     */
    public function getModuleNamespaceTables (string $moduleName)
    {
        $moduleName = strtolower($moduleName);

        if ($moduleName == 'core' || $moduleName == 'ms.core' || $moduleName == '')
        {
            return 'Ms\Core\Tables\\';
        }

        //Если namespace модуля успешно получен
        if ($namespace = $this->getModuleNamespace($moduleName))
        {
            $namespace .= 'Tables\\';

            return $namespace;
        }

        return false;
    }

    /**
     * Возвращает список файлов таблиц модуля
     *
     * @param string $moduleName
     *
     * @return array|bool
     * @unittest
     */
    public function getModuleTableFiles (string $moduleName)
    {
        if (!$this->checkModuleName($moduleName))
        {
            return false;
        }

        if ($moduleName == 'core' || $moduleName == 'ms.core' || $moduleName == '')
        {
            $arTables = Files::getListFiles($this->getPathToModuleTablesFiles($moduleName), ['.readme']);

            return ($arTables) ? $arTables : false;
        }

        if (Loader::issetModule($moduleName))
        {
            $arTables = Files::getListFiles(
                $this->getPathToModuleTablesFiles($moduleName),
                ['.readme']
            );

            return ($arTables) ? $arTables : false;
        }

        return false;
    }

    /**
     * Возвращает список имен таблиц модуля, либо false
     *
     * @param string $moduleName полное имя модуля
     *
     * @return array|bool
     * @unittest
     */
    public function getModuleTableNames (string $moduleName)
    {
        if (!$strNamespace = $this->getModuleNamespaceTables($moduleName))
        {
            return false;
        }
        if ($arTableFiles = $this->getModuleTableFiles($moduleName))
        {
            $arNames = [];
            foreach ($arTableFiles as $fileTable)
            {
                $className = $this->getTableClassByFileName($fileTable);

                $runClass = $strNamespace . $className;
                $orm = ORMController::getInstance(new $runClass());
                $arNames[] = $orm->getTableName();
            }
            if (!empty($arNames))
            {
                return $arNames;
            }
        }

        return false;
    }

    /**
     * Возвращает путь к файлам модуля
     *
     * @param string $moduleName Имя модуля
     *
     * @return bool|string
     * @unittest
     */
    public function getPathToModule (string $moduleName)
    {
        if ($this->checkModuleName($moduleName))
        {
            if ($moduleName == 'core' || $moduleName == 'ms.core' || $moduleName == '')
            {
                return Application::getInstance()->getSettings()->getCoreRoot();
            }

            return Loader::getInstance()->getModulePath($moduleName);
        }

        return false;
    }

    /**
     * Возвращает путь к JavaScript файлам модуля
     *
     * @param string $moduleName Имя модуля
     *
     * @return bool|string
     * @unittest
     */
    public function getPathToModuleJs (string $moduleName)
    {
        $path = $this->getPathToModule($moduleName);
        if ($path)
        {
            return $path . '/js';
        }

        return false;
    }

    /**
     * Возвращает путь к файлам с описанием  таблиц модуля
     *
     * @param string $moduleName
     *
     * @return bool|string
     * @unittest
     */
    public function getPathToModuleTablesFiles (string $moduleName)
    {
        $pathToModule = $this->getPathToModule($moduleName);
        if ($pathToModule)
        {
            return $pathToModule . '/classes/Tables';
        }

        return false;
    }

    /**
     * Возвращает класс таблицы по имени файла с описанием таблицы
     *
     * @param string $filename
     *
     * @return string
     * @unittest
     */
    public function getTableClassByFileName (string $filename)
    {
        return str_replace('.php', '', $filename);
    }

    /**
     * Возвращает полный путь к директории загрузки пользовательских файлов модуля
     *
     * @param string $moduleName Имя модуля
     *
     * @return string|bool  Путь, либо false
     * @unittest
     */
    public function getUpload (string $moduleName)
    {
        $moduleName = strtolower($moduleName);
        if (!is_null($moduleName))
        {
            $uploadDir = Settings::getInstance()->getUploadDir();

            return $uploadDir . '/modules/' . $moduleName;
        }
        else
        {
            return false;
        }
    }

    /**
     * Запускает файл установки модуля, возвращая результат запуска
     *
     * @param string $moduleName
     *
     * @return Installer
     * @throws ModuleDoesNotExistsException
     * @throws ArgumentTypeException
     * @unittest
     */
    public function installModule (string $moduleName)
    {
        $installer = new \Ms\Core\Entity\Modules\Installer($moduleName);

        return $installer->install();
    }

    /**
     * Проверяет подходит ли текущая версия выражению требуемой версии
     *
     * @param string $needVersionExpression Выражение требуемой версии
     * @param string $currentVersion        Текущая версия
     * @param bool   $bThrow                Выбрасывать ли исключения, по умолчанию - TRUE - выбрасывать
     *
     * @return bool
     * @throws ArgumentException
     * @unittest
     */
    public function isCorrectVersion (string $needVersionExpression, string $currentVersion, bool $bThrow = true)
    {
        $currentVersion = new Version($currentVersion);
        // Если строка содержит ",", нужно каждую указанную версию проверять отдельно,
        // если хотя бы одна проверка будет успешна - общая проверка так же успешна
        if (strpos($needVersionExpression, ', ') !== false)
        {
            $arExpressions = explode(', ', $needVersionExpression);
        }
        elseif (strpos($needVersionExpression, ',') !== false)
        {
            $arExpressions = explode(',', $needVersionExpression);
        }
        else
        {
            $arExpressions = [$needVersionExpression];
        }
        foreach ($arExpressions as $expression)
        {
            $expression = trim($expression);

            // Если строка содержит пробел, необходимо проверить диапазон значений (раз диапазон, то берем первых два выражения)
            // Нужно, чтобы текущая версия входила в указанный диапазон
            if (strpos($expression, ' ') !== false)
            {
                list($firstExpression, $secondExpression) = explode(' ', $expression, 2);
                try
                {
                    $arException = ['FILE' => __FILE__, 'LINE' => __LINE__];
                    if ($this->isVersionInRange($currentVersion, trim($firstExpression), trim($secondExpression)))
                    {
                        return true;
                    }
                }
                catch (ArgumentException $e)
                {
                    if ($bThrow)
                    {
                        throw new ArgumentException(
                            $e->getMessage(),
                            $e->getParameter(),
                            $arException['FILE'],
                            $arException['LINE'],
                            $e->getCode(),
                            $e
                        );
                    }
                    else
                    {
                        continue;
                    }
                }

                continue;
            }

            // Проверяем обычное выражение
            $ver = new Version($expression);
            try
            {
                $arException = ['FILE' => __FILE__, 'LINE' => __LINE__];
                $res = VersionComparator::getInstance()
                                        ->compare(
                                            $currentVersion,
                                            $ver->getOperator(),
                                            $ver
                                        )
                ;
            }
            catch (ArgumentException $e)
            {
                if ($bThrow)
                {
                    throw new ArgumentException(
                        $e->getMessage(),
                        $e->getParameter(),
                        $arException['FILE'],
                        $arException['LINE'],
                        $e->getCode(),
                        $e
                    );
                }
                else
                {
                    continue;
                }
            }

            if ($res)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Разбирает имя модуля на бренд и имя
     *
     * Разбирает имя модуля на бренд и имя и возвращает в массиве с ключами BRAND и MODULE соответственно.
     * В случае ошибки возвращает false
     *
     * @param string $moduleName Имя модуля
     *
     * @return array|bool
     * @unittest
     */
    public function parseModuleName (string $moduleName)
    {
        $moduleName = strtolower($moduleName);

        //Если имя модуля соответствует стандартам
        if ($this->checkModuleName($moduleName))
        {
            $matches = null;
            preg_match(Settings::MODULE_NAME_REGULAR_EXPRESSION, $moduleName, $matches);
            if (isset($matches[1]) && isset($matches[2]))
            {
                return ['BRAND' => $matches[1], 'MODULE' => $matches[2]];
            }
        }

        return false;
    }

    /**
     * Запускает файл удаления модуля, возвращая результат запуска
     *
     * @param string  $moduleName          Имя модула
     * @param bool    $bDeleteModuleTables Удалять ли таблицы модуля
     * @param array  &$arParams            Дополнительные параметры
     *
     * @return Installer
     * @throws ArgumentTypeException
     * @throws ModuleDoesNotExistsException
     * @unittest
     */
    public function unInstallModule (string $moduleName, bool $bDeleteModuleTables = true, array $arParams = [])
    {
        $installer = new Installer($moduleName);

        return $installer->unInstall($bDeleteModuleTables, $arParams);
    }

    /**
     * Возвращает true, если текущая версия входит в диапазон
     *
     * @param Version $currentVersion   Текущая версия
     * @param string  $firstExpression  Первое выражение диапазона
     * @param string  $secondExpression Второй веражение диапазона
     *
     * @return bool
     * @throws ArgumentException
     */
    protected function isVersionInRange (
        Version $currentVersion,
        string $firstExpression,
        string $secondExpression
    ) {
        $firstExpression = new Version($firstExpression);
        $secondExpression = new Version($secondExpression);
        $res1 = VersionComparator::getInstance()
                                 ->compare(
                                     $currentVersion,
                                     $firstExpression->getOperator(),
                                     $firstExpression
                                 )
        ;
        $res2 = VersionComparator::getInstance()
                                 ->compare(
                                     $currentVersion,
                                     $secondExpression->getOperator(),
                                     $secondExpression
                                 )
        ;

        return ($res1 && $res2);
    }
}
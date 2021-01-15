<?php
/**
 * Функции обертки основного функционала системы
 *
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

if (!function_exists('IncludeLangFile'))
{
    /**
     * Подключает языковой файл для указанного файла
     *
     * @param string $path Путь файлу, требующему локализацию
     *
     * @return bool
     */
    function IncludeLangFile ($path)
    {
        return \Ms\Core\Entity\Localization\Loc::getInstance()->includeLocalizationForThisFile($path);
    }
}

if (!function_exists('IncludeLocalizationFile'))
{
    /**
     * Подключает указанный языковой файл
     *
     * @param string $path Путь к языковому файлу
     *
     * @return bool|\Ms\Core\Entity\Localization\MessagesCollection
     */
    function IncludeLocalizationFile ($path)
    {
        return \Ms\Core\Entity\Localization\Loc::getInstance()->includeLocalizationFile($path);
    }
}

if (!function_exists('GetMessage'))
{
    /**
     * Возвращает локализованный текст, заменяя теги указанными значениями
     *
     * @param string $name      Код сообщения
     * @param array  $arReplace Массив замен вида код_тега=>замена
     *
     * @return string
     */
    function GetMessage (string $name, array $arReplace = [])
    {
        return \Ms\Core\Entity\Localization\Loc::getInstance()->getMessage($name, $arReplace);
    }
}

if (!function_exists('GetModuleMessage'))
{
    /**
     * Функция обертка для getMessage для модулей.
     * Собирает код текстового сообщения из префикса, названия модуля и кода сообщения
     *
     * @param string $module    Имя модуля
     * @param string $name      Код локализованной фразы
     * @param array  $arReplace Массив замен
     *
     * @return mixed
     */
    function GetModuleMessage (string $module, string $name, array $arReplace = [])
    {
        return \Ms\Core\Entity\Localization\Loc::getInstance()->getModuleMessage($module, $name, $arReplace);
    }
}

if (!function_exists('GetCoreMessage'))
{
    /**
     * Возвращает локализованный текст для ядра, заменяя теги указанными значениями
     *
     * @param string $name      Код языковой фразы
     * @param array  $arReplace Массив замен
     *
     * @return string
     */
    function GetCoreMessage (string $name, array $arReplace = [])
    {
        return \Ms\Core\Entity\Localization\Loc::getInstance()->getCoreMessage($name, $arReplace);
    }
}

if (!function_exists('GetComponentMessage'))
{
    /**
     * Возвращает локализованный текст для компонента, заменяя теги указанными значениями
     *
     * @param string $fullComponentName Полное имя компонента вида бренд:компонент
     * @param string $name              Код языковой фразы
     * @param array  $arReplace         Массив замен
     *
     * @return string
     */
    function GetComponentMessage (string $fullComponentName, string $name, array $arReplace = [])
    {
        return \Ms\Core\Entity\Localization\Loc::getInstance()->getComponentMessage
        (
            $fullComponentName,
            $name,
            $arReplace
        );
    }
}

if (!function_exists('IssetModule'))
{
    /**
     * Возвращает TRUE, если модуль существует и его версия соответствует условию, иначе FALSE
     *
     * @param string      $moduleName            Имя модуля
     * @param string|null $needVersionExpression Выражение требуемой версии, не обязательное
     *
     * @return bool
     */
    function IssetModule (string $moduleName, string $needVersionExpression = null)
    {
        return \Ms\Core\Entity\Modules\Loader::issetModule($moduleName, $needVersionExpression);
    }
}

if (!function_exists('IncludeModule'))
{
    /**
     * Инициирует указанный модуль.
     * В настоящее время не требуется явно инициировать модуль, так как это происходит автоматически при первом
     * вызове любого метода любого класса.
     *
     * @param string $moduleName Имя модуля
     *
     * @return bool
     * @throws \Ms\Core\Exceptions\Modules\LoaderException
     * @throws \Ms\Core\Exceptions\Modules\ModuleDoesNotExistsException
     */
    function IncludeModule (string $moduleName)
    {
        return \Ms\Core\Entity\System\Modules\Loader::includeModule($moduleName);
    }
}

if (!function_exists('DebMess'))
{
    /**
     * Добавляет сообщение в лог отладки. При этом заменяет в тексте сообщения все вхождения из массива замен arReplace.
     * Если третьим параметром передана коллекция ошибок, добавляет ошибку в нее.
     *
     * @param string                                      $strMessage
     * @param array                                       $arReplace
     * @param \Ms\Core\Entity\Errors\ErrorCollection|null $errorCollection
     */
    function DebMess (string $strMessage, array $arReplace = [], \Ms\Core\Entity\Errors\ErrorCollection $errorCollection = null)
    {
        $logger = new \Ms\Core\Entity\Errors\FileLogger('system','debug');
        $logger->addMessage($strMessage, $arReplace);
        if (!is_null($errorCollection))
        {
            $errorCollection->addError(
                new \Ms\Core\Entity\Errors\Error(
                    \Ms\Core\Lib\Tools::strReplace(
                        $arReplace,
                        $strMessage
                    )
                )
            );
        }
    }
}

if (!function_exists('ms_sessid'))
{
    /**
     * Возвращает ID текущей сессии
     *
     * @return string
     */
    function ms_sessid ()
    {
        return \Ms\Core\Entity\System\Application::getInstance()->getSession()->getSID();
    }
}


<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Localization;

use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Entity\Modules\Modules;

/**
 * Класс Ms\Core\Entity\Localization
 * Локализация ядра и модулей
 */
class Loc extends Multiton
{
    protected $arIncludedFiles    = [];
    protected $lang               = null;
    protected $messagesCollection = null;

    protected function __construct ()
    {
        $this->messagesCollection = new MessagesCollection();
        $this->lang = Application::getInstance()->getSettings()->getSiteLang();
    }

    /**
     * Возвращает локализованный текст для компонента, заменяя теги указанными значениями
     *
     * @param string $fullComponentName Полное имя компонента вида бренд:компонент
     * @param string $name              Код языковой фразы
     * @param array  $arReplace         Массив замен
     *
     * @return string
     * @unittest
     */
    public function getComponentMessage (string $fullComponentName, string $name, array $arReplace = [])
    {
        // $prefix = str_replace(':', '_', strtolower($fullComponentName)) . '_';
        // $prefix = str_replace('.','_',$prefix);

        return $this->getMessage($fullComponentName . '_' . $name, $arReplace);
    }

    /**
     * Возвращает локализованный текст для ядра, заменяя теги указанными значениями
     *
     * @param string $name      Код языковой фразы
     * @param array  $arReplace Массив замен
     *
     * @return string
     * @unittest
     */
    public function getCoreMessage (string $name, array $arReplace = [])
    {
        return $this->getMessage('ms_core_' . $name, $arReplace);
    }

    /**
     * Возвращает локализованный текст, заменяя теги указанными значениями
     *
     * @param string $name      Код сообщения
     * @param array  $arReplace Массив замен вида код_тега=>замена
     *
     * @return string
     * @unittest
     */
    public function getMessage (string $name, array $arReplace = [])
    {
        $name = strtolower($name);
        if ($this->messagesCollection->offsetExists($name))
        {
            $message = $this->messagesCollection->getMessage($name);
        }
        else
        {
            $message = '[' . strtolower($name) . ']';
        }

        if (!empty($arReplace))
        {
            foreach ($arReplace as $field => $value)
            {
                $field = strtoupper($field);
                $message = str_replace('#' . $field . '#', $value, $message);
            }
        }

        return (!is_null($message)) ? $message : '[' . strtolower($name) . ']';
    }

    /**
     * Возвращает коллекцию языковых фраз
     *
     * @return MessagesCollection
     * @unittest
     */
    public function getMessagesCollection ()
    {
        return $this->messagesCollection;
    }

    /**
     * Функция обертка для getMessage для модулей.
     * Собирает код текстового сообщения из префикса, названия модуля и кода сообщения
     *
     * @param string $moduleName Имя модуля
     * @param string $name       Код локализованной фразы
     * @param array  $arReplace  Массив замен
     * @param string $prefix     Префикс
     *
     * @return mixed
     * @unittest
     */
    public function getModuleMessage (string $moduleName, string $name, array $arReplace = [], string $prefix = '')
    {
        if ($moduleName != 'core' && $moduleName != 'ms.core')
        {
            if ($arModule = Modules::getInstance()->parseModuleName($moduleName))
            {
                $prefix .= strtolower($arModule['BRAND']) . '_';
                $moduleName = strtolower($arModule['MODULE']);
            }
        }
        else
        {
            $moduleName = str_replace('ms.','',$moduleName);
            $prefix .= 'ms_';
        }

        return $this->getMessage($prefix . $moduleName . '_' . $name, $arReplace);
    }

    /**
     * Подключает указанный языковой файл. Например, таким образом можно подключить общий языковой файл модуля
     *
     * @param string $filename Полный путь к файлу от корня
     * @param string $prefix   Префикс ключей фраз
     *
     * @return bool
     * @unittest
     */
    public function includeLocalizationFile (string $filename, string $prefix = '')
    {
        if (!file_exists($filename) || strpos($filename, $this->lang) === false)
        {
            return false;
        }

        if (in_array($filename, $this->arIncludedFiles))
        {
            return true;
        }

        $arMess = include($filename);
        if (empty($arMess))
        {
            return true;
        }

        $this->messagesCollection->addFromArray($arMess, $prefix);
        if (!in_array($filename, $this->arIncludedFiles))
        {
            $this->arIncludedFiles[] = $filename;
        }

        return true;
    }

    /**
     * Подключает языковой файл для указанного файла
     *
     * @param string $filename Путь к файлу, требующему локализацию
     * @param string $prefix   Префикс ключей языковых фраз
     *
     * @return bool
     * @unittest
     */
    public function includeLocalizationForThisFile (string $filename, string $prefix = '')
    {
        if ($filename = $this->prepareLocFile($filename, $prefix))
        {
            // echo $filename;
            return $this->includeLocalizationFile($filename, $prefix);
        }

        return false;
    }

    /**
     * Возвращает массив, содержащий все локализованные тексты указанного модуля
     *
     * @param string $moduleName Имя модуля или '' == 'core'
     * @param string $prefix     Префикс
     *
     * @return array
     * @unittest
     */
    public function showAllMessagesModule (string $moduleName = 'core', string $prefix = '')
    {
        if ($moduleName == '')
        {
            $moduleName = 'core';
        }
        if ($moduleName != 'core')
        {
            if ($arModule = Modules::getInstance()->parseModuleName($moduleName))
            {
                $prefix .= strtolower($arModule['BRAND']) . '_';
                $moduleName = strtolower($arModule['MODULE']);
            }
        }
        $prefix .= $moduleName . '_';
        $prefix = strtolower($prefix);

        $arMessages = [];
        if ($this->messagesCollection->isEmpty())
        {
            return [];
        }
        foreach ($this->messagesCollection as $key => $message)
        {
            if (strstr($key, $prefix) !== false)
            {
                $arMessages[$key] = $message;
            }
        }

        return $arMessages;
    }

    /**
     * @param string $filename
     * @param null   $prefix
     *
     * @return bool|string
     */
    private function prepareLocFile (string $filename, &$prefix = null)
    {
        $newFilename = false;
        $lang = $this->lang;
        // msDebug($filename);
        if (strpos($filename, 'modules') !== false)
        {
            if (preg_match('/modules\/([a-z]{1}[a-z0-9]*)[.]{1}([a-z]{1}[a-z0-9_]*)\//', $filename, $m))
            {
                $prefix = strtolower($m[1]) . '_' . strtolower($m[2]) . '_';
            }
            $returnValue = preg_replace(
                '/modules\/([a-z]{1}[a-z0-9]*[.]{1}[a-z]{1}[a-z0-9_]*)\//', 'modules/$1/loc/' . $lang . '/', $filename
            );
            if (!is_null($returnValue))
            {
                $newFilename = $returnValue;
            }
        }
        elseif (strpos($filename, 'components'))
        {
            $returnValue = null;
            if (preg_match('/components\/([a-z0-9]+)\/([a-z0-9_.]+)\/templates\/([a-z0-9_.]{2,})\//', $filename, $m))
            {
                $prefix = strtolower($m[1]) . ':' . strtolower($m[2]) . '_';
                $returnValue = preg_replace(
                    '/components\/([a-z0-9]+)\/([a-z0-9_.]+)\/templates\/([a-z0-9_.]{2,})\//',
                    'components/$1/$2/templates/$3/loc/' . $lang . '/', $filename
                );
            }
            elseif (preg_match('/components\/([a-z0-9]+)\/([a-z0-9_.]+)\//', $filename, $m))
            {
                $prefix = strtolower($m[1]) . ':' . strtolower($m[2]) . '_';
                $returnValue = preg_replace(
                    '/components\/([a-z0-9]+)\/([a-z0-9_.]+)\//', 'components/$1/$2/loc/' . $lang . '/', $filename
                );
            }
            if (!is_null($returnValue))
            {
                $newFilename = $returnValue;
            }
        }
        elseif (strpos($filename, 'core') !== false)
        {
            $prefix = 'ms_core_';
            $newFilename = str_replace('core', 'core/loc/' . $lang, $filename);
        }

        return $newFilename;
    }
}
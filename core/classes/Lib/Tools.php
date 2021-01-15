<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\Localization\Loc;
use Ms\Core\Entity\Modules\Loader;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;
use Ms\Core\Exceptions\SystemException;

/**
 * Класс Ms\Core\Lib\Tools
 * Набор различных инструментов
 */
class Tools
{
    /**
     * Преобразует HTML-код в текст
     *
     * @param string $str           Преобразуемая строка
     * @param string $strSiteUrl    Префикс ссылки
     * @param array  $aDelete       Массив со списком заменяемых строк
     * @param int    $maxLen        Максимальная длина строки
     *
     * @return string
     * @unittest
     */
    public static function HTMLToTxt (string $str, string $strSiteUrl = "", array $aDelete = [], int $maxLen = 70)
    {
        //get rid of whitespace
        $str = preg_replace("/[\\t\\n\\r]/", " ", $str);

        //replace tags with placeholders
        static $search = [
            "'<script[^>]*?>.*?</script>'si",
            "'<style[^>]*?>.*?</style>'si",
            "'<select[^>]*?>.*?</select>'si",
            "'&(quot|#34);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
        ];

        static $replace = [
            "",
            "",
            "",
            "\"",
            "\xa1",
            "\xa2",
            "\xa3",
            "\xa9",
        ];

        $str = preg_replace($search, $replace, $str);

        $str = preg_replace("#<[/]{0,1}(b|i|u|em|small|strong)>#i", "", $str);
        $str = preg_replace("#<[/]{0,1}(font|div|span)[^>]*>#i", "", $str);

        //ищем списки
        $str = preg_replace("#<ul[^>]*>#i", "\r\n", $str);
        $str = preg_replace("#<li[^>]*>#i", "\r\n  - ", $str);

        //удалим то что заданно
        foreach ($aDelete as $del_reg)
        {
            $str = preg_replace($del_reg, "", $str);
        }

        //ищем картинки
        $str = preg_replace(
            "/(<img\\s.*?src\\s*=\\s*)([\"']?)(\\/.*?)(\\2)(\\s.+?>|\\s*>)/is",
            "[" . chr(1) . $strSiteUrl . "\\3" . chr(1) . "] ", $str
        );
        $str = preg_replace(
            "/(<img\\s.*?src\\s*=\\s*)([\"']?)(.*?)(\\2)(\\s.+?>|\\s*>)/is", "[" . chr(1) . "\\3" . chr(1) . "] ", $str
        );

        //ищем ссылки
        $str = preg_replace(
            "/(<a\\s.*?href\\s*=\\s*)([\"']?)(\\/.*?)(\\2)(.*?>)(.*?)<\\/a>/is",
            "\\6 [" . chr(1) . $strSiteUrl . "\\3" . chr(1) . "] ", $str
        );
        $str = preg_replace(
            "/(<a\\s.*?href\\s*=\\s*)([\"']?)(.*?)(\\2)(.*?>)(.*?)<\\/a>/is", "\\6 [" . chr(1) . "\\3" . chr(1) . "] ",
            $str
        );

        //ищем <br>
        $str = preg_replace("#<br[^>]*>#i", "\r\n", $str);

        //ищем <p>
        $str = preg_replace("#<p[^>]*>#i", "\r\n\r\n", $str);

        //ищем <hr>
        $str = preg_replace("#<hr[^>]*>#i", "\r\n----------------------\r\n", $str);

        //ищем таблицы
        $str = preg_replace("#<[/]{0,1}(thead|tbody)[^>]*>#i", "", $str);
        $str = preg_replace("#<([/]{0,1})th[^>]*>#i", "<\\1td>", $str);

        $str = preg_replace("#</td>#i", "\t", $str);
        $str = preg_replace("#</tr>#i", "\r\n", $str);
        $str = preg_replace("#<table[^>]*>#i", "\r\n", $str);

        $str = preg_replace("#\r\n[ ]+#", "\r\n", $str);

        //мочим вообще все оставшиеся тэги
        $str = preg_replace("#<[/]{0,1}[^>]+>#i", "", $str);

        $str = preg_replace("#[ ]+ #", " ", $str);
        $str = str_replace("\t", "    ", $str);

        //переносим длинные строки
        if ($maxLen > 0)
        {
            $str = preg_replace("#([^\\n\\r]{" . intval($maxLen) . "}[^ \\r\\n]*[\\] ])([^\\r])#", "\\1\r\n\\2", $str);
        }

        $str = str_replace(chr(1), " ", $str);

        return trim($str);
    }

    /**
     * Превращает булевское значение в символ
     *
     * @param bool $bool
     *
     * @return string
     * @example true  => 'Y'
     * @example false => 'N'
     * @unittest
     */
    public static function boolToStr (bool $bool)
    {
        if ($bool)
        {
            return 'Y';
        }
        else
        {
            return 'N';
        }
    }

    /**
     * Проверяет, содержит ли строка сериализированные данные
     *
     * @param string $string Проверяемая строка
     *
     * @return bool
     * @unittest
     */
    public static function isSerialized (string $string)
    {
        return (@unserialize($string) !== false || $string == 'b:0;');
    }

    /**
     * Переводит русский символ в латинский
     *
     * @param string $char Русский символ
     *
     * @return string Латинский символ
     * @unittest
     */
    public static function convertRusToLat ($char)
    {
        \IncludeLangFile(__FILE__);
        $lang = Loc::getInstance();
        $char = mb_substr($char, 0, 1);
        switch ($char)
        {
            case $lang->getCoreMessage('alpha_big_a'):   //А
                return 'A';
            case $lang->getCoreMessage('alpha_big_b'):   //Б
                return 'B';
            case $lang->getCoreMessage('alpha_big_v'):   //В
                return 'V';
            case $lang->getCoreMessage('alpha_big_g'):   //Г
                return 'G';
            case $lang->getCoreMessage('alpha_big_d'):   //Д
                return 'D';
            case $lang->getCoreMessage('alpha_big_ye'):  //Е
            case $lang->getCoreMessage('alpha_big_yo'):  //Ё
                return 'E';
            case $lang->getCoreMessage('alpha_big_j'):   //Ж
                return 'J';
            case $lang->getCoreMessage('alpha_big_z'):   //З
                return 'Z';
            case $lang->getCoreMessage('alpha_big_i'):   //И
            case $lang->getCoreMessage('alpha_big_iy'):  //Й
            case $lang->getCoreMessage('alpha_big_ji'):  //Ы
                return 'I';
            case $lang->getCoreMessage('alpha_big_k'):   //К
                return 'K';
            case $lang->getCoreMessage('alpha_big_l'):   //Л
                return 'L';
            case $lang->getCoreMessage('alpha_big_m'):   //М
                return 'M';
            case $lang->getCoreMessage('alpha_big_n'):   //Н
                return 'N';
            case $lang->getCoreMessage('alpha_big_o'):   //О
                return 'O';
            case $lang->getCoreMessage('alpha_big_p'):   //П
                return 'P';
            case $lang->getCoreMessage('alpha_big_r'):   //Р
                return 'R';
            case $lang->getCoreMessage('alpha_big_s'):   //С
                return 'S';
            case $lang->getCoreMessage('alpha_big_t'):   //Т
                return 'T';
            case $lang->getCoreMessage('alpha_big_u'):   //У
                return 'U';
            case $lang->getCoreMessage('alpha_big_f'):   //Ф
                return 'F';
            case $lang->getCoreMessage('alpha_big_h'):   //Х
                return 'Kh';
            case $lang->getCoreMessage('alpha_big_c'):   //Ц
                return 'C';
            case $lang->getCoreMessage('alpha_big_ch'):  //Ч
                return 'Ch';
            case $lang->getCoreMessage('alpha_big_sh'):  //Ш
                return 'Sh';
            case $lang->getCoreMessage('alpha_big_sch'): //Щ
                return 'Sch';
            case $lang->getCoreMessage('alpha_big_ae'):  //Э
                return 'Ae';
            case $lang->getCoreMessage('alpha_big_yu'):  //Ю
                return 'Yu';
            case $lang->getCoreMessage('alpha_big_ya'):  //Я
                return 'Ya';
            case $lang->getCoreMessage('alpha_a'):       //а
                return 'a';
            case $lang->getCoreMessage('alpha_b'):       //б
                return 'b';
            case $lang->getCoreMessage('alpha_v'):       //в
                return 'v';
            case $lang->getCoreMessage('alpha_g'):       //г
                return 'g';
            case $lang->getCoreMessage('alpha_d'):       //д
                return 'd';
            case $lang->getCoreMessage('alpha_ye'):      //е
            case $lang->getCoreMessage('alpha_yo'):      //ё
                return 'e';
            case $lang->getCoreMessage('alpha_j'):       //ж
                return 'j';
            case $lang->getCoreMessage('alpha_z'):       //з
                return 'z';
            case $lang->getCoreMessage('alpha_i'):       //и
            case $lang->getCoreMessage('alpha_iy'):      //й
            case $lang->getCoreMessage('alpha_ji'):      //ы
                return 'i';
            case $lang->getCoreMessage('alpha_k'):       //к
                return 'k';
            case $lang->getCoreMessage('alpha_l'):       //л
                return 'l';
            case $lang->getCoreMessage('alpha_m'):       //м
                return 'm';
            case $lang->getCoreMessage('alpha_n'):       //н
                return 'n';
            case $lang->getCoreMessage('alpha_o'):       //о
                return 'o';
            case $lang->getCoreMessage('alpha_p'):       //п
                return 'p';
            case $lang->getCoreMessage('alpha_r'):       //р
                return 'r';
            case $lang->getCoreMessage('alpha_s'):       //с
                return 's';
            case $lang->getCoreMessage('alpha_t'):       //т
                return 't';
            case $lang->getCoreMessage('alpha_u'):       //у
                return 'u';
            case $lang->getCoreMessage('alpha_f'):       //ф
                return 'f';
            case $lang->getCoreMessage('alpha_h'):       //х
                return 'kh';
            case $lang->getCoreMessage('alpha_c'):       //ц
                return 'c';
            case $lang->getCoreMessage('alpha_ch'):      //ч
                return 'ch';
            case $lang->getCoreMessage('alpha_sh'):      //ш
                return 'sh';
            case $lang->getCoreMessage('alpha_sch'):     //щ
                return 'sch';
            case $lang->getCoreMessage('alpha_ae'):      //э
                return 'ae';
            case $lang->getCoreMessage('alpha_yu'):      //ю
                return 'yu';
            case $lang->getCoreMessage('alpha_ya'):      //я
                return 'ya';
            case 'A':
            case 'B':
            case 'C':
            case 'D':
            case 'E':
            case 'F':
            case 'G':
            case 'H':
            case 'I':
            case 'J':
            case 'K':
            case 'L':
            case 'M':
            case 'N':
            case 'O':
            case 'P':
            case 'Q':
            case 'R':
            case 'S':
            case 'T':
            case 'U':
            case 'V':
            case 'W':
            case 'X':
            case 'Y':
            case 'Z':
            case 'a':
            case 'b':
            case 'c':
            case 'd':
            case 'e':
            case 'f':
            case 'g':
            case 'h':
            case 'i':
            case 'j':
            case 'k':
            case 'l':
            case 'm':
            case 'n':
            case 'o':
            case 'p':
            case 'q':
            case 'r':
            case 's':
            case 't':
            case 'u':
            case 'v':
            case 'w':
            case 'x':
            case 'y':
            case 'z':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
            case '9':
            case '0':
                return $char;
            default:
                return '_';
        }
    }

    /**
     * Обрезает переданную строку до указанного количества символов, добавляя в конце троеточие «…»
     *
     * @param string $string Исходная строка
     * @param int    $number Максимальное количество символов
     * @param string $dots   Что подставить в конце строки
     *
     * @return string
     * @unittest
     */
    public static function cropString (string $string, int $number = 50, string $dots = '...')
    {
        if (strlen(mb_convert_encoding($string, 'windows-1251', 'utf-8')) > $number)
        {
            $string = mb_substr($string, 0, $number - strlen($dots), 'UTF-8');

            return $string . $dots;
        }
        else
        {
            return $string;
        }
    }

    /**
     * Функция генерирует код из полученной текстовой строки
     *
     * @return string
     * @unittest
     */
    public static function generateCode ()
    {
        $text = func_get_arg(0);
        $code = "";
        $text = iconv("utf-8", "windows-1251", $text);
        $array = str_split($text);

        $lastChar = "";
        for ($i = 0; $i < count($array); $i++)
        {
            $array[$i] = iconv("windows-1251", "utf-8", $array[$i]);
            $char = static::convertRusToLat($array[$i]);
            if ($char == "_" && $char != $lastChar)
            {
                $code .= $char;
                $lastChar = $char;
            }
            elseif ($char != "_")
            {
                $code .= $char;
                $lastChar = $char;
            }
        }
        if ($lastChar == '_')
        {
            $code = mb_substr($code, 0, -1);
        }
        $code = strtolower($code);

        return $code;
    }

    /**
     * Возвращает имя класса, описывающего таблицу, по ее имени в базе данных
     *
     * @param string $strTableName Имя таблицы в базе данных
     *
     * @return string Имя класса описывающего таблицу
     * @unittest
     */
    public static function getClassNameByTableName ($strTableName)
    {
        $strClassName = '';
        $arStr = explode("_", $strTableName);
        $moduleName = null;
        $module_name = null;
        $tableName = null;
        $lastClassExists = null;
        $brand = null;
        for ($i = 0; $i < count($arStr); $i++)
        {
            //На первом месте идет бренд
            if ($i == 0)
            {
                $strClassName .= static::setFirstCharToBig($arStr[$i]) . "\\";
                $brand = $arStr[$i];
            }
            //На втором месте идет модуль
            elseif ($i == 1)
            {
                //С ядром все просто
                if ($arStr[$i] == "core")
                {
                    $strClassName .= "Core\\Tables\\";
                }
                else
                {
                    //Если модуль с таким именем есть, сохраняем его
                    if (Loader::issetModule(strtolower($brand . '.' . $arStr[$i])))
                    {
                        $strClassName .= static::setFirstCharToBig($arStr[$i]) . "\\Tables\\";
                    }
                    //если такого модуля нет, значит имя состоит из нескольких слов через _
                    //сохраняем часть названия модуля
                    else
                    {
                        $moduleName = static::setFirstCharToBig($arStr[$i]);
                        $module_name = $arStr[$i];
                    }
                }
            }
            //Если название модуля состоит из нескольких слов
            elseif (!is_null($moduleName))
            {
                //Если такой модуль существует, записываем его
                if (Loader::issetModule(strtolower($brand . '.' . $module_name . '_' . $arStr[$i])))
                {
                    $strClassName .= $moduleName;
                    $strClassName .= static::setFirstCharToBig($arStr[$i]) . "\\Tables\\";
                    $moduleName = null;
                }
                //если не все слова названия модуля собрали, собираем дальше
                else
                {
                    $moduleName .= static::setFirstCharToBig($arStr[$i]);
                    $module_name .= '_' . $arStr[$i];
                }
            }
            //После названия модуля идет непосредственно название таблицы, сохраняем его
            else
            {
                $arStr[$i] = static::setFirstCharToBig($arStr[$i]);
                $strClassName .= $arStr[$i];
                if (class_exists($strClassName . 'Table'))
                {
                    $lastClassExists = $strClassName . 'Table';
                }
            }
        }
        $strClassName .= "Table";

        return (!is_null($lastClassExists) ? $lastClassExists : $strClassName);
    }

    /**
     * Возвращает имя текущей директории
     *
     * @return string
     * @unittest
     */
    public static function getCurDir ()
    {
        return dirname(Application::getInstance()->getServer()->getScriptName());
    }

    /**
     * Возвращает путь к текущему скрипту
     *
     * @return null|string
     * @unittest
     */
    public static function getCurPath ()
    {
        return Application::getInstance()->getServer()->getScriptName();
    }

    /**
     * Возвращает имя файла с описанием таблицы по имени таблицы БД
     *
     * @param $strTableName
     *
     * @return bool|string
     * @unittest
     */
    public static function getFileByTableName ($strTableName)
    {
        $arStr = explode('_', $strTableName);
        $brand = $arStr[0];
        $start = 1;
        if ($arStr[1] == 'core')
        {
            $module = 'core';
            $start = 2;
        }
        else
        {
            $module = '';
        }
        $table = '';
        for ($i = $start; $i < count($arStr); $i++)
        {
            if (($module == '' || !Loader::issetModule($brand . '.' . $module)) && $module != 'core')
            {
                $module .= $arStr[$i];
                continue;
            }
            else
            {
                if ($table != '')
                {
                    $table .= '_';
                }

                $table .= $arStr[$i];
            }
        }
        $app = Application::getInstance();
        $table = $app->convertSnakeCaseToPascalCase($table);
        $table .= 'Table';
        if ($module == 'core')
        {
            $filename = $app->getSettings()->getMsRoot() . '/core/classes/Tables/' . $table . '.php';
        }
        else
        {
            $filename =
                $app->getSettings()->getMsRoot() . '/modules/' . $brand . '.' . $module . '/classes/Tables/' . $table
                . '.php';
        }
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
     * Возвращает первый символ указанной строки, если передана пустая строка выдает исключение
     *
     * @param string $str Исходная строка
     *
     * @return false|string
     * @throws ArgumentOutOfRangeException
     * @unittest
     */
    public static function getFirstChar (string $str)
    {
        if (strlen($str) > 0)
        {
            return mb_substr($str, 0, 1);
        }
        else
        {
            throw new ArgumentOutOfRangeException('str', 1);
        }
    }

    /**
     * Проверяет, находится ли число между первым и вторым. Если стоит флаг "включительно" происходит сравнение >= и <=
     * Если и first и second равны null, вседа считается, что число попало в диапазон
     *
     * @param float      $value      Проверяемое значение
     * @param null|float $first      Начало диапазона, может быть null
     * @param null|float $second     Окончание диапазоне, может быть null
     * @param bool       $bInclusive Флаг "Включительно"
     *
     * @return bool
     * @unittest
     */
    public static function isBetween (float $value, float $first = null, float $second = null, bool $bInclusive = true)
    {
        if (is_null($first) && is_null($second))
        {
            return true;
        }

        if ($bInclusive)
        {
            if (!is_null($first) && !is_null($second))
            {
                return ($value >= $first && $value <= $second);
            }
            elseif (!is_null($first))
            {
                return ($value >= $first);
            }
            elseif (!is_null($second))
            {
                return ($value <= $second);
            }
        }
        else
        {
            if (!is_null($first) && !is_null($second))
            {
                return ($value > $first && $value < $second);
            }
            elseif (!is_null($first))
            {
                return ($value > $first);
            }
            elseif (!is_null($second))
            {
                return ($value < $second);
            }
        }

        return false;
    }

    /**
     * Проверяет является ли переданный путь директорией
     *
     * @param string $needle Путь
     *
     * @return bool
     */
/*    public static function isDir (string $needle)
    {
        $bDir = false;
        if (is_array($needle))
        {
            foreach ($needle as $dir)
            {
                if (strpos(self::getCurDir(), $dir) !== false)
                {
                    $bDir = true;
                    break;
                }
            }
        }
        elseif (strpos(self::getCurDir(), $needle) !== false)
        {
            $bDir = true;
        }

        return $bDir;
    }*/

    /**
     * Безопасная отправка почты
     *
     * @param        $to
     * @param        $subject
     * @param        $message
     * @param string $additional_headers
     * @param string $additional_parameters
     *
     * @return bool
     */
    public static function ms_mail ($to, $subject, $message, $additional_headers = "", $additional_parameters = "")
    {
        if (function_exists("custom_mail"))
        {
            return @custom_mail($to, $subject, $message, $additional_headers, $additional_parameters);
        }

        if ($additional_parameters != "")
        {
            return @mail($to, $subject, $message, $additional_headers, $additional_parameters);
        }

        return @mail($to, $subject, $message, $additional_headers);
    }

    /**
     * Безопасная обертка PHP функции strrpos
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool|int
     * @see strrpos()
     *
     */
    public static function ms_strrpos ($haystack, $needle)
    {
        if (Application::getInstance()->getSettings()->isCharsetUtf8())
        {
            $ln = strlen($needle);
            for ($i = strlen($haystack) - $ln; $i >= 0; $i--)
            {
                if (substr($haystack, $i, $ln) == $needle)
                {
                    return $i;
                }
            }

            return false;
        }
        $index = strpos(strrev($haystack), strrev($needle));
        if ($index === false)
        {
            return false;
        }
        $index = strlen($haystack) - strlen($needle) - $index;

        return $index;
    }

    /**
     * Перемножает элементы переданного массива
     *
     * @param $arMultiplier
     *
     * @return float
     * @unittest
     */
    public static function multiplication ($arMultiplier)
    {
        $result = 1;
        foreach ($arMultiplier as $multiplier)
        {
            $result = $result * $multiplier;
        }

        return $result;
    }

    /**
     * Возвращает число ID пользователя переданного или текущего
     *
     * @param mixed $userID ID пользователя, либо NULL (если NULL, вернет ID текущего пользователя)
     *
     * @return int|null
     * @unittest
     */
    public static function normalizeUserID ($userID = null)
    {
        if (is_null($userID) || (int)$userID < 0)
        {
            $userID = Application::getInstance()->getUser()->getID();
        }
        else
        {
            $userID = (int)$userID;
        }

        return $userID;
    }

    /**
     * Округляет число до указанной точности
     *
     * @param mixed $value Число
     * @param int   $prec  Точность
     *
     * @return float
     * @unittest
     */
    public static function roundEx ($value, $prec = 0)
    {
        $eps = 1.00 / pow(10, $prec + 4);

        return round(doubleval($value) + $eps, $prec);
    }

    /**
     * Выбирает нужный падеж для переданного числа
     *
     * @param int    $value             Число
     * @param string $subjectiveCase    Слово для числа 1
     * @param string $genitiveSingular  Слово для числа 2
     * @param string $genitivePlural    Слово для числа 5
     *
     * @return string
     * @unittest
     */
    public static function sayRusRight ($value, $subjectiveCase = null, $genitiveSingular = null, $genitivePlural = null
    ) {
        \IncludeLangFile(__FILE__);
        if (is_null($subjectiveCase))
        {
            $subjectiveCase = Loc::getInstance()->getCoreMessage('tools_day1');
        }
        if (is_null($genitiveSingular))
        {
            $genitiveSingular = Loc::getInstance()->getCoreMessage('tools_day2');
        }
        if (is_null($genitivePlural))
        {
            $genitivePlural = Loc::getInstance()->getCoreMessage('tools_day3');
        }

        $x = $value % 100;
        $y = ($x % 10) - 1;

        return ($x / 10) >> 0 == 1
            ? $genitivePlural
            : ($y & 12 ? $genitivePlural
                : ($y & 3 ? $genitiveSingular : $subjectiveCase));
    }

    /**
     * Возвращает строку, у которой первый символ переведен в верхний регистр
     *
     * @param string $str Исходная строка
     *
     * @return string
     * @unittest
     */
    public static function setFirstCharToBig (string $str)
    {
        if (strlen($str) > 0)
        {
            $fc = mb_strtoupper(mb_substr($str, 0, 1));

            return $fc . mb_substr($str, 1);
        }

        return $str;
    }

    /**
     * Заменяет все вхождения ключей массива окруженные решеткой # на значения массива
     *
     * @param array  $arReplace Массив замен
     * @param string $subject   Строка, в которой происходят замены
     *
     * @return string
     * @unittest
     */
    public static function strReplace (array $arReplace, $subject)
    {
        if (!empty($arReplace))
        {
            $arSearch = [];
            $arRepl = [];
            foreach ($arReplace as $key => $value)
            {
                if (!in_array('#' . $key . '#', $arSearch))
                {
                    $arSearch[] = '#' . $key . '#';
                    $arRepl[] = $value;
                }
            }
            if (!empty($arSearch))
            {
                $subject = str_replace($arSearch, $arRepl, $subject);
            }
        }

        return $subject;
    }

    /**
     * Превращает строковое значение в булевское
     * 'Y' превращается в true, остальное в false
     *
     * @param string $str
     *
     * @return bool
     * @example 'Y' => true
     * @example 'abrakadabra' => false
     * @unittest
     */
    public static function strToBool (string $str)
    {
        return ($str === 'Y');
    }

    public static function strrpos ($haystack, $needle)
    {
        if (strtoupper(Application::getInstance()->getSettings()->getCharset()) == "UTF-8")
        {
            //mb_strrpos does not work on invalid UTF-8 strings
            $ln = strlen($needle);
            for ($i = strlen($haystack) - $ln; $i >= 0; $i--)
            {
                if (substr($haystack, $i, $ln) == $needle)
                {
                    return $i;
                }
            }

            return false;
        }

        return strrpos($haystack, $needle);
    }

    /**
     * Функция транслитирует строку
     *
     * @param string $string Исходная строка
     *
     * @return string Транслитированная строка
     * @unittest
     */
    public static function transliterate (string $string)
    {
        if (mb_internal_encoding() == 'UTF-8')
        {
            $len = mb_strlen($string);
            $arStr = [];
            for ($i = 0; $i < $len; $i++)
            {
                $arStr[] = mb_substr($string, $i, 1);
            }
        }
        else
        {
            $arStr = str_split($string);
        }
        $newStr = '';
        if (!empty($arStr))
        {
            foreach ($arStr as $char)
            {
                $newStr .= static::convertRusToLat($char);
            }
        }

        return $newStr;
    }

    /**
     * Преобразует полученное значение к булевому значению
     *
     * @param mixed $value Исходное значение
     *
     * @return bool
     * @unittest
     */
    public static function validateBoolVal ($value)
    {
        if (
            (is_string($value) && ($value == '1' || $value == '0'))
            || (is_bool($value))
        )
        {
            $value = (int)$value;
        }
        elseif (is_string($value) && ($value == 'true' || $value == 'Y'))
        {
            $value = 1;
        }
        elseif (is_string($value) && ($value == 'false' || $value == 'N'))
        {
            $value = 0;
        }

        if (is_integer($value) && ($value == 1 || $value == 0))
        {
            if (intval($value) == 1)
            {
                $value = true;
            }
            else
            {
                $value = false;
            }
        }

        return (bool)$value;
    }

    /**
     * Преобразует полученное значение к значению даты
     *
     * @param mixed $date
     *
     * @return bool|Date
     * @unittest
     */
    public static function validateDateVal ($date)
    {
        if ($date instanceof Date)
        {
            $value = $date;
        }
        elseif (strpos($date, '.') !== false)
        {
            $arData = explode('.', $date);
            if (
                (intval($arData[0]) >= 1 && intval($arData[0]) <= 31)
                && (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
                && (intval($arData[2]) >= 1970 && intval($arData[2]) <= 9999)
            )
            {
                if (intval($arData[1]) >= 1 && intval($arData[1]) <= 0)
                {
                    $arData[1] = '0' . $arData[1];
                }
                if (intval($arData[0]) >= 1 && intval($arData[0]) <= 0)
                {
                    $arData[0] = '0' . $arData[0];
                }
                try
                {
                    $value = new Date($arData[2] . '-' . $arData[1] . '-' . $arData[0], 'Y-m-d');
                }
                catch (SystemException $e)
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        elseif (strpos($date, '-') !== false)
        {
            $arData = explode('-', $date);
            if (
                (intval($arData[2]) >= 1 && intval($arData[2]) <= 31)
                && (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
                && (intval($arData[0]) >= 1970 && intval($arData[0]) <= 9999)
            )
            {
                if (intval($arData[2]) >= 1 && intval($arData[2]) <= 0)
                {
                    $arData[2] = '0' . $arData[2];
                }
                if (intval($arData[1]) >= 1 && intval($arData[1]) <= 0)
                {
                    $arData[1] = '0' . $arData[1];
                }
                try
                {
                    $value = new Date($arData[0] . '-' . $arData[1] . '-' . $arData[2], 'Y-m-d');
                }
                catch (SystemException $e)
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }

        return $value;
    }

    /**
     * Преобразует полученное значение к значению float,
     * убирая пробелы и заменяя запятую ',' точкой '.'
     *
     * @param mixed $value Исходное значение
     *
     * @return float Значение
     * @unittest
     */
    public static function validateFloatVal ($value)
    {
        $temp = str_replace(' ', '', (string)$value);
        $temp = str_replace(',', '.', $temp);

        return (float)$temp;
    }

    /**
     * Преобразует полученное значение к значению int
     *
     * @param mixed $value Исходное значение
     *
     * @return int Значение
     * @unittest
     */
    public static function validateIntVal ($value)
    {
        return (int)$value;
    }

    /**
     * Преобразует полученное значение к строковому значению
     *
     * @param mixed $value
     *
     * @return string
     * @unittest
     */
    public static function validateStringVal ($value)
    {
        return htmlspecialchars((string)$value);
    }
}
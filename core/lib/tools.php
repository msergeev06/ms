<?php
/**
 * Ms\Core\Lib\Tools
 * Набор различных инструментов
 *
 * @package Ms\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @since 0.1.0
 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/start
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\Application;
use Ms\Core\Entity\Type\Date;

class Tools
{
	private static $search =  array("&lt;", "&gt;", "&quot;", "&apos;", "&amp;");
	private static $replace = array("<",    ">",    "\"",     "'",      "&");
	private static $searchEx =  array("&amp;",     "&lt;",     "&gt;",     "&quot;",     "&#34",     "&#x22",     "&#39",     "&#x27",     "<",    ">",    "\"");
	private static $replaceEx = array("&amp;amp;", "&amp;lt;", "&amp;gt;", "&amp;quot;", "&amp;#34", "&amp;#x22", "&amp;#39", "&amp;#x27", "&lt;", "&gt;", "&quot;");
	private static $isWindowsOs = null;

	/**
	 * Функция генерирует код из полученной текстовой строки
	 *
	 * @api
	 *
	 * @return string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_generate_code
	 */
	public static function generateCode ()
	{
		$text = func_get_arg (0);
		$code = "";
		$text = iconv("utf-8","windows-1251",$text);
		$array = str_split($text);

		$lastChar = "";
		for ($i=0; $i<count($array); $i++)
		{
			$array[$i] = iconv("windows-1251","utf-8",$array[$i]);
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
		$code = strtolower($code);

		return $code;
	}

	/**
	 * Переводит русский символ в латинский
	 *
	 * @api
	 *
	 * @param string $char Русский символ
	 *
	 * @return string Латинский символ
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_convert_rus_to_lat
	 */
	public static function convertRusToLat ($char)
	{
		Loc::includeLocFile(__FILE__,'ms_core_');
		switch ($char)
		{
			case Loc::getModuleMessage('core','alpha_big_a'):   //А
				return 'A';
			case Loc::getModuleMessage('core','alpha_big_b'):   //Б
				return 'B';
			case Loc::getModuleMessage('core','alpha_big_v'):   //В
				return 'V';
			case Loc::getModuleMessage('core','alpha_big_g'):   //Г
				return 'G';
			case Loc::getModuleMessage('core','alpha_big_d'):   //Д
				return 'D';
			case Loc::getModuleMessage('core','alpha_big_ye'):  //Е
			case Loc::getModuleMessage('core','alpha_big_yo'):  //Ё
				return 'E';
			case Loc::getModuleMessage('core','alpha_big_j'):   //Ж
				return 'J';
			case Loc::getModuleMessage('core','alpha_big_z'):   //З
				return 'Z';
			case Loc::getModuleMessage('core','alpha_big_i'):   //И
			case Loc::getModuleMessage('core','alpha_big_iy'):  //Й
			case Loc::getModuleMessage('core','alpha_big_ji'):  //Ы
				return 'I';
			case Loc::getModuleMessage('core','alpha_big_k'):   //К
				return 'K';
			case Loc::getModuleMessage('core','alpha_big_l'):   //Л
				return 'L';
			case Loc::getModuleMessage('core','alpha_big_m'):   //М
				return 'M';
			case Loc::getModuleMessage('core','alpha_big_n'):   //Н
				return 'N';
			case Loc::getModuleMessage('core','alpha_big_o'):   //О
				return 'O';
			case Loc::getModuleMessage('core','alpha_big_p'):   //П
				return 'P';
			case Loc::getModuleMessage('core','alpha_big_r'):   //Р
				return 'R';
			case Loc::getModuleMessage('core','alpha_big_s'):   //С
				return 'S';
			case Loc::getModuleMessage('core','alpha_big_t'):   //Т
				return 'T';
			case Loc::getModuleMessage('core','alpha_big_u'):   //У
				return 'U';
			case Loc::getModuleMessage('core','alpha_big_f'):   //Ф
				return 'F';
			case Loc::getModuleMessage('core','alpha_big_h'):   //Х
				return 'Kh';
			case Loc::getModuleMessage('core','alpha_big_c'):   //Ц
				return 'C';
			case Loc::getModuleMessage('core','alpha_big_ch'):  //Ч
				return 'Ch';
			case Loc::getModuleMessage('core','alpha_big_sh'):  //Ш
				return 'Sh';
			case Loc::getModuleMessage('core','alpha_big_sch'): //Щ
				return 'Sch';
			case Loc::getModuleMessage('core','alpha_big_ae'):  //Э
				return 'Ae';
			case Loc::getModuleMessage('core','alpha_big_yu'):  //Ю
				return 'Yu';
			case Loc::getModuleMessage('core','alpha_big_ya'):  //Я
				return 'Ya';
			case Loc::getModuleMessage('core','alpha_a'):       //а
				return 'a';
			case Loc::getModuleMessage('core','alpha_b'):       //б
				return 'b';
			case Loc::getModuleMessage('core','alpha_v'):       //в
				return 'v';
			case Loc::getModuleMessage('core','alpha_g'):       //г
				return 'g';
			case Loc::getModuleMessage('core','alpha_d'):       //д
				return 'd';
			case Loc::getModuleMessage('core','alpha_ye'):      //е
			case Loc::getModuleMessage('core','alpha_yo'):      //ё
				return 'e';
			case Loc::getModuleMessage('core','alpha_j'):       //ж
				return 'j';
			case Loc::getModuleMessage('core','alpha_z'):       //з
				return 'z';
			case Loc::getModuleMessage('core','alpha_i'):       //и
			case Loc::getModuleMessage('core','alpha_iy'):      //й
			case Loc::getModuleMessage('core','alpha_ji'):      //ы
				return 'i';
			case Loc::getModuleMessage('core','alpha_k'):       //к
				return 'k';
			case Loc::getModuleMessage('core','alpha_l'):       //л
				return 'l';
			case Loc::getModuleMessage('core','alpha_m'):       //м
				return 'm';
			case Loc::getModuleMessage('core','alpha_n'):       //н
				return 'n';
			case Loc::getModuleMessage('core','alpha_o'):       //о
				return 'o';
			case Loc::getModuleMessage('core','alpha_p'):       //п
				return 'p';
			case Loc::getModuleMessage('core','alpha_r'):       //р
				return 'r';
			case Loc::getModuleMessage('core','alpha_s'):       //с
				return 's';
			case Loc::getModuleMessage('core','alpha_t'):       //т
				return 't';
			case Loc::getModuleMessage('core','alpha_u'):       //у
				return 'u';
			case Loc::getModuleMessage('core','alpha_f'):       //ф
				return 'f';
			case Loc::getModuleMessage('core','alpha_h'):       //х
				return 'kh';
			case Loc::getModuleMessage('core','alpha_c'):       //ц
				return 'c';
			case Loc::getModuleMessage('core','alpha_ch'):      //ч
				return 'ch';
			case Loc::getModuleMessage('core','alpha_sh'):      //ш
				return 'sh';
			case Loc::getModuleMessage('core','alpha_sch'):     //щ
				return 'sch';
			case Loc::getModuleMessage('core','alpha_ae'):      //э
				return 'ae';
			case Loc::getModuleMessage('core','alpha_yu'):      //ю
				return 'yu';
			case Loc::getModuleMessage('core','alpha_ya'):      //я
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
	 * Превращает булевское значение в символ
	 *
	 * @api
	 *
	 * @example true  => 'Y'
	 * @example false => 'N'
	 *
	 * @return string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_bool_to_str
	 */
	public static function boolToStr () {
		$bool = func_get_arg (0);

		if (is_bool($bool)) {
			if ($bool)
				return 'Y';
			else
				return 'N';
		}
		else {
			return $bool;
		}
	}

	/**
	 * Превращает строковое значение в булевское
	 * 'Y' превращается в true, остальное в false
	 *
	 * @api
	 *
	 * @example 'Y' => true
	 * @example 'abrakadabra' => false
	 *
	 * @return bool
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_str_to_bool
	 */
	public static function strToBool () {
		$str = func_get_arg(0);

		if (is_string($str)) {
			if ($str=="Y")
				return true;
			else
				return false;
		}
		else {
			return $str;
		}
	}

	/**
	 * Перемножает элементы переданного массива
	 *
	 * @param $arMultiplier
	 *
	 * @return int
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_multiplication
	 */
	public static function multiplication ($arMultiplier)
	{
		$result = 1;
		foreach ($arMultiplier as $multiplier) {
			$result = $result * $multiplier;
		}

		return $result;
	}

	/**
	 * Возвращает имя класса, описывающего таблицу, по ее имени в базе данных
	 *
	 * @api
	 *
	 * @param string $strTableName Имя таблицы в базе данных
	 *
	 * @return string Имя класса описывающего таблицу
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_get_class_name_by_table_name
	 */
	public static function getClassNameByTableName ($strTableName)
	{
		$strClassName = '';
		$arStr = explode("_",$strTableName);
		$moduleName = null;
		$module_name = null;
		$brand = null;
		for($i=0;$i<count($arStr);$i++)
		{
			//На первом месте идет бренд
			if ($i==0)
			{
				$strClassName .= static::setFirstCharToBig($arStr[$i])."\\";
				$brand = $arStr[$i];
			}
			//На втором месте идет модуль
			elseif($i==1)
			{
				//С ядром все просто
				if ($arStr[$i] == "core")
				{
					$strClassName .= "Core\\Tables\\";
				}
				else
				{
					//Если модуль с таким именем есть, сохраняем его
					if (Loader::issetModule(strtolower($brand.'.'.$arStr[$i])))
					{
						$strClassName .= static::setFirstCharToBig($arStr[$i])."\\Tables\\";
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
				//Ести такой модуль существует, записываем его
				if (Loader::issetModule(strtolower($brand.'.'.$module_name.'_'.$arStr[$i])))
				{
					$strClassName .= $moduleName;
					$strClassName .= static::setFirstCharToBig($arStr[$i])."\\Tables\\";
					$moduleName = null;
				}
				//если не все слова названия модуля собрали, собираем дальше
				else
				{
					$moduleName .= static::setFirstCharToBig($arStr[$i]);
					$module_name .= '_'.$arStr[$i];
				}
			}
			//После названия модуля идет непосредственно название таблицы, сохраняем его
			else
			{
				$arStr[$i] = static::setFirstCharToBig($arStr[$i]);
				$strClassName .= $arStr[$i];
			}
		}
		$strClassName .= "Table";

		return $strClassName;
	}

	/**
	 * Запускает функцию класса описывающего таблицу для указанного имени таблицы
	 *
	 * @api
	 *
	 * @param string $strTable    Имя таблицы в базе данных
	 * @param string $strFunction Имя функции в классе описывающем таблицу
	 * @param array  $arParams    Передаваемые параметры
	 *
	 * @return mixed
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_run_table_class_function
	 */
	public static function runTableClassFunction ($strTable,$strFunction,$arParams=array())
	{
		$strClassFunction = static::getClassNameByTableName($strTable);
		$strClassFunction .= "::".$strFunction;
		if (empty($arParams))
		{
			return call_user_func($strClassFunction);
		}
		else
		{
			return call_user_func($strClassFunction,$arParams);
		}
	}

	/**
	 * Возвращает строку, у которой первый символ переведен в верхний регистр
	 *
	 * @api
	 *
	 * @param string $str Исходная строка
	 *
	 * @return string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_set_first_char_to_big
	 */
	public static function setFirstCharToBig ($str)
	{
		if (strlen($str)>0)
		{
			$str = iconv("utf-8","windows-1251",$str);
			$str[0] = strtoupper($str[0]);
			$str = iconv("windows-1251","utf-8",$str);
		}

		return $str;
	}

	/**
	 * Преобразует полученную строку к значению floatval,
	 * убирая пробелы и заменяя запятую ',' точкой '.'
	 *
	 * @api
	 *
	 * @param string $strFloat Исходная строка
	 *
	 * @return float Значение
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_validate_float_val
	 */
	public static function validateFloatVal ($strFloat)
	{
		$temp = str_replace(' ','',$strFloat);
		$temp = str_replace(',','.',$temp);
		$temp = floatval($temp);

		return $temp;
	}

	/**
	 * Преобразует полученную строку к значению intval
	 *
	 * @api
	 *
	 * @param string $strInt Исходная строка
	 *
	 * @return int Значение
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_validate_int_val
	 */
	public static function validateIntVal ($strInt)
	{
		return intval($strInt);
	}

	/**
	 * Преобразует полученное значение к строковому значению
	 *
	 * @param $str
	 *
	 * @return string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_validate_string_val
	 */
	public static function validateStringVal ($str)
	{
		return htmlspecialchars($str);
	}

	/**
	 * Преобразует полученное значение к булевому значению
	 *
	 * @param $value
	 *
	 * @return bool|int
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_validate_bool_val
	 */
	public static function validateBoolVal ($value)
	{
		if (
			(is_string($value) && ($value == '1' || $value == '0'))
			||
			(is_bool($value))
		)
		{
			$value = (int) $value;
		}
		elseif (is_string($value) && ($value == 'true' || $value == 'Y'))
		{
			$value = 1;
		}
		elseif (is_string($value) && ($value == 'false' || $value== 'N'))
		{
			$value = 0;
		}

		if (is_integer($value) && ($value == 1 || $value == 0))
		{
			if (intval($value)==1)
			{
				$value = true;
			}
			else
			{
				$value = false;
			}
		}

		return $value;
	}

	/**
	 * Преобразует полученное значение к значению даты
	 *
	 * @param $date
	 *
	 * @return bool|Date
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_validate_date_val
	 */
	public static function validateDateVal ($date)
	{
		if ($date instanceof Date)
		{
			$value = $date;
		}
		elseif (strpos($date,'.') !== false)
		{
			$arData = explode('.',$date);
			if (
				(intval($arData[0]) >= 1 && intval($arData[0]) <= 31)
				&& (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
				&& (intval($arData[2]) >= 1970 && intval($arData[2]) <= 9999)
			)
			{
				if (intval($arData[1])>=1 && intval($arData[1])<=0)
				{
					$arData[1] = '0'.$arData[1];
				}
				if (intval($arData[0])>=1 && intval($arData[0])<=0)
				{
					$arData[0] = '0'.$arData[0];
				}
				$value = new Date($arData[2].'-'.$arData[1].'-'.$arData[0],'Y-m-d');
			}
			else
			{
				return false;
			}
		}
		elseif (strpos($date,'-') !== false)
		{
			$arData = explode('-',$date);
			if (
				(intval($arData[2]) >= 1 && intval($arData[2]) <= 31)
				&& (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
				&& (intval($arData[0]) >= 1970 && intval($arData[0]) <= 9999)
			)
			{
				if (intval($arData[2])>=1 && intval($arData[2])<=0)
				{
					$arData[2] = '0'.$arData[2];
				}
				if (intval($arData[1])>=1 && intval($arData[1])<=0)
				{
					$arData[1] = '0'.$arData[1];
				}
				$value = new Date($arData[0].'-'.$arData[1].'-'.$arData[2],'Y-m-d');
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
	 * Возвращает имя файла с описанием таблицы по имени таблицы БД
	 *
	 * @param $strTableName
	 *
	 * @return bool|string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_get_file_by_table_name
	 */
	public static function getFileByTableName ($strTableName)
	{
		$arStr = explode('_',$strTableName);
		$brand = $arStr[0];
		$start = 1;
		if ($arStr[1]=='core')
		{
			$module = 'core';
			$start = 2;
		}
		else
		{
			$module = '';
		}
		$table = '';
		for ($i=$start;$i<count($arStr);$i++)
		{
			if ($module == '' || !Loader::issetModule($brand.'.'.$module))
			{
				if ($module != '') $module .= '_';

				$module .= $arStr[$i];
				continue;
			}
			else
			{
				if ($table != '') $table .= '_';

				$table .= $arStr[$i];
			}
		}
		$app = Application::getInstance();
		if ($module == 'core')
		{
			$filename = $app->getSettings()->getMsRoot().'/core/tables/'.$table.'.php';
		}
		else
		{
			$filename = $app->getSettings()->getMsRoot().'/modules/'.$brand.'.'.$module.'/tables/'.$table.'.php';
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
	 * Функция транслитирует строку
	 *
	 * @api
	 *
	 * @param string $string Исходная строка
	 *
	 * @return string Транслитированная строка
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_transliterate
	 */
	public static function transliterate($string)
	{
		Loc::includeLocFile(__FILE__,'ms_core_');
		$converter = array(
			Loc::getModuleMessage('core','alpha_a') => 'a',     //а
			Loc::getModuleMessage('core','alpha_b') => 'b',     //б
			Loc::getModuleMessage('core','alpha_v') => 'v',     //в
			Loc::getModuleMessage('core','alpha_g') => 'g',     //г
			Loc::getModuleMessage('core','alpha_d') => 'd',     //д
			Loc::getModuleMessage('core','alpha_ye') => 'e',    //е
			Loc::getModuleMessage('core','alpha_yo') => 'e',    //ё
			Loc::getModuleMessage('core','alpha_j') => 'zh',    //ж
			Loc::getModuleMessage('core','alpha_z') => 'z',     //з
			Loc::getModuleMessage('core','alpha_i') => 'i',     //и
			Loc::getModuleMessage('core','alpha_iy') => 'y',    //й
			Loc::getModuleMessage('core','alpha_k') => 'k',     //к
			Loc::getModuleMessage('core','alpha_l') => 'l',     //л
			Loc::getModuleMessage('core','alpha_m') => 'm',     //м
			Loc::getModuleMessage('core','alpha_n') => 'n',     //н
			Loc::getModuleMessage('core','alpha_o') => 'o',     //о
			Loc::getModuleMessage('core','alpha_p') => 'p',     //п
			Loc::getModuleMessage('core','alpha_r') => 'r',     //р
			Loc::getModuleMessage('core','alpha_s') => 's',     //с
			Loc::getModuleMessage('core','alpha_t') => 't',     //т
			Loc::getModuleMessage('core','alpha_u') => 'u',     //у
			Loc::getModuleMessage('core','alpha_f') => 'f',     //ф
			Loc::getModuleMessage('core','alpha_h') => 'h',     //х
			Loc::getModuleMessage('core','alpha_c') => 'c',     //ц
			Loc::getModuleMessage('core','alpha_ch') => 'ch',   //ч
			Loc::getModuleMessage('core','alpha_sh') => 'sh',   //ш
			Loc::getModuleMessage('core','alpha_sch') => 'sch', //щ
			Loc::getModuleMessage('core','alpha_hard') => '\'', //ъ
			Loc::getModuleMessage('core','alpha_ji') => 'y',    //ы
			Loc::getModuleMessage('core','alpha_soft') => '\'', //ь
			Loc::getModuleMessage('core','alpha_ae') => 'e',    //э
			Loc::getModuleMessage('core','alpha_yu') => 'yu',   //ю
			Loc::getModuleMessage('core','alpha_ya') => 'ya',   //я

			Loc::getModuleMessage('core','alpha_big_a') => 'A',     //А
			Loc::getModuleMessage('core','alpha_big_b') => 'B',     //Б
			Loc::getModuleMessage('core','alpha_big_v') => 'V',     //В
			Loc::getModuleMessage('core','alpha_big_g') => 'G',     //Г
			Loc::getModuleMessage('core','alpha_big_d') => 'D',     //Д
			Loc::getModuleMessage('core','alpha_big_ye') => 'E',    //Е
			Loc::getModuleMessage('core','alpha_big_yo') => 'E',    //Ё
			Loc::getModuleMessage('core','alpha_big_j') => 'Zh',    //Ж
			Loc::getModuleMessage('core','alpha_big_z') => 'Z',     //З
			Loc::getModuleMessage('core','alpha_big_i') => 'I',     //И
			Loc::getModuleMessage('core','alpha_big_iy') => 'Y',    //Й
			Loc::getModuleMessage('core','alpha_big_k') => 'K',     //К
			Loc::getModuleMessage('core','alpha_big_l') => 'L',     //Л
			Loc::getModuleMessage('core','alpha_big_m') => 'M',     //М
			Loc::getModuleMessage('core','alpha_big_n') => 'N',     //Н
			Loc::getModuleMessage('core','alpha_big_o') => 'O',     //О
			Loc::getModuleMessage('core','alpha_big_p') => 'P',     //П
			Loc::getModuleMessage('core','alpha_big_r') => 'R',     //Р
			Loc::getModuleMessage('core','alpha_big_s') => 'S',     //С
			Loc::getModuleMessage('core','alpha_big_t') => 'T',     //Т
			Loc::getModuleMessage('core','alpha_big_u') => 'U',     //У
			Loc::getModuleMessage('core','alpha_big_f') => 'F',     //Ф
			Loc::getModuleMessage('core','alpha_big_h') => 'H',     //Х
			Loc::getModuleMessage('core','alpha_big_c') => 'C',     //Ц
			Loc::getModuleMessage('core','alpha_big_ch') => 'Ch',   //Ч
			Loc::getModuleMessage('core','alpha_big_sh') => 'Sh',   //Ш
			Loc::getModuleMessage('core','alpha_big_sch') => 'Sch', //Щ
			Loc::getModuleMessage('core','alpha_big_hard') => '\'', //Ъ
			Loc::getModuleMessage('core','alpha_big_ji') => 'Y',    //Ы
			Loc::getModuleMessage('core','alpha_big_soft') => '\'', //Ь
			Loc::getModuleMessage('core','alpha_big_ae') => 'E',    //Э
			Loc::getModuleMessage('core','alpha_big_yu') => 'Yu',   //Ю
			Loc::getModuleMessage('core','alpha_big_ya') => 'Ya',   //Я
		);
		return strtr($string, $converter);
	}

	/**
	 * Возвращает имя текущей директории
	 *
	 * @return string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_get_cur_dir
	 */
	public static function getCurDir ()
	{
		return dirname(Application::getInstance()->getContext()->getServer()->getRequestUri());
	}

	/**
	 * Возвращает путь к текущему скрипту
	 *
	 * @return null|string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_get_cur_path
	 */
	public static function getCurPath ()
	{
		return Application::getInstance()->getContext()->getServer()->getScriptName();
	}

	/**
	 * Проверяет является ли переданный путь директорией
	 *
	 * @param $needle
	 *
	 * @return bool
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_is_dir
	 */
	public static function isDir ($needle)
	{
		$bDir = false;
		if (is_array($needle))
		{
			foreach($needle as $dir)
			{
				if (strpos(self::getCurDir(),$dir)!==false)
				{
					$bDir = true;
					break;
				}
			}
		}
		elseif (strpos (self::getCurDir(),$needle) !== false)
		{
			$bDir = true;
		}

		return $bDir;
	}

	/**
	 * Обрезает переданную строку до указанного количества символов, добавляя в конце троеточие «…»
	 *
	 * @param        $string
	 * @param int    $number
	 * @param string $dots
	 *
	 * @return string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_crop_string
	 */
	public static function cropString ($string, $number=50, $dots='...')
	{
		if (strlen(mb_convert_encoding($string, 'windows-1251', 'utf-8'))>$number)
		{
			$string = mb_substr($string,0,$number-3,'UTF-8');
			return $string.$dots;
		}
		else
		{
			return $string;
		}
	}

	public static function roundEx($value, $prec=0)
	{
		$eps = 1.00/pow(10, $prec+4);
		return round(doubleval($value)+$eps, $prec);
	}

	public static function trimUnsafe($path)
	{
		return rtrim($path, "\0.\\/+ ");
	}

	public static function htmlspecialchars ($str)
	{
		return str_replace(self::$searchEx, self::$replaceEx, $str);
	}

	public static function strrpos ($haystack, $needle)
	{
		if(strtoupper(Application::getInstance()->getSettings()->getCharset())=="UTF-8")
		{
			//mb_strrpos does not work on invalid UTF-8 strings
			$ln = strlen($needle);
			for($i = strlen($haystack)-$ln; $i >= 0; $i--)
				if(substr($haystack, $i, $ln) == $needle)
					return $i;
			return false;
		}
		return strrpos($haystack, $needle);
	}

	/**
	 * Выбирает нужный падеж для переданного числа
	 *
	 * @param int    $value
	 * @param string $subjectiveCase
	 * @param string $genitiveSingular
	 * @param string $genitivePlural
	 *
	 * @return mixed|null
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_say_rus_right
	 */
	public static function sayRusRight ($value, $subjectiveCase=null, $genitiveSingular=null, $genitivePlural=null)
	{
		Loc::includeLocFile(__FILE__,'ms_core_');
		if (is_null($subjectiveCase))
		{
			$subjectiveCase = Loc::getModuleMessage('core','tools_day1');
		}
		if (is_null($genitiveSingular))
		{
			$genitiveSingular = Loc::getModuleMessage('core','tools_day2');
		}
		if (is_null($genitivePlural))
		{
			$genitivePlural = Loc::getModuleMessage('core','tools_day3');
		}

		$x = $value % 100;
		$y = ($x % 10)-1;

		return ($x/10)>>0==1 ? $genitivePlural : ($y&12 ? $genitivePlural : ($y&3 ? $genitiveSingular : $subjectiveCase));
	}

	public static function htmlspecialcharsBack ($str)
	{
		return str_replace(self::$search, self::$replace, $str);
	}

	/**
	 * Проверяет, содержит ли переменная сериализированные данные
	 *
	 * @param string $str
	 * @param int    $max_depth
	 *
	 * @return bool
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/tools/method_check_serialized_data
	 */
	public static function checkSerializedData($str, $max_depth = 200)
	{
		if(preg_match('/O\\:\\d/', $str)) // serialized objects
		{
			return false;
		}

		// check max depth in PHP 5.3.0 and earlier
		if(!version_compare(phpversion(),"5.3.0",">"))
		{
			$str1 = preg_replace('/[^{}]+/u', '', $str);
			$cnt = 0;
			for ($i=0,$len=strlen($str1);$i<$len;$i++)
			{
				// we've just cleared all possible utf-symbols, so we can use [] syntax
				if ($str1[$i]=='}')
					$cnt--;
				else
				{
					$cnt++;
					if ($cnt > $max_depth)
						break;
				}
			}

			return $cnt <= $max_depth;
		} else
			return true;
	}

	public static function HTMLToTxt($str, $strSiteUrl="", $aDelete=array(), $maxlen=70)
	{
		//get rid of whitespace
		$str = preg_replace("/[\\t\\n\\r]/", " ", $str);

		//replace tags with placeholders
		static $search = array(
			"'<script[^>]*?>.*?</script>'si",
			"'<style[^>]*?>.*?</style>'si",
			"'<select[^>]*?>.*?</select>'si",
			"'&(quot|#34);'i",
			"'&(iexcl|#161);'i",
			"'&(cent|#162);'i",
			"'&(pound|#163);'i",
			"'&(copy|#169);'i",
		);

		static $replace = array(
			"",
			"",
			"",
			"\"",
			"\xa1",
			"\xa2",
			"\xa3",
			"\xa9",
		);

		$str = preg_replace($search, $replace, $str);

		$str = preg_replace("#<[/]{0,1}(b|i|u|em|small|strong)>#i", "", $str);
		$str = preg_replace("#<[/]{0,1}(font|div|span)[^>]*>#i", "", $str);

		//ищем списки
		$str = preg_replace("#<ul[^>]*>#i", "\r\n", $str);
		$str = preg_replace("#<li[^>]*>#i", "\r\n  - ", $str);

		//удалим то что заданно
		foreach($aDelete as $del_reg)
			$str = preg_replace($del_reg, "", $str);

		//ищем картинки
		$str = preg_replace("/(<img\\s.*?src\\s*=\\s*)([\"']?)(\\/.*?)(\\2)(\\s.+?>|\\s*>)/is", "[".chr(1).$strSiteUrl."\\3".chr(1)."] ", $str);
		$str = preg_replace("/(<img\\s.*?src\\s*=\\s*)([\"']?)(.*?)(\\2)(\\s.+?>|\\s*>)/is", "[".chr(1)."\\3".chr(1)."] ", $str);

		//ищем ссылки
		$str = preg_replace("/(<a\\s.*?href\\s*=\\s*)([\"']?)(\\/.*?)(\\2)(.*?>)(.*?)<\\/a>/is", "\\6 [".chr(1).$strSiteUrl."\\3".chr(1)."] ", $str);
		$str = preg_replace("/(<a\\s.*?href\\s*=\\s*)([\"']?)(.*?)(\\2)(.*?>)(.*?)<\\/a>/is", "\\6 [".chr(1)."\\3".chr(1)."] ", $str);

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
		if($maxlen > 0)
			$str = preg_replace("#([^\\n\\r]{".intval($maxlen)."}[^ \\r\\n]*[\\] ])([^\\r])#", "\\1\r\n\\2", $str);

		$str = str_replace(chr(1), " ",$str);

		return trim($str);
	}

	/**
	 * Превращает CamelCase в camel_case
	 *
	 * @param $strCamelCase
	 *
	 * @return string
	 */
	public static function camelCaseToUnderscore ($strCamelCase)
	{
		$strCamelCase = preg_replace('/(?<=\\w)(?=[A-Z])/','_$1', $strCamelCase);

		return strtolower($strCamelCase);
	}

	/**
	 * Возвращает TRUE, если текущая ОС является Windows, FALSE в противном случае
	 *
	 * @return bool
	 */
	public static function isWindowsOs ()
	{
		if (is_null(static::$isWindowsOs))
		{
			if(strncasecmp(PHP_OS, "WIN", 3) == 0)
			{
				//windows
				static::$isWindowsOs = true;
			}
			else
			{
				//unix
				static::$isWindowsOs = false;
			}
		}

		return static::$isWindowsOs;
	}
}
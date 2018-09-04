<?php
/**
 * Ms
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */


use \Ms\Core\Exception;
use \Ms\Core\Lib;
use Ms\Core\Entity\Type\Date;
use \Ms\Core\Entity\Application;

/**
 * Returns HTML "input"
 *
 * @param string        $strType        input type
 * @param string        $strName        input name
 * @param string        $strValue       input value
 * @param string|array  $strCmp         checked
 * @param bool          $strPrintValue  Выводить strValue или strPrint
 * @param string        $strPrint       Вывод описания поля
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputType($strType, $strName, $strValue, $strCmp, $strPrintValue=false, $strPrint="", $field1="", $strId="")
{
	$bCheck = false;
	if($strValue <> '')
	{
		if (is_array($strValue))
		{
			if(is_array($strCmp))
			{
				$bCheck = in_array($strValue, $strCmp);
			}
			elseif($strCmp <> '')
			{
				$bCheck = in_array($strValue, explode(",", $strCmp));
			}
		}
		else
		{
			$bCheck = ($strValue == $strCmp);
		}
	}
	$bLabel = false;
	if ($strType == 'radio'/* || $strType == 'checkbox'*/)
		$bLabel = true;
	return ($bLabel? '<label>': '').'<input type="'.$strType.'" '.$field1.' name="'.$strName.'" id="'.($strId <> ''? $strId : $strName).'" value="'.$strValue.'"'.
	($bCheck? ' checked':'').'> '.($strPrintValue? $strValue:$strPrint).($bLabel? '</label>': '');
}

function TextArea ($strName, $strValue, $field1='',$strId='')
{
	if ($strId=='')
	{
		$strId = $strName;
	}

	return '<textarea name="'.$strName.'" id="'.$strId.'" '.$field1.'>'.$strValue.'</textarea>';
}


/**
 * Returns HTML "input" type "text"
 *
 * @param string        $strName        input name
 * @param string        $strValue       input value
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputText($strName, $strValue, $field1="", $strId="")
{
	return InputType ('text', $strName, $strValue, '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "email"
 *
 * @param string        $strName        input name
 * @param string        $strValue       input value
 * @param bool          $multiple       Является ли поле множественным
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputEmail($strName, $strValue, $multiple=false, $field1="", $strId="")
{
	if ($multiple===true)
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='multiple';
	}
	return InputType ('email', $strName, $strValue, '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "tel"
 *
 * @param string        $strName        input name
 * @param string        $strValue       input value
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputTel($strName, $strValue, $field1="", $strId="")
{
	return InputType ('tel', $strName, $strValue, '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "number"
 *
 * @param string            $strName        input name
 * @param string            $strValue       input value
 * @param bool|int|float    $min            Минимальное значение числа
 * @param bool|int|float    $max            Максимальное значение числа
 * @param bool|int|float    $step           Шаг числа, также указывает возможное количество знаков после запятой
 * @param string            $field1         Дополнительный вывод данных для input
 * @param string            $strId          input id
 *
 * @return string
 */
function InputNumber($strName, $strValue, $min=false, $max=false, $step=false, $field1="", $strId="")
{
	if ($min!==false)
	{
		if ($field1 != '')
		{
			$field1 .= ' ';
		}
		$field1.='min="'.$min.'"';
	}
	if ($max!==false)
	{
		if ($field1 != '')
		{
			$field1 .= ' ';
		}
		$field1.='max="'.$max.'"';
	}
	if ($step!==false)
	{
		if ($field1 != '')
		{
			$field1 .= ' ';
		}
		$field1.='step="'.$step.'"';
	}
	return InputType ('number', $strName, $strValue, '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "password"
 *
 * @param string        $strName        input name
 * @param string        $strValue       input value
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputPassword($strName, $strValue, $field1="", $strId="")
{
	return InputType ('password', $strName, $strValue, '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "date"
 *
 * @param string        $strName        input name
 * @param Date          $date           input value
 * @param Date|null     $min            Минимальное значение даты
 * @param Date|null     $max            Максимальное значение даты
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputDate($strName, Date $date=null, Date $min=null, Date $max=null, $field1='', $strId='')
{
	if (!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='min="'.$min->getDateDB().'"';
	}

	if (!is_null($max))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="'.$max->getDateDB().'"';
	}
	elseif (!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$max = new Date('2038-01-18 23:00:00','db_datetime');
		$field1.='max="'.$max->getDateDB().'"';
	}

	if (!is_null($date))
	{
		$date = $date->getDateDB();
	}
	else
	{
		$date = '';
	}

	return InputType ('date', $strName, $date, '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "time"
 *
 * @param string        $strName        input name
 * @param Date          $time           input value
 * @param Date|null     $min            Минимальное значение времени
 * @param Date|null     $max            Максимальное значение времени
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputTime($strName, Date $time, Date $min=null, Date $max=null, $field1="", $strId="")
{
	if (!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='min="'.$min->getTime().'"';
	}

	if (!is_null($max))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="'.$max->getTime().'"';
	}
	elseif(!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="23:59:59"';
	}

	return InputType ('time', $strName, $time->getTime(), '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "datetime"
 *
 * @param string        $strName    input name
 * @param Date          $datetime   input value
 * @param Date|null     $min        Минимальное значение даты
 * @param Date|null     $max        Максимальное значение даты
 * @param string        $field1     Дополнительный вывод данных для input
 * @param string        $strId      input id
 *
 * @return string
 */
function InputDateTime($strName, Date $datetime, Date $min=null, Date $max=null, $field1='', $strId='')
{
	if (!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='min="'.$min->getDate('Y-m-dTH:i:s').'"';
	}

	if (!is_null($max))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="'.$max->getDate('Y-m-dTH:i:s').'"';
	}
	elseif(!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="2038-01-18T23:59:59"';
	}

	return InputType ('datetime', $strName, $datetime->getDate('Y-m-dTH:i:s'), '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "datetime-local"
 *
 * @param string        $strName        input name
 * @param Date          $datetime       input value
 * @param Date|null     $min            Минимальное значение даты
 * @param Date|null     $max            Максимальное значение даты
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputDateTimeLocal($strName, Date $datetime, Date $min=null, Date $max=null, $field1='', $strId='')
{
	if (!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='min="'.$min->getDate('Y-m-dTH:i:s').'"';
	}

	if (!is_null($max))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="'.$max->getDate('Y-m-dTH:i:s').'"';
	}
	elseif(!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="2038-01-18T23:59:59"';
	}

	return InputType ('datetime-local', $strName, $datetime->getDate('Y-m-dTH:i:s'), '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "month"
 *
 * @param string        $strName        input name
 * @param Date          $month          input value
 * @param Date|null     $min            Минимальное значение даты
 * @param Date|null     $max            Максимальное значение даты
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputMonth($strName, Date $month, Date $min=null, Date $max=null, $field1="", $strId="")
{
	if (!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='min="'.$min->getDate('Y-m').'"';
	}
	if (!is_null($max))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="'.$max->getDate('Y-m').'"';
	}
	elseif(!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="9999-12"';
	}

	return InputType ('month', $strName, $month->getDate('Y-m'), '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "week"
 *
 * @param string        $strName        input name
 * @param Date          $week           input value
 * @param Date          $min            Минимальное значение даты
 * @param Date          $max            Максимальное значение даты
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputWeek($strName, Date $week, Date $min=null, Date $max=null, $field1="", $strId="")
{
	if (!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='min="'.$min->getDate('Y-W').'"';
	}
	if (!is_null($max))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="'.$max->getDate('Y-W').'"';
	}
	elseif(!is_null($min))
	{
		if ($field1!='')
		{
			$field1.=' ';
		}
		$field1.='max="2038-2"';
	}

	return InputType ('week', $strName, $week->getDate('Y-W'), '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "search"
 *
 * @param string        $strName        input name
 * @param string        $strValue       input value
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputSearch($strName, $strValue, $field1="", $strId="")
{
	return InputType ('search', $strName, $strValue, '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "color"
 *
 * @param string        $strName        input name
 * @param string        $strValue       input value
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputColor($strName, $strValue, $field1="", $strId="")
{
	return InputType ('color', $strName, $strValue, '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "url"
 *
 * @param string        $strName        input name
 * @param string        $strValue       input value
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputUrl($strName, $strValue, $field1="", $strId="")
{
	return InputType ('url', $strName, $strValue, '', false, '', $field1, $strId);
}

/**
 * Returns HTML "input" type "range"
 *
 * @param string        $strName        input name
 * @param string        $strValue       input value
 * @param int|float     $min            Минимальное значение диапазона
 * @param int|float     $max            Максимальное значение диапазона
 * @param int|float     $step           Шаг числа, также указывает возможное количество знаков после запятой
 * @param string        $field1         Дополнительный вывод данных для input
 * @param string        $strId          input id
 *
 * @return string
 */
function InputRange($strName, $strValue, $min, $max, $step=1, $field1="", $strId="")
{
	if ($field1!='')
	{
		$field1.=' ';
	}
	$field1.='min="'.$min.'" max="'.$max.'"';
	$field1.=' step="'.$step.'"';
	$field1.=' oninput="showVal_'.$strName.'(this.value)" onchange="showVal_'.$strName.'(this.value)"';
	$return = InputType ('range', $strName, $strValue, '', false, '', $field1, $strId);
	$return .= '<span id="range-info-'.$strName.'">'.$strValue.'</span>'
		.'<script>function showVal_'.$strName.'(val){document.getElementById("range-info-'.$strName.'").innerHTML=val;'
		.'}</script>';

	return $return;
}

function InputFile($strName, $strValue, $field1="", $strId="")
{
	return InputType('file',$strName, $strValue, '', false, '', $field1, $strId);
}

/**
 * Returns HTML "select"
 *
 * @param string        $strBoxName     Input name
 * @param array         $arValues       Array with items
 * @param string        $strDetText     Empty item text
 * @param string|int    $strSelectedVal Selected item value
 * @param string        $field1         Additional attributes
 *
 * @throws Exception\ArgumentTypeException Если параметры неверных типов
 *
 * @return string
 */
function SelectBox($strBoxName, $arValues, $strDetText = "", $strSelectedVal = "null", $field1="class=\"typeselect\"")
{
	try
	{
		if (!is_string($strBoxName))
		{
			throw new Exception\ArgumentTypeException('$strBoxName','string');
		}
		if (!is_array($arValues))
		{
			throw new Exception\ArgumentTypeException('$arValues','array');
		}
		if (!is_string($strDetText))
		{
			throw new Exception\ArgumentTypeException('$strDetText','string');
		}
		if (!is_string($strSelectedVal) && !is_numeric($strSelectedVal))
		{
			throw new Exception\ArgumentTypeException('$strSelectedVal','string');
		}
		if (!is_string($field1))
		{
			throw new Exception\ArgumentTypeException('$field1','string');
		}
	}
	catch (Exception\ArgumentTypeException $e)
	{
		die($e->showException());
	}

	$strReturnBox = "<select ".$field1." name=\"".$strBoxName."\" id=\"".$strBoxName."\">";
	if ($strDetText <> '')
	{
		$strReturnBox = $strReturnBox."<option value=\"\"";
		if (is_null($strSelectedVal) || $strSelectedVal == '' || strtolower($strSelectedVal) == "null")
		{
			$strReturnBox.= " selected";
		}
		$strReturnBox.= ">".$strDetText."</option>";
	}
	if (empty($arValues))
	{
		return false;
	}
	foreach ($arValues as $arValue) {
		$strReturnBox = $strReturnBox."<option ";
		if (
			(isset($arValue["SELECTED"]) && $arValue["SELECTED"])
			|| ($strSelectedVal != "" && $strSelectedVal == $arValue["VALUE"])
		)
		{
			$strReturnBox = $strReturnBox." selected ";
		}
		$strReturnBox = $strReturnBox."value=\"".$arValue["VALUE"]. "\">".$arValue["NAME"]."</option>";
	}
	return $strReturnBox."</select>";
}

/**
 * @param        $strBoxName
 * @param string $strSelectedVal
 * @param string $strYes
 * @param string $strNo
 * @param string $field1
 *
 * @return string
 * @throws Exception\ArgumentTypeException
 */
function SelectBoxBool ($strBoxName, $strSelectedVal = "", $strYes='', $strNo='', $field1="class=\"typeselect\"")
{
	Lib\Loc::includeLocFile(__FILE__,'ms_core_');

	if ($strYes == '') $strYes = Lib\Loc::getModuleMessage('core','core_yes'); // 'Да';
	if ($strNo == '') $strNo = Lib\Loc::getModuleMessage('core','core_no'); // 'Нет';
	if ($strSelectedVal == "") $strSelectedVal = 0;
	if (is_bool($strSelectedVal))
	{
		if ($strSelectedVal===true)
		{
			$strSelectedVal = 1;
		}
		else
		{
			$strSelectedVal = 0;
		}
	}

	$arValues = array(
		array(
			'VALUE' => 0,
			'NAME' => $strNo
		),
		array(
			'VALUE' => 1,
			'NAME' => $strYes
		)
	);

	return SelectBox($strBoxName, $arValues, '', $strSelectedVal, $field1);
}

/**
 * Returns HTML multiple "select"
 *
 * @param string    $strBoxName             Input name
 * @param array     $arValues               Array with items
 * @param string    $strDetText             Empty item text
 * @param bool      $strDetText_selected    Allow to choose an empty item
 * @param string    $size                   Size attribute
 * @param string    $field1                 Additional attributes
 * @return string
 */
function SelectBoxM($strBoxName, $arValues, $strDetText = "", $strDetText_selected = false, $size = "5", $field1="class=\"typeselect\"")
{
	$strReturnBox = "<select ".$field1." multiple name=\"".$strBoxName."\" id=\"".$strBoxName."\" size=\"".$size."\">";
	if ($strDetText <> '')
	{
		$strReturnBox = $strReturnBox."<option ";
		if ($strDetText_selected)
			$strReturnBox = $strReturnBox." selected ";
		$strReturnBox = $strReturnBox." value='NULL'>".$strDetText."</option>";
	}
	foreach ($arValues as $arValue) {
		$strReturnBox = $strReturnBox."<option ";
		if (isset($arValue["SELECTED"]) && $arValue["SELECTED"])
		{
			$strReturnBox = $strReturnBox." selected ";
		}
		$strReturnBox = $strReturnBox."value=\"".$arValue["VALUE"]. "\">".$arValue["NAME"]."</option>";
	}
	return $strReturnBox."</select>";
}

/**
 * Show Input for select Date
 *
 * @param string $strName
 * @param string $strValue
 * @param string $field1
 * @param string $strId
 *
 * @throw Exception\ArgumentNullException
 *
 * @return string
 */
function InputCalendar ($strName, $strValue="", $field1="", $strId="")
{
	try
	{
		if (strlen($strName)==0)
		{
			throw new Exception\ArgumentNullException("strName");
		}
		if (strlen($strValue)==0 && !is_null($strValue)) $strValue = date("d.m.Y");
		//Lib\Buffer::addJS(Lib\Config::getConfig("CORE_ROOT")."js/calendar.js");
		//Lib\Plugins::includeInputCalendar();
		if (!Application::getInstance()->includePlugin('input-calendar'))
		{
			return '';
		}
		//Lib\Buffer::addJS(Lib\Config::getConfig("CORE_ROOT")."plugins/jquery.maskedinput/jquery.maskedinput.min.js");
		//Lib\Plugins::includeMaskedInput();
		if (!Application::getInstance()->includePlugin('masked-input'))
		{
			return '';
		}
		$strReturnBox = "<input ".$field1." type=\"date\" id=\"".(($strId!="")?$strId:$strName);
		$strReturnBox.= "\" name=\"".$strName."\" value=\"".$strValue."\"";
		$strReturnBox.= " onfocus=\"this.select();lcs(this)\"";
		$strReturnBox.= " onclick=\"event.cancelBubble=true;this.select();lcs(this)\"";
		$strReturnBox.= ">\n";
		$strReturnBox.= "<script>\n";
		$strReturnBox.= "$(function() {\n";
		$strReturnBox.= "\$('#".(($strId!="")?$strId:$strName)."').mask('99.99.9999', {placeholder: 'дд.мм.гггг' });\n";
		$strReturnBox.= "});\n";
		$strReturnBox.= "</script>\n";

		return $strReturnBox;
	}
	catch (Exception\ArgumentNullException $e)
	{
		$e->showException();
	}

	return '';
}

function LineCharts ($arParams = null)
{
	/*
	$arParams = array();
	$arParams['title'] = 'Средняя месячная температура';
	$arParams['subtitle'] = 'Источник: WorldClimate.com';
	$arParams['xAxis'] = array('Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек');
	$arParams['yAxis'] = 'Температура (°C)';
	$arParams['valueSuffix'] = '°C';
	$arParams['series'] = array(
		0 => array(
			'name' => 'Токио',
			'data' => array(7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6)
		),
		1 => array(
			'name' => 'Нью-Йорк',
			'data' => array(-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5)
		),
		2 => array(
			'name' => 'Берлин',
			'data' => array(-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0)
		),
		3 => array(
			'name' => 'Лондон',
			'data' => array(3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8)
		)
	);
	*/
	try
	{
		if (is_null($arParams))
		{
			throw new Exception\ArgumentNullException('arParams');
		}
		else
		{
			if (!isset($arParams['title']))
			{
				$arParams['title'] = 'График';
			}
			if (!isset($arParams['subtitle']))
			{
				$arParams['subtitle'] = 'MajorDoMo';
			}
			if (!isset($arParams['xAxis']))
			{
				throw new Exception\ArgumentNullException('arParams[xAxis]');
			}
			elseif (!is_array($arParams['xAxis']))
			{
				throw new Exception\ArgumentOutOfRangeException('arParams[xAxis]');
			}
			if (!isset($arParams['yAxis']))
			{
				throw new Exception\ArgumentNullException('arParams[yAxis]');
			}
			if (!isset($arParams['valueSuffix']))
			{
				throw new Exception\ArgumentNullException("arParams[valueSuffix]");
			}
			if (!isset($arParams['series']))
			{
				throw new Exception\ArgumentNullException('arParams[series]');
			}
			if (!isset($arParams['container']))
			{
				$containerName = $arParams['container'] = 'line_charts';
			}
			else
			{
				$containerName = $arParams['container'];
			}
		}
	}
	catch (Exception\ArgumentNullException $e)
	{
		die($e->showException());
	}
	catch (Exception\ArgumentOutOfRangeException $e1)
	{
		die($e1->showException());
	}

	if (!Application::getInstance()->addJS(Application::getInstance()->getSettings()->getCoreRoot()."/js/highcharts.js")
		|| !Application::getInstance()->addJS (Application::getInstance()->getSettings()->getCoreRoot()."/js/exporting.js"))
	{
		return '';
	}

	$arHighCharts = array (
		'title' => array(
			'text' => $arParams['title'],
			'x' => -20 //center
		),
		'subtitle' => array(
			'text' => $arParams['subtitle'],
			'x' => -20
		),
		'xAxis' => array(
			'categories' => $arParams['xAxis']
		),
		'yAxis' => array(
			'title' => array(
				'text' => $arParams['yAxis']
			),
			'plotLines' => array(
				0 => array(
					'value' => 0,
					'width' => 1,
					'color' => '#808080'
				)
			)
		),
		'tooltip' => array(
			'valueSuffix' => $arParams['valueSuffix']
		),
		'legend' => array(
			'layout' => 'vertical',
			'align' => 'right',
			'verticalAlign' => 'middle',
			'borderWidth' => 0
		),
		'series' => $arParams['series']
	);
	$echo = '<div id="'.$containerName.'" style="min-width: 310px; height: 400px; margin: 0 auto"></div>'."\n";
	$echo .= '<script type="text/javascript">'."\n\t"
		."\$(function () {\n\t\t"
		."\$('#".$containerName."').highcharts(".json_encode($arHighCharts).");\n\t"
		."});\n"
		."</script>\n";

	return $echo;
}

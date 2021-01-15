<?php
/**
 * Компонент ядра ms:menu
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Components;

use Ms\Core\Entity\System\Application;

class MenuComponent extends Component
{
	public function __construct ($component, $template='.default', $arParams=array())
	{
		parent::__construct($component,$template,$arParams);
	}

	public function run ()
	{
		$arParams = &$this->arParams;
		$arResult = &$this->arResult;
		$app = Application::getInstance();
		/*
			Array
			(
			    [0] => пункт меню 1
			        Array
			            (
			                [0] => заголовок пункта меню
			                [1] => ссылка на пункте меню
			                [2] => массив дополнительных ссылок для подсветки пункта меню:
			                    Array
			                        (
			                            [0] => ссылка 1
			                            [1] => ссылка 2
			                            ...
			                         )
			                [3] => массив дополнительных переменных передаваемых в шаблон меню:
			                    Array
			                        (
			                            [имя переменной 1] => значение переменной 1
			                            [имя переменной 2] => значение переменной 2
			                            ...
			                         )
			                [4] => условие, при котором пункт меню появляется
			                       это PHP выражение, которое должно вернуть "true"
			            )
			    [1] => пункт меню 2
			    [2] => пункт меню 3
			)
		 */

		if (isset($arParams['MAIN_MENU_TYPE']) && !is_null($arParams['MAIN_MENU_TYPE']))
		{
			$arParams['MAIN_MENU_URL'] = $app->getNearestFile('.'.$arParams['MAIN_MENU_TYPE'].'.menu.php');
			if ($arParams['MAIN_MENU_URL'])
			{
				$arParams['MAIN_MENU_DATA'] = include($arParams['MAIN_MENU_URL']);
			}
			else
			{
				$arParams['MAIN_MENU_URL'] = $app->getNearestFile('.'.$arParams['MAIN_MENU_TYPE'].'.menu.alt.php');
				if ($arParams['MAIN_MENU_URL'])
				{
					$arParams['MAIN_MENU_DATA'] = include($arParams['MAIN_MENU_URL']);
				}
			}
		}
		else
		{
			$arParams['MAIN_MENU_URL'] = false;
		}

		if (isset($arParams['SECOND_MENU_TYPE']) && !is_null($arParams['SECOND_MENU_TYPE']))
		{
			$arParams['SECOND_MENU_URL'] = $app->getNearestFile('.'.$arParams['SECOND_MENU_TYPE'].'.menu.php');
			if ($arParams['SECOND_MENU_URL'])
			{
				$arParams['SECOND_MENU_DATA'] = include($arParams['SECOND_MENU_URL']);
			}
			else
			{
				$arParams['SECOND_MENU_URL'] = $app->getNearestFile('.'.$arParams['SECOND_MENU_TYPE'].'.menu.alt.php');
				if ($arParams['SECOND_MENU_URL'])
				{
					$arParams['SECOND_MENU_DATA'] = include($arParams['SECOND_MENU_URL']);
				}
			}
		}
		else
		{
			$arParams['SECOND_MENU_URL'] = false;
		}

		if (isset($arParams['THIRD_MENU_TYPE']) && !is_null($arParams['THIRD_MENU_TYPE']))
		{
			$arParams['THIRD_MENU_URL'] = $app->getNearestFile('.'.$arParams['THIRD_MENU_TYPE'].'.menu.php');
			if ($arParams['THIRD_MENU_TYPE'])
			{
				$arParams['THIRD_MENU_DATA'] = include($arParams['THIRD_MENU_URL']);
			}
			else
			{
				$arParams['THIRD_MENU_URL'] = $app->getNearestFile('.'.$arParams['THIRD_MENU_TYPE'].'.menu.alt.php');
				if ($arParams['THIRD_MENU_TYPE'])
				{
					$arParams['THIRD_MENU_DATA'] = include($arParams['THIRD_MENU_URL']);
				}
			}
		}
		else
		{
			$arParams['THIRD_MENU_URL'] = false;
		}

		$arResult['ITEMS'] = array();
		if (isset($arParams['MAIN_MENU_DATA']))
		{
			$i=0;
			foreach ($arParams['MAIN_MENU_DATA'] as $ar_menu)
			{
				$arResult['ITEMS'][$i] = $this->getMenuData($i,$ar_menu);
				$i++;
			}
		}

		$this->includeTemplate();

		//msDebugNoAdmin($arParams);
		//msDebugNoAdmin($arResult);
		//msDebugNoAdmin($app);
	}

	private function getMenuData (&$i, $arMenu)
	{
		/*
		    TEXT - заголовок пункта меню;
			LINK - ссылка на пункте меню;
			SELECTED - активен ли пункт меню в данный момент, возможны следующие значения:
			true - пункт меню выбран;
			false - пункт меню не выбран;
			ADDITIONAL_LINKS - массив дополнительных ссылок для подсветки меню;
			ITEM_TYPE - флаг, указывающий на тип ссылки, указанной в LINK, возможны следующие значения:
			D - каталог (LINK заканчивается на "/");
			P - страница;
			U - страница с параметрами;
			ITEM_INDEX - порядковый номер пункта меню;
			PARAMS - ассоциативный массив параметров пунктов меню. Параметры задаются в расширенном режиме редактирования меню.
			SHOW - показывается ли пункт меню (true/false)
		 */
		$app = Application::getInstance();

		$arTmp['TEXT'] = $arMenu[0];
		$arTmp['LINK'] = $arMenu[1];
		if (strpos($app->getServer()->getScriptName(),$arTmp['LINK'])===false)
		{
			msDebug($arTmp['LINK']);
			$arTmp['SELECTED'] = false;
		}
		else
		{
			$arTmp['SELECTED'] = true;
		}
		if ($arTmp['LINK'][strlen($arTmp['LINK'])-1]=='/')
		{
			$arTmp['ITEM_TYPE'] = 'D';
		}
		elseif (strpos($arTmp['LINK'],'?')!==false)
		{
			$arTmp['ITEM_TYPE'] = 'U';
		}
		else
		{
			$arTmp['ITEM_TYPE'] = 'P';
		}
		$arTmp['ADDITIONAL_LINKS'] = $arMenu[2];
		$arTmp['ITEM_INDEX'] = $i;
		$arTmp['PARAMS'] = $arMenu[3];
		if ($arMenu[5]=='')
		{
			$arTmp['SHOW'] = true;
		}
		else
		{
			$arTmp['SHOW'] = eval($arMenu[5]);
		}
		if (!empty($arMenu[4]))
		{
			$k=0;
			foreach ($arMenu[4] as $ar_child)
			{
				$arTmp['CHILD'][$k] = $this->getMenuData($k,$ar_child);
				$k++;
			}
		}
		else
		{
			$arTmp['CHILD'] = array();
		}

		return $arTmp;
	}
}
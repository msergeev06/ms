<?php
/**
 * Компонент ядра ms:table
 *
 * @package Ms\Core
 * @subpackage Entity\Components
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Components;

use Ms\Core\Entity\System\Application;

class TableComponent extends Component
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

		foreach ($arParams['TABLE_HEADER'] as $field=>&$arTd)
		{
			$arTd['_STYLE'] = ' style="';
			if (isset($arTd['ALIGN']))
			{
				if (strtoupper($arTd['ALIGN'])=='CENTER')
				{
					$arTd['_ALIGN'] = 'text-align: center;';
				}
				elseif (strtoupper($arTd['ALIGN'])=='RIGHT')
				{
					$arTd['_ALIGN'] = 'text-align: right;';
				}
				else
				{
					$arTd['_ALIGN'] = 'text-align: left;';
				}
			}
			else
			{
				$arTd['_ALIGN'] = 'text-align: left;';
			}
			$arTd['_STYLE'] .= $arTd['_ALIGN'];
			$arTd['_STYLE'] .= '"';

			$arTd['TD'] = $arTd['_STYLE'];
			if (!is_null($arTd['TITLE']))
			{
				$arTd['VALUE'] = $arTd['TITLE'];
			}
			else
			{
				$arTd['VALUE'] = '&nbsp;';
			}

		}
		unset($arTd);
		$arResult['TABLE_DATA'] = array();
		foreach ($arParams['TABLE_DATA'] as $i=>$arData)
		{
			foreach ($arData as $field=>$value)
			{
				$arResult['TABLE_DATA'][$i][$field]['RAW_VALUE'] = $value;
				$type = $arParams['TABLE_HEADER'][$field]['TYPE'];
				if ($type=='DATE')
				{
					switch($arParams['TABLE_HEADER'][$field]['SUB_TYPE'])
					{
						case 'DATE_SITE':
							$arResult['TABLE_DATA'][$i][$field]['VALUE'] = $arResult['TABLE_DATA'][$i][$field]['RAW_VALUE']->getDateSite();
							break;
						case 'DATE_DB':
							$arResult['TABLE_DATA'][$i][$field]['VALUE'] = $arResult['TABLE_DATA'][$i][$field]['RAW_VALUE']->getDateDB();
							break;
						case 'DATETIME_SITE':
							$arResult['TABLE_DATA'][$i][$field]['VALUE'] = $arResult['TABLE_DATA'][$i][$field]['RAW_VALUE']->getDateTimeSite();
							break;
						case 'DATETIME_DB':
							$arResult['TABLE_DATA'][$i][$field]['VALUE'] = $arResult['TABLE_DATA'][$i][$field]['RAW_VALUE']->getDateTimeDB();
							break;
						default:
							$arResult['TABLE_DATA'][$i][$field]['VALUE'] = $arResult['TABLE_DATA'][$i][$field]['RAW_VALUE']->getTimestamp();
							break;
					}
				}
				elseif ($type=='TEMPLATE')
				{
					$arResult['TABLE_DATA'][$i][$field]['VALUE'] = $arParams['TABLE_HEADER'][$field]['TEMPLATE'];
					foreach ($arResult['TABLE_DATA'][$i][$field]['RAW_VALUE'] as $tmpl=>$val)
					{
						$arResult['TABLE_DATA'][$i][$field]['VALUE'] = str_replace('#'.$tmpl.'#',$val,$arResult['TABLE_DATA'][$i][$field]['VALUE']);
					}
				}
				elseif ($type=='BOOL')
				{
					if (isset($arParams['TABLE_HEADER'][$field]['VALUE_TRUE']))
					{
						$yes = $arParams['TABLE_HEADER'][$field]['VALUE_TRUE'];
					}
					else
					{
						$yes = 1;
					}
					if (isset($arParams['TABLE_HEADER'][$field]['VALUE_FALSE']))
					{
						$no = $arParams['TABLE_HEADER'][$field]['VALUE_FALSE'];
					}
					else
					{
						$no = 0;
					}

					if ($arResult['TABLE_DATA'][$i][$field]['RAW_VALUE'])
					{
						$arResult['TABLE_DATA'][$i][$field]['VALUE'] = $yes;
					}
					else
					{
						$arResult['TABLE_DATA'][$i][$field]['VALUE'] = $no;
					}
				}
				elseif ($type=='FLOAT')
				{
					if (isset($arParams['TABLE_HEADER'][$field]['SCALE']))
					{
						$scale = $arParams['TABLE_HEADER'][$field]['SCALE'];
					}
					else
					{
						$scale = 2;
					}
					if (isset($arParams['TABLE_HEADER'][$field]['DECIMALS']))
					{
						$dec = $arParams['TABLE_HEADER'][$field]['DECIMALS'];
					}
					else
					{
						$dec = '.';
					}
					if (isset($arParams['TABLE_HEADER'][$field]['THOUSAND']))
					{
						$thousand = $arParams['TABLE_HEADER'][$field]['THOUSAND'];
					}
					else
					{
						$thousand = ' ';
					}

					$arResult['TABLE_DATA'][$i][$field]['VALUE'] = number_format($arResult['TABLE_DATA'][$i][$field]['RAW_VALUE'],$scale,$dec,$thousand);
				}
				else
				{
					$arResult['TABLE_DATA'][$i][$field]['VALUE'] = $arResult['TABLE_DATA'][$i][$field]['RAW_VALUE'];
				}
			}
		}
		if (isset($arParams['TABLE_FOOTER']))
		{
			$arResult['TABLE_FOOTER'] = array();
			foreach ($arParams['TABLE_FOOTER'] as $i=>$arFooter)
			{
				foreach($arFooter as $code=>$arTd)
				{
					if (isset($arTd['COLSPAN'])||isset($arTd['ALIGN']))
					{
						$td = '';

						if (isset($arTd['COLSPAN'])&&intval($arTd['COLSPAN'])>1)
						{
							$td .= ' colspan="'.intval($arTd['COLSPAN']).'"';
						}

						if (isset($arTd['ALIGN']))
						{
							$td .= ' style="';
							switch($arTd['ALIGN'])
							{
								case 'CENTER':
									$td .= 'text-align: center;';
									break;
								case 'RIGHT':
									$td .= 'text-align: right;';
									break;
								default:
									$td .= 'text-align: left;';
							}
							$td .= '"';
						}

						$arResult['TABLE_FOOTER'][$i][$code]['TD'] = $td;
					}
					if (isset($arTd['VALUE']))
					{
						$value = $arTd['VALUE'];
					}
					else
					{
						$value = '&nbsp;';
					}
					$arResult['TABLE_FOOTER'][$i][$code]['VALUES'] = $value;
				}
			}
		}


		//msDebugNoAdmin($arParams);
		//msDebugNoAdmin($arResult);
		$this->includeTemplate();

	}

}
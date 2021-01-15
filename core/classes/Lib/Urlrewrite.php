<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Tables\UrlrewriteTable;

/**
 * Класс Ms\Core\Lib\Urlrewrite
 * Обработка страниц при помощи urlrewrite
 */
class Urlrewrite
{
    /**
     * @param string $sComponentName Имя компонента
     *
     * @return array|bool
     */
    public static function getConditionArray ($sComponentName = null)
    {
        if (!is_null($sComponentName))
        {
            $arFilter = ['COMPONENT_NAME' => $sComponentName];
        }
        else
        {
            $arFilter = [];
        }
        try
        {
            $orm = ORMController::getInstance(new UrlrewriteTable());
            $arRes = $orm->getList(
                [
                    'filter' => $arFilter
                ]
            );
        }
        catch (SystemException $e)
        {
            return [];
        }

        if ($arRes)
        {
            return $arRes;
        }

        return [];
    }

    /**
     * @param $arCondition
     */
    public static function sortConditionArray (&$arCondition)
    {
        if (!empty($arCondition))
        {
            $arTmp = $arSort = [];
            foreach ($arCondition as $ar_cond)
            {
                $arTmp[] = $ar_cond;
                $arSort[] = strlen($ar_cond['CONDITION']);
            }
            $arCondition = [];
            arsort($arSort);
            foreach ($arSort as $id => $sort)
            {
                $arCondition[] = $arTmp[$id];
            }
        }
    }
}
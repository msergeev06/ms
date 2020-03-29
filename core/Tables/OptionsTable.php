<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Lib\DataManager;

/**
 * Класс Ms\Core\Tables\OptionsTable
 * ORM таблицы "Настройки" (ms_core_options)
 */
class OptionsTable extends DataManager
{
    public static function getTableTitle()
    {
        return "Настройки";
    }

    protected static function getMap()
    {
        return [
            (new Fields\IntegerField ('ID'))
                ->setPrimary()
                ->setAutocomplete()
                ->setTitle('ID настройки')
            ,
            (new Fields\StringField ('NAME'))
                ->setTitle('Имя настройки')
            ,
            (new Fields\StringField ('VALUE'))
                ->setTitle('Значение настройки')
        ];
    }

    public static function getValues()
    {
        return [
            [
                'NAME'  => 'MS_CORE_SORT_DEFAULT',
                'VALUE' => 500
            ]
        ];
    }

}
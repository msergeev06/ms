<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Tables\FieldsCollection;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Tables\OptionsTable
 * ORM таблицы "Настройки" (ms_core_options)
 */
class OptionsTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return "Настройки";
    }

    public function getMap (): FieldsCollection
    {
        return (new FieldsCollection())
            ->addField(
                (new Fields\IntegerField ('ID'))
                    ->setPrimary()
                    ->setAutocomplete()
                    ->setTitle('ID настройки')
            )
            ->addField(
                (new Fields\StringField ('MODULE_NAME'))
                    ->setTitle('Имя модуля настройки')
            )
            ->addField(
                (new Fields\StringField ('NAME'))
                    ->setTitle('Имя настройки')
            )
            ->addField(
                (new Fields\TextField('VALUE'))
                    ->setTitle('Значение настройки')
            )
        ;
    }

    public function getDefaultRowsArray (): array
    {
        return [
            [
                'MODULE_NAME' => 'core',
                'NAME'  => 'SORT_DEFAULT',
                'VALUE' => '500'
            ]
        ];
    }
}
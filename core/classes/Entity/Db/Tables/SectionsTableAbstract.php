<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Tables;

use Ms\Core\Lib\TableHelper;
use Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Entity\Db\Tables\SectionsTableAbstract
 * Абстрактный класс таблиц "Разделы"
 */
abstract class SectionsTableAbstract extends TreeTableAbstract
{
    /**
     * Тестовые данные для отладки
     *
     * @return array
     * @unittest
     */
    public function getDefaultRowsArray (): array
    {
        return [
            [
                'ID'           => 1,
                'NAME'         => 'Узел 1',
                'LEFT_MARGIN'  => 1,
                'RIGHT_MARGIN' => 32,
                'DEPTH_LEVEL'  => 1,
                'PARENT_ID'    => null
            ],
            [
                'ID'           => 2,
                'NAME'         => 'Узел 2',
                'LEFT_MARGIN'  => 2,
                'RIGHT_MARGIN' => 9,
                'DEPTH_LEVEL'  => 2,
                'PARENT_ID'    => 1
            ],
            [
                'ID'           => 3,
                'NAME'         => 'Узел 3',
                'LEFT_MARGIN'  => 10,
                'RIGHT_MARGIN' => 23,
                'DEPTH_LEVEL'  => 2,
                'PARENT_ID'    => 1
            ],
            [
                'ID'           => 4,
                'NAME'         => 'Узел 4',
                'LEFT_MARGIN'  => 24,
                'RIGHT_MARGIN' => 31,
                'DEPTH_LEVEL'  => 2,
                'PARENT_ID'    => 1
            ],
            [
                'ID'           => 5,
                'NAME'         => 'Узел 5',
                'LEFT_MARGIN'  => 3,
                'RIGHT_MARGIN' => 8,
                'DEPTH_LEVEL'  => 3,
                'PARENT_ID'    => 2
            ],
            [
                'ID'           => 6,
                'NAME'         => 'Узел 6',
                'LEFT_MARGIN'  => 11,
                'RIGHT_MARGIN' => 12,
                'DEPTH_LEVEL'  => 3,
                'PARENT_ID'    => 3
            ],
            [
                'ID'           => 7,
                'NAME'         => 'Узел 7',
                'LEFT_MARGIN'  => 13,
                'RIGHT_MARGIN' => 20,
                'DEPTH_LEVEL'  => 3,
                'PARENT_ID'    => 3
            ],
            [
                'ID'           => 8,
                'NAME'         => 'Узел 8',
                'LEFT_MARGIN'  => 21,
                'RIGHT_MARGIN' => 22,
                'DEPTH_LEVEL'  => 3,
                'PARENT_ID'    => 3
            ],
            [
                'ID'           => 9,
                'NAME'         => 'Узел 9',
                'LEFT_MARGIN'  => 25,
                'RIGHT_MARGIN' => 30,
                'DEPTH_LEVEL'  => 3,
                'PARENT_ID'    => 4
            ],
            [
                'ID'           => 10,
                'NAME'         => 'Узел 10',
                'LEFT_MARGIN'  => 4,
                'RIGHT_MARGIN' => 5,
                'DEPTH_LEVEL'  => 4,
                'PARENT_ID'    => 5
            ],
            [
                'ID'           => 11,
                'NAME'         => 'Узел 11',
                'LEFT_MARGIN'  => 6,
                'RIGHT_MARGIN' => 7,
                'DEPTH_LEVEL'  => 4,
                'PARENT_ID'    => 5
            ],
            [
                'ID'           => 12,
                'SORT'         => 100,
                'NAME'         => 'Узел 12',
                'LEFT_MARGIN'  => 14,
                'RIGHT_MARGIN' => 15,
                'DEPTH_LEVEL'  => 4,
                'PARENT_ID'    => 7
            ],
            [
                'ID'           => 13,
                'SORT'         => 200,
                'NAME'         => 'Узел 13',
                'LEFT_MARGIN'  => 16,
                'RIGHT_MARGIN' => 17,
                'DEPTH_LEVEL'  => 4,
                'PARENT_ID'    => 7
            ],
            [
                'ID'           => 14,
                'SORT'         => 300,
                'NAME'         => 'Узел 14',
                'LEFT_MARGIN'  => 18,
                'RIGHT_MARGIN' => 19,
                'DEPTH_LEVEL'  => 4,
                'PARENT_ID'    => 7
            ],
            [
                'ID'           => 15,
                'NAME'         => 'Узел 15',
                'LEFT_MARGIN'  => 26,
                'RIGHT_MARGIN' => 27,
                'DEPTH_LEVEL'  => 4,
                'PARENT_ID'    => 9
            ],
            [
                'ID'           => 16,
                'NAME'         => 'Узел 16',
                'LEFT_MARGIN'  => 28,
                'RIGHT_MARGIN' => 29,
                'DEPTH_LEVEL'  => 4,
                'PARENT_ID'    => 9
            ]
        ];
    }

    /**
     * <Описание>
     *
     * @return FieldsCollection
     * @unittest
     */
    public function getMap (): FieldsCollection
    {
        $treeCollection = parent::getMap();

        $sectionCollection = (new FieldsCollection())
            ->addField(
                TableHelper::sortField()
            )
            ->addField(
                (new Fields\StringField('NAME'))
                    ->setRequired()
                    ->setTitle('Название раздела')
            )
        ;

        $sectionCollection->merge($treeCollection);

        return $sectionCollection;
    }

    public function getTableTitle (): string
    {
        return 'Разделы';
    }
}
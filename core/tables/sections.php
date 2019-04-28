<?php
/**
 * Ms\Core\Tables\SectionsTable
 *
 * @package Ms\Core
 * @subpackage Tables
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Lib;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Db\DBResult;

class SectionsTable extends TreeTable
{
	public static function getTableTitle()
	{
		return 'Разделы';
	}

	protected static function getMap()
	{
		$arTreeFields = parent::getMap();

		$arSectionFields = [
			Lib\TableHelper::sortField(),
			new Fields\StringField('NAME',array(
				'required' => true,
				'title' => 'Название раздела'
			))
		];

		return array_merge($arTreeFields, $arSectionFields);
	}

	/**
	 * Тестовые данные для отладки
	 *
	 * @return array
	 */
	public static function getValues ()
	{
		return array(
			array(
				'ID' => 1,
				'NAME' => 'Узел 1',
				'LEFT_MARGIN' => 1,
				'RIGHT_MARGIN' => 32,
				'DEPTH_LEVEL' => 1,
				'PARENT_ID' => null
			),
			array(
				'ID' => 2,
				'NAME' => 'Узел 2',
				'LEFT_MARGIN' => 2,
				'RIGHT_MARGIN' => 9,
				'DEPTH_LEVEL' => 2,
				'PARENT_ID' => 1
			),
			array(
				'ID' => 3,
				'NAME' => 'Узел 3',
				'LEFT_MARGIN' => 10,
				'RIGHT_MARGIN' => 23,
				'DEPTH_LEVEL' => 2,
				'PARENT_ID' => 1
			),
			array(
				'ID' => 4,
				'NAME' => 'Узел 4',
				'LEFT_MARGIN' => 24,
				'RIGHT_MARGIN' => 31,
				'DEPTH_LEVEL' => 2,
				'PARENT_ID' => 1
			),
			array(
				'ID' => 5,
				'NAME' => 'Узел 5',
				'LEFT_MARGIN' => 3,
				'RIGHT_MARGIN' => 8,
				'DEPTH_LEVEL' => 3,
				'PARENT_ID' => 2
			),
			array(
				'ID' => 6,
				'NAME' => 'Узел 6',
				'LEFT_MARGIN' => 11,
				'RIGHT_MARGIN' => 12,
				'DEPTH_LEVEL' => 3,
				'PARENT_ID' => 3
			),
			array(
				'ID' => 7,
				'NAME' => 'Узел 7',
				'LEFT_MARGIN' => 13,
				'RIGHT_MARGIN' => 20,
				'DEPTH_LEVEL' => 3,
				'PARENT_ID' => 3
			),
			array(
				'ID' => 8,
				'NAME' => 'Узел 8',
				'LEFT_MARGIN' => 21,
				'RIGHT_MARGIN' => 22,
				'DEPTH_LEVEL' => 3,
				'PARENT_ID' => 3
			),
			array(
				'ID' => 9,
				'NAME' => 'Узел 9',
				'LEFT_MARGIN' => 25,
				'RIGHT_MARGIN' => 30,
				'DEPTH_LEVEL' => 3,
				'PARENT_ID' => 4
			),
			array(
				'ID' => 10,
				'NAME' => 'Узел 10',
				'LEFT_MARGIN' => 4,
				'RIGHT_MARGIN' => 5,
				'DEPTH_LEVEL' => 4,
				'PARENT_ID' => 5
			),
			array(
				'ID' => 11,
				'NAME' => 'Узел 11',
				'LEFT_MARGIN' => 6,
				'RIGHT_MARGIN' => 7,
				'DEPTH_LEVEL' => 4,
				'PARENT_ID' => 5
			),
			array(
				'ID' => 12,
				'SORT' => 100,
				'NAME' => 'Узел 12',
				'LEFT_MARGIN' => 14,
				'RIGHT_MARGIN' => 15,
				'DEPTH_LEVEL' => 4,
				'PARENT_ID' => 7
			),
			array(
				'ID' => 13,
				'SORT' => 200,
				'NAME' => 'Узел 13',
				'LEFT_MARGIN' => 16,
				'RIGHT_MARGIN' => 17,
				'DEPTH_LEVEL' => 4,
				'PARENT_ID' => 7
			),
			array(
				'ID' => 14,
				'SORT' => 300,
				'NAME' => 'Узел 14',
				'LEFT_MARGIN' => 18,
				'RIGHT_MARGIN' => 19,
				'DEPTH_LEVEL' => 4,
				'PARENT_ID' => 7
			),
			array(
				'ID' => 15,
				'NAME' => 'Узел 15',
				'LEFT_MARGIN' => 26,
				'RIGHT_MARGIN' => 27,
				'DEPTH_LEVEL' => 4,
				'PARENT_ID' => 9
			),
			array(
				'ID' => 16,
				'NAME' => 'Узел 16',
				'LEFT_MARGIN' => 28,
				'RIGHT_MARGIN' => 29,
				'DEPTH_LEVEL' => 4,
				'PARENT_ID' => 9
			)
		);
	}


    //<editor-fold defaultstate="collapse" desc=">> Методы взаимодействия с деревом разделов">
    /**
     * Добавляет новый раздел в дерево
     *
     * @api
     *
     * @param string   $sName     Имя раздела
     * @param null|int $iParent   Родительский раздел, если null - размещается в корне
     * @param int      $iSort     Индекс сортировки, по-умолчанию 500
     * @param bool     $bActive   Флаг активности раздела, по-умолчанию true - активен
     *
     * @return bool|int
     */
    final public static function addSection ($sName, $iParent=null, $iSort=500, $bActive=true)
    {
        $arSection = [
            'NAME' => $sName,
            'PARENT_ID' => $iParent,
            'SORT' => $iSort,
            'ACTIVE' => $bActive
        ];
        if ($iNewSectionID = static::addNode($arSection))
        {
            static::sortSection($iNewSectionID);
        }

        return $iNewSectionID;
    }

    /**
     * Сортирует раздел по индексу сортировки.
     * Если параметер sort указан, в начале обновляет значение этого параметра у раздела
     * Изменяет такие поля как LEFT_MARGIN и RIGHT_MARGIN
     *
     * @api
     *
     * @param int       $sectionID   ID раздела
     * @param int|null  $sort        Новый индекс сортировки, если необходим
     *
     * @return bool
     */
    final public static function sortSection ($sectionID, $sort=null)
    {
        //OK
        if (!is_null($sort))
        {
            static::updateSection($sectionID,['SORT'=>intval($sort)]);
        }

        return static::sortNode($sectionID, 'SORT','ASC');
    }

    /**
     * Определяет уровень родительского узла
     * Результат записывается в массив параметров, с ключем level_up
     *
     * @param array $arSection Массив полей раздела
     * @param array $arParams  Массив параметров, куда записывается уровень родительского раздела
     */
    final protected static function getSectionParentLevel ($arSection, &$arParams)
    {
        $parentLevel = static::getParentLevel($arSection['ID']);

        $arParams['level_up'] = (($parentLevel<=0)?0:$parentLevel);
    }

    /**
     * Обновляет указанный раздел, предварительно исключив неизменяемые поля
     *
     * @param int   $sectionID  ID изменяемого раздела
     * @param array $arUpdate   Массив со списком изменяемых полей.
     *                          Перед изменениями из массива будут исключены неизменяемые поля:
     *                          LEFT_MARGIN - левая граница;
     *                          RIGHT_MARGIN - правая граница;
     *                          DEPTH_LEVEL - уровень вложенности
     *
     * @return false|DBResult|string
     */
    final protected static function updateSection ($sectionID, $arUpdate)
    {
        static::checkUpdateFields($arUpdate);

        return static::update($sectionID,$arUpdate);
    }

    /**
     * Перемещает раздел в другой подраздел.
     * Изменяет поле DEPTH_LEVEL
     *
     * @param int       $sectionID      ID раздела
     * @param int|null  $newParentID    ID нового родительского раздела
     *
     * @return false;
     */
    public static function changeParent ($sectionID, $newParentID=null)
    {
        //OK
        if ($res = parent::changeParent($sectionID, $newParentID))
        {
            return static::sortSection($sectionID);
        }

        return false;
    }

    /**
     * Возвращает данные родительского раздела (осуществляет поиск по дереву разделов), либо false
     *
     * @param int   $sectionID      ID раздела
     * @param array $arSelect       Массив возвращаемых полей
     * @param bool  $mainSection    Флаг, обозначающий необходимость вернуть самый верхний раздел
     *
     * @return array|bool
     */
    final protected static function getParentFromTree ($sectionID, $arSelect = [], $mainSection=false)
    {
        if ((int)$sectionID>0)
        {
            if (!$mainSection)
            {
                return static::getParentInfo((int)$sectionID, $arSelect);
            }

            $arParents = static::getParents((int)$sectionID, $arSelect);
            if ($arParents && isset($arParents[0]))
            {
                return $arParents[0];
            }
        }

        return false;
    }

    /**
     * Удаляет указанный раздел
     *
     * @api
     *
     * @param int $sectionID ID удаляемого раздела
     *
     * @return bool
     */
    final public static function deleteSection ($sectionID)
    {
        return static::deleteNode($sectionID);
    }

    /**
     * Деактивирует раздел
     *
     * @api
     *
     * @param int $sectionID ID раздела
     *
     * @return DBResult
     */
    final public static function deactivateSection($sectionID)
    {
        return static::deactivateNode($sectionID);
    }

    /**
     * Активирует раздел
     *
     * @api
     *
     * @param int $sectionID ID раздела
     *
     * @return DBResult
     */
    final public static function activateSection ($sectionID)
    {
        return static::activateNode($sectionID);
    }
    //</editor-fold>


}
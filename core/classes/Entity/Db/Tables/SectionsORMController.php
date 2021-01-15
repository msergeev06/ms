<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Tables;

use Ms\Core\Entity\Db\Result\DBResult;

/**
 * Класс Ms\Core\Entity\Db\Tables\SectionsORMController
 * ORM контроллер для дерева разделов
 */
class SectionsORMController extends TreeORMController
{
    /** @var null|SectionsTableAbstract */
    protected $table = null;

    /**
     * Защищенный конструктор класса SectionsORMController
     *
     * @param SectionsTableAbstract $table
     */
    protected function __construct (SectionsTableAbstract $table)
    {
        $this->table = $table;
    }

    /**
     * Перемещает раздел в другой подраздел.
     * Изменяет поле DEPTH_LEVEL
     *
     * @param int      $sectionID   ID раздела
     * @param int|null $newParentID ID нового родительского раздела
     *
     * @return false
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentTypeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     * @throws \Ms\Core\Exceptions\Db\ValidateException
     * @unittest
     */
    public function changeParent ($sectionID, $newParentID = null)
    {
        //OK
        if ($res = parent::changeParent($sectionID, $newParentID))
        {
            return $this->sortSection($sectionID);
        }

        return false;
    }

    /**
     * Возвращает объект таблицы
     *
     * @return SectionsTableAbstract|null
     * @unittest
     */
    public function getTable ()
    {
        return $this->table;
    }

    /**
     * Активирует раздел
     *
     * @param int $sectionID ID раздела
     *
     * @return DBResult
     * @unittest
     */
    final public function activateSection ($sectionID)
    {
        return $this->activateNode($sectionID);
    }

    /**
     * Добавляет новый раздел в дерево
     *
     * @param string   $sName   Имя раздела
     * @param null|int $iParent Родительский раздел, если null - размещается в корне
     * @param int      $iSort   Индекс сортировки, по-умолчанию 500
     * @param bool     $bActive Флаг активности раздела, по-умолчанию true - активен
     *
     * @return bool|int
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentTypeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     * @throws \Ms\Core\Exceptions\Db\ValidateException
     * @unittest
     */
    final public function addSection ($sName, $iParent = null, $iSort = 500, $bActive = true)
    {
        $arSection = [
            'NAME'      => $sName,
            'PARENT_ID' => $iParent,
            'SORT'      => $iSort,
            'ACTIVE'    => $bActive
        ];
        if ($iNewSectionID = $this->addNode($arSection))
        {
            $this->sortSection($iNewSectionID);
        }

        return $iNewSectionID;
    }

    /**
     * Деактивирует раздел
     *
     * @param int $sectionID ID раздела
     *
     * @return DBResult
     * @unittest
     */
    final public function deactivateSection ($sectionID)
    {
        return $this->deactivateNode($sectionID);
    }

    /**
     * Удаляет указанный раздел
     *
     * @param int $sectionID ID удаляемого раздела
     *
     * @return bool
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentTypeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     * @unittest
     */
    final public function deleteSection ($sectionID)
    {
        return $this->deleteNode($sectionID);
    }

    /**
     * Сортирует раздел по индексу сортировки.
     * Если параметер sort указан, в начале обновляет значение этого параметра у раздела
     * Изменяет такие поля как LEFT_MARGIN и RIGHT_MARGIN
     *
     * @param int      $sectionID ID раздела
     * @param int|null $sort      Новый индекс сортировки, если необходим
     *
     * @return bool
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentTypeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     * @throws \Ms\Core\Exceptions\Db\ValidateException
     * @unittest
     */
    final public function sortSection ($sectionID, $sort = null)
    {
        //OK
        if (!is_null($sort))
        {
            $this->updateSection($sectionID, ['SORT' => intval($sort)]);
        }

        return $this->sortNode($sectionID, 'SORT', 'ASC');
    }

    /**
     * Возвращает данные родительского раздела (осуществляет поиск по дереву разделов), либо false
     *
     * @param int   $sectionID   ID раздела
     * @param array $arSelect    Массив возвращаемых полей
     * @param bool  $mainSection Флаг, обозначающий необходимость вернуть самый верхний раздел
     *
     * @return array|bool
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentTypeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     */
    final protected function getParentFromTree ($sectionID, $arSelect = [], $mainSection = false)
    {
        if ((int)$sectionID > 0)
        {
            if (!$mainSection)
            {
                return $this->getParentInfo((int)$sectionID, $arSelect);
            }

            $arParents = $this->getParents((int)$sectionID, $arSelect);
            if ($arParents && isset($arParents[0]))
            {
                return $arParents[0];
            }
        }

        return false;
    }

    /**
     * Определяет уровень родительского узла
     * Результат записывается в массив параметров, с ключем level_up
     *
     * @param array $arSection Массив полей раздела
     * @param array $arParams  Массив параметров, куда записывается уровень родительского раздела
     *
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentTypeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     */
    final protected function getSectionParentLevel ($arSection, &$arParams)
    {
        $parentLevel = $this->getParentLevel($arSection['ID']);

        $arParams['level_up'] = (($parentLevel <= 0) ? 0 : $parentLevel);
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
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     * @throws \Ms\Core\Exceptions\Db\ValidateException
     */
    final protected function updateSection ($sectionID, $arUpdate)
    {
        $this->checkUpdateFields($arUpdate);

        return $this->update($sectionID, $arUpdate);
    }
}
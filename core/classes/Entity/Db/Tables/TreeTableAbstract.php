<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Tables;

use Ms\Core\Entity\Db\Links\ForeignKey;
use Ms\Core\Entity\Db\Links\LinkedField;
use Ms\Core\Entity\Db\Query\QueryBase;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Entity\Helpers\TableHelper;

/**
 * Класс Ms\Core\Tables\TreeTable
 * ORM таблицы "Дерево" (ms_core_tree)
 */
abstract class TreeTableAbstract extends TableAbstract
{
    /**
     * @return string
     * @unittest
     */
    public function getTableTitle (): string
    {
        return 'Дерево';
    }

    /**
     * <Описание>
     *
     * @return FieldsCollection
     * @unittest
     */
    public function getMap (): FieldsCollection
    {
        $helper = TableHelper::getInstance();
        $class = $this->getClassName();

        return (new FieldsCollection())
            ->addField(
                (new Fields\IntegerField('ID'))
                    ->setPrimary()
                    ->setAutocomplete()
                    ->setTitle('ID ветки')
            )
            ->addField(
                $helper->activeField()
            )
            ->addField(
                (new Fields\IntegerField('LEFT_MARGIN'))
                    ->setRequired()
                    ->setDefaultCreate(1)
                    ->setTitle('Левая граница')
            )
            ->addField(
                (new Fields\IntegerField('RIGHT_MARGIN'))
                    ->setRequired()
                    ->setDefaultCreate(2)
                    ->setTitle('Правая граница')
            )
            ->addField(
                (new Fields\IntegerField('DEPTH_LEVEL'))
                    ->setRequired()
                    ->setDefaultCreate(1)
                    ->setDefaultInsert(1)
                    ->setTitle('Уровень вложенности')
            )
            ->addField(
                (new Fields\IntegerField('PARENT_ID'))
                    ->setTitle('Родительский узел')
                    ->setLink(
                        (new LinkedField(
                            new $class(),
                            'ID',
                            (new ForeignKey())
                                ->setOnUpdateCascade()
                                ->setOnDeleteCascade()
                        ))
                    )
            )
        ;
    }

    /**
     * Добавляет индекс в таблицу
     * Функция запускается автоматически после создания таблицы.
     *
     * @return bool
     * @unittest
     */
    public function onAfterCreateTable()
    {
        $sqlHelper = new SqlHelper($this->getTableName());
        $sql = "CREATE INDEX "
               . $sqlHelper->wrapQuotes('LEFT_MARGIN') . " ON "
               . $sqlHelper->wrapTableQuotes() . " ("
               . $sqlHelper->wrapQuotes('LEFT_MARGIN') . ", "
               . $sqlHelper->wrapQuotes('RIGHT_MARGIN') . ", "
               . $sqlHelper->wrapQuotes('DEPTH_LEVEL') . ")";
        $query = new QueryBase($sql);
        try
        {
            $res = $query->exec();
            if ($res->getResult())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        catch (SqlQueryException $e)
        {
            return false;
        }
    }

    /**
     * Обработчик события таблицы, перед обновлением данных.
     * Исключает неизменяемые поля
     *
     * @param mixed       $primary    Ключ узла
     * @param array       &$arUpdate  Массив обновляемых полей
     * @param null|string &$sSqlWhere SQL запрос WHERE
     *
     * @return bool
     * @unittest
     */
    public function onBeforeUpdate($primary, &$arUpdate, &$sSqlWhere = null): bool
    {
        $orm = TreeORMController::getInstance($this);
        $orm->checkUpdateFields($arUpdate);

        return (!empty($arUpdate));
    }



    /**
     * Возвращает имя поля, в котором хранится указатель на родительский узел
     *
     * @return string
     * @unittest
     */
    public function getParentFieldName()
    {
        return 'PARENT_ID';
    }
}
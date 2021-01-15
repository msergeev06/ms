<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\Links\ForeignKey;
use Ms\Core\Entity\Db\Links\LinkedField;
use Ms\Core\Entity\Db\Query\QueryBase;
use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Tables\FieldsCollection;
use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Errors\ErrorCollection;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Lib\TableHelper;

/**
 * Класс Ms\Core\Tables\TreeTable
 * ORM таблицы "Дерево" (ms_core_tree)
 */
class TreeTable extends TableAbstract
{
    public function getTableTitle (): string
    {
        return 'Дерево';
    }

    public function getMap (): FieldsCollection
    {
        return (new FieldsCollection())
            ->addField(
                (new Fields\IntegerField('ID'))
                    ->setPrimary()
                    ->setAutocomplete()
                    ->setTitle('ID ветки')
            )
            ->addField(
                TableHelper::activeField()
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
                            (new $this()),
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
     */
    public function OnAfterCreateTable()
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
     * Возвращает имя поля, в котором хранится указатель на родительский узел
     *
     * @return string
     */
    public function getParentFieldName()
    {
        return 'PARENT_ID';
    }
}
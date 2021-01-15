<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Tables;

use Ms\Core\Api\ApiAdapter;
use Ms\Core\Entity\Db\Fields\BooleanField;
use Ms\Core\Entity\Db\Fields\ScalarFieldAbstract;
use Ms\Core\Entity\Db\Params\GetListParams;
use Ms\Core\Entity\Db\Query\QueryBase;
use Ms\Core\Entity\Db\Query\QueryCreate;
use Ms\Core\Entity\Db\Query\QueryDelete;
use Ms\Core\Entity\Db\Query\QueryDrop;
use Ms\Core\Entity\Db\Query\QueryInsert;
use Ms\Core\Entity\Db\Query\QuerySelect;
use Ms\Core\Entity\Db\Query\QueryUpdate;
use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\System\Application;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Exceptions\Db\ValidateException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Interfaces\Db\IField;
use Ms\Core\Lib\Tools;

/**
 * Класс Ms\Core\Entity\Db\Tables\ORMController
 * Контроллер управляет таблицами БД
 */
class ORMController
{
    const TRIGGER_TIME_BEFORE  = 'before';

    const TRIGGER_TIME_AFTER   = 'after';

    const TRIGGER_EVENT_UPDATE = 'update';

    const TRIGGER_EVENT_INSERT = 'insert';

    const TRIGGER_EVENT_DELETE = 'delete';

    protected static $instances = [];
    protected        $showSql   = false;
    /** @var TableAbstract */
    protected $table = null;

    /**
     * Получение объекта обработчика таблицы
     *
     * @param TableAbstract $tableAbstract Таблица
     *
     * @return $this
     * @unittest
     */
    final public static function getInstance (TableAbstract $tableAbstract)
    {
        $tableName = $tableAbstract->getTableName();

        if (!isset(static::$instances[$tableName]))
        {
            static::$instances[$tableName] = new static($tableAbstract);
        }

        return static::$instances[$tableName];
    }

    /**
     * Защищенный конструктор класса ORMController
     *
     * @param TableAbstract $tableAbstract
     */
    protected function __construct (TableAbstract $tableAbstract)
    {
        $this->table = $tableAbstract;
    }

    /**
     * Возвращает коллекцию сущностей полей таблицы базы данных. Является оберткой метода связанной таблицы
     *
     * @return FieldsCollection Коллекция сущностей полей таблицы базы данных
     * @unittest
     */
    public function getMap (): FieldsCollection
    {
        return $this->table->getMap();
    }

    /**
     * Возвращает объект таблицы
     *
     * @return TableAbstract
     * @unittest
     */
    public function getTable ()
    {
        return $this->table;
    }

    /**
     * Нужно ли показывать SQL запрос вместо его выполнения
     *
     * @return bool
     * @unittest
     */
    public function isShowSql (): bool
    {
        return $this->showSql;
    }

    /**
     * Устанавливает флаг необходимости показать SQL запрос вместо его выполнения
     *
     * @param bool $showSql
     *
     * @return $this
     * @unittest
     */
    public function setShowSql (bool $showSql = true)
    {
        $this->showSql = $showSql;

        return $this;
    }

    /**
     * Валидирует значения полей таблицы
     *
     * @param string $sFieldName Имя поля
     * @param mixed  $mValue     Значение поля
     * @param string $actionType Тип действия
     *
     * @return bool
     * @throws ValidateException
     * @unittest
     */
    public function validateFields (
        string $sFieldName, &$mValue, string $actionType = ScalarFieldAbstract::DEFAULT_VALUE_TYPE_INSERT
    ) {
        /** @var ScalarFieldAbstract $obField */
        $obField = $this->table->getMap()->getField($sFieldName);
        if (!is_null($obField))
        {
            if ($obField->isRequired())
            {
                $this->validateRequiredFieldAllowedValues($obField, $mValue);
            }

            $this->validateFieldType($obField, $mValue, $actionType);

            if ($obField->isRequired())
            {
                $this->validateFieldRequiredValues($obField, $mValue, $actionType);
            }

            $obField->runValidator($mValue, $actionType);
        }

        return true;
    }

    /**
     * Добавляет один или несколько одиночных индексов в таблицу
     *
     * @param string|array $mFields Один или несколько полей для каждого из которых создаются индексы
     *
     * @return DBResult
     * @unittest
     */
    final public function addIndexes ($mFields)
    {
        $sql = $this->getSqlAddIndexes($mFields);
        if (empty($sql))
        {
            return new DBResult();
        }

        $conn = Application::getInstance()->getConnection();
        try
        {
            $res = $conn->querySQL($sql);

            return $res;
        }
        catch (SqlQueryException $e)
        {
            return new DBResult();
        }
    }

    /**
     * Добавляет в таблицу триггер
     *
     * @param string $sAction SQL код действий триггера
     * @param string $sTime   Время срабатывания триггера ('BEFORE' или 'AFTER')
     * @param string $sEvent  Событие срабатывания триггера ('INSERT', 'UPDATE' или 'DELETE')
     *
     * @return DBResult
     * @unittest
     */
    final public function addTriggerSql (
        string $sAction, string $sTime = self::TRIGGER_TIME_BEFORE, $sEvent = self::TRIGGER_EVENT_UPDATE
    ) {
        $sql = $this->getSqlAddTrigger($sAction, $sTime, $sEvent);
        if (empty($sql))
        {
            return new DBResult();
        }
        $conn = Application::getInstance()->getConnection();
        try
        {
            $res = $conn->querySQL($sql);

            return $res;
        }
        catch (SqlQueryException $e)
        {
            return new DBResult();
        }
    }

    /**
     * Добавляет индекс уникальных полей
     *
     * @param string|array $mFields Поле/поля уникальные для таблицы
     * @param bool         $bIndex  Флаг, создавать индекс
     * @param null|string  $sName   Имя унимального (индекса), если не задан, создасться автоматически
     *
     * @return DBResult
     * @unittest
     */
    final public function addUnique ($mFields, $bIndex = false, $sName = null)
    {
        $sql = $this->getSqlAddUnique($mFields, $bIndex, $sName);
        if (empty($sql))
        {
            return new DBResult();
        }
        $conn = Application::getInstance()->getConnection();

        try
        {
            $res = $conn->querySQL($sql);

            return $res;
        }
        catch (SqlQueryException $e)
        {
            return new DBResult();
        }
    }

    /**
     * Очищает таблицу
     *
     * @return bool
     * @unittest
     */
    final public function clearTable ()
    {
        $helper = new SqlHelper();
        $sql = 'TRUNCATE TABLE ' . $helper->wrapQuotes($this->getTableName()) . ';';
        $query = new QueryBase($sql);
        try
        {
            $res = $query->exec();
        }
        catch (SqlQueryException $e)
        {
            return false;
        }

        return $res->isSuccess();
    }

    /**
     * Возвращает количество записей в таблице
     *
     * @return int
     * @unittest
     */
    final public function count ()
    {
        $helper = new SqlHelper($this->getTableName());

        $sql = 'SELECT COUNT(*) as ' . $helper->wrapFieldQuotes('COUNT') . ' FROM ' . $helper->wrapTableQuotes();
        echo $sql;
        $query = new QueryBase($sql);

        try
        {
            $res = $query->exec();
        }
        catch (SqlQueryException $e)
        {
            return 0;
        }
        if ($ar_res = $res->fetch())
        {
            if (isset($ar_res['COUNT']) && (int)$ar_res['COUNT'] > 0)
            {
                return $ar_res['COUNT'];
            }
        }

        return 0;
    }

    /**
     * Создает таблицу
     *
     * @param bool $bShowSql Флаг, показывать SQL вместо выполнения
     *
     * @return DBResult|string Результат mysql запроса
     * @throws SqlQueryException
     * @unittest
     */
    final public function createTable ($bShowSql = false)
    {
        $table = $this->getTable();

        $query = new QueryCreate($table);
        if ($bShowSql)
        {
            return $query->getSql() . "\n" . (string)$table->getAdditionalCreateSql();
        }

        $res = $query->exec();

        if ($res->isSuccess())
        {
            $additionalSql = $table->getAdditionalCreateSql();
            if (!empty($additionalSql))
            {
                $query = new QueryBase($additionalSql);
                $query->exec();
            }

            $table->onAfterCreateTable();
        }

        return $res;
    }

    /**
     * Удаляет запись из таблицы
     *
     * @param mixed $primary Поле PRIMARY таблицы
     *
     * @return DBResult Результат mysql запроса
     * @throws SqlQueryException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @unittest
     */
    final public function delete ($primary)
    {
        //Обрабатываем системное событие перед удалением записи из таблицы
        $bDelete = ApiAdapter::getInstance()->getEventsApi()->runEvents(
            'core',
            'OnBeforeDelete',
            [$this->getTable(), $primary, null]
        )
        ;
        if ($bDelete === false)
        {
            return new DBResult();
        }

        //Обрабатываем событие таблицы перед удалением записи из таблицы
        $bDelete = $this->getTable()->onBeforeDelete($primary, null);
        if ($bDelete === false)
        {
            return new DBResult();
        }

        $query = new QueryDelete($primary, $this->getTable());

        $res = $query->exec();

        //Обрабатываем событие таблицы после попытки удаления записи из таблицы
        $this->getTable()->onAfterDelete($primary, $res, null);

        //Обрабатываем системное событие после попытки удаления записи из таблицы
        ApiAdapter::getInstance()->getEventsApi()->runEvents(
            'core',
            'OnAfterDelete',
            [$this->getTable(), $primary, null, $res]
        )
        ;

        return $res;
    }

    /**
     * Удаляет запись из таблицы по условию WHERE
     *
     * @param string $strSqlWhere SQL код условия удаления записи WHERE
     *
     * @return DBResult|string
     * @throws SqlQueryException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @unittest
     */
    final public function deleteWhere (string $strSqlWhere)
    {
        //Обрабатываем системное событие перед удалением записи из таблицы
        $bDelete = ApiAdapter::getInstance()->getEventsApi()->runEvents(
            'core',
            'OnBeforeDelete',
            [$this->getTable(), null, $strSqlWhere]
        )
        ;
        if ($bDelete === false)
        {
            return new DBResult();
        }

        //Обрабатываем событие таблицы перед удалением записи из таблицы
        $bDelete = $this->getTable()->onBeforeDelete(null, $strSqlWhere);
        if ($bDelete === false)
        {
            return new DBResult();
        }

        $query = new QueryDelete(null, $this->getTable(), $strSqlWhere);

        $res = $query->exec();

        //Обрабатываем событие таблицы после попытки удаления записи из таблицы
        $this->getTable()->onAfterDelete(null, $res, $strSqlWhere);

        //Обрабатываем системное событие после попытки удаления записи из таблицы
        ApiAdapter::getInstance()->getEventsApi()->runEvents(
            'core',
            'OnAfterDelete',
            [$this->getTable(), null, $strSqlWhere, $res]
        )
        ;

        return $res;
    }

    /**
     * Удаляет таблицу из БД. Возвращает TRUE, если табилца успешно удалена, иначе FALSE
     *
     * @param bool $bIgnoreForeignKeys Флаг, означающий необходимо игнорировать ограничения внешних ключей
     *
     * @return DBResult
     * @throws SqlQueryException
     * @unittest
     */
    final public function dropTable (bool $bIgnoreForeignKeys = false)
    {
        $query = new QueryDrop($this->getTable(), $bIgnoreForeignKeys);

        $res = ApiAdapter::getInstance()->getEventsApi()->runEvents(
            'core',
            'OnBeforeDropTable',
            [$this->getTable(), $bIgnoreForeignKeys]
        )
        ;
        if ($res === false)
        {
            return new DBResult();
        }

        if (!$this->getTable()->onBeforeDropTable())
        {
            return new DBResult();
        }

        $res = $query->exec();

        $this->getTable()->onAfterDropTable();

        ApiAdapter::getInstance()->getEventsApi()->runEvents(
            'core',
            'OnAfterDropTable',
            [$this->getTable(), $bIgnoreForeignKeys, $res]
        )
        ;

        return $res;
    }

    /**
     * Алиас метода getByPrimary
     *
     * @param int   $ID       ID записи
     * @param array $arSelect Массив возвращаемых полей
     *
     * @return array|bool|mixed|string
     * @throws ArgumentTypeException
     * @throws SqlQueryException
     * @unittest
     */
    final public function getByID ($ID, array $arSelect = [])
    {
        return $this->getByPrimary($ID, $arSelect);
    }

    /**
     * Возвращает строку из БД по значению PRIMARY поля
     *
     * @param       $primaryValue
     * @param array $arSelect
     *
     * @return array|bool|mixed|string
     * @throws ArgumentTypeException
     * @throws SqlQueryException
     * @unittest
     */
    final public function getByPrimary ($primaryValue, array $arSelect = [])
    {
        $arResult = $this->getList(
            [
                'select' => $arSelect,
                'filter' => [
                    $this->getPrimaryFieldName() => $primaryValue
                ],
                'limit'  => 1
            ]
        );

        if (empty($arResult))
        {
            return false;
        }
        if (isset($arResult[0]))
        {
            return $arResult[0];
        }

        return $arResult;
    }

    /**
     * Осуществляет выборку из таблицы значений по указанным параметрам
     *
     * @param array $getListParams Массив параметров запроса
     * @param bool  $bShowSql      Флаг, возвращать SQL код вместо результатов запроса (для отладки)
     *
     * @return array|bool|string
     * @throws ArgumentTypeException
     * @throws SqlQueryException
     * @unittest
     */
    final public function getList (array $getListParams, bool $bShowSql = false)
    {
        $getListParams = $this->checkGetListParams($getListParams);
        $query = new QuerySelect($this->getTable(), $getListParams);
        if ($bShowSql)
        {
            return $query->getSql();
        }

        $res = $query->exec();
        $arResult = [];
        while ($ar_res = $res->fetch())
        {
            $arResult[] = $ar_res;
        }

        if (!empty($arResult))
        {
            return $arResult;
        }
        else
        {
            return false;
        }
    }

    /**
     * Возвращает обработанный массив сущностей полей таблицы базы данных.
     * Обрабатывает коллекцию, полученную методом getMap
     *
     * @return IField[] Обработанный массив сущностей полей таблицы базы данных
     * @unittest
     */
    final public function getMapArray ()
    {
        $collection = $this->getMap();

        return $collection->toArray();
    }

    /**
     * Возвращает одну запись из БД
     *
     * @param array $getListParams
     * @param bool  $bShowSql
     *
     * @return array|bool|string
     * @throws ArgumentTypeException
     * @throws SqlQueryException
     * @unittest
     */
    final public function getOne (array $getListParams, bool $bShowSql = false)
    {
        $getListParams['limit'] = 1;

        $result = $this->getList($getListParams, $bShowSql);
        if ($result && array_key_exists(0, $result))
        {
            $result = $result[0];
        }

        return $result;
    }

    /**
     * Возвращает объект primary поля
     *
     * @return null|IField
     * @unittest
     */
    final public function getPrimaryField ()
    {
        return $this->getTable()->getMap()->getPrimaryField();
    }

    /**
     * Возвращает имя поля таблицы, обозначенного как PRIMARY KEY
     *
     * @return string
     * @unittest
     */
    final public function getPrimaryFieldName ()
    {
        return $this->getTable()->getMap()->getPrimaryField()->getName();
    }

    /**
     * Возвращает текст SQL запроса добавления индексов в таблицу
     *
     * @param string|array $mFields Один или несколько полей для каждого из которых создаются индексы
     *
     * @return null|string
     * @unittest
     */
    final public function getSqlAddIndexes ($mFields)
    {
        return $this->table->getSqlAddIndexes($mFields);
    }

    /**
     * Возвращает SQL запрос создания триггера для таблицы
     *
     * @param string $sAction SQL код действий триггера
     * @param string $sTime   Время срабатывания триггера ('BEFORE' или 'AFTER')
     * @param string $sEvent  Событие срабатывания триггера ('INSERT', 'UPDATE' или 'DELETE')
     *
     * @return string SQL код создания триггера или пустая строка в случае ошибки
     * @unittest
     */
    final public function getSqlAddTrigger (
        string $sAction, string $sTime = self::TRIGGER_TIME_BEFORE, $sEvent = self::TRIGGER_EVENT_UPDATE
    ) {
        $sTime = strtoupper($sTime);
        $sEvent = strtoupper($sEvent);
        if (
            !in_array($sTime, ['BEFORE', 'AFTER'])
            || !in_array($sEvent, ['INSERT', 'UPDATE', 'DELETE'])
        )
        {
            return null;
        }
        $helper = new SqlHelper($this->getTableName());
        $sql = <<<EOL
DROP TRIGGER IF EXISTS #TRIGGER_NAME#;
DELIMITER |
CREATE TRIGGER #TRIGGER_NAME#
#TRIGGER_TIME# #TRIGGER_EVENT# ON #TABLE_NAME# FOR EACH ROW
BEGIN
    #TRIGGER_ACTION#
END;
DELIMITER ;
EOL;
        $sql = '' . Tools::strReplace(
                [
                    'TRIGGER_NAME'   => $helper->wrapQuotes(
                        $this->getTableName() . '_' . strtolower($sTime) . '_' . strtolower($sEvent)
                    ),
                    'TRIGGER_TIME'   => $sTime,
                    'TRIGGER_EVENT'  => $sEvent,
                    'TRIGGER_ACTION' => $sAction,
                    'TABLE_NAME'     => $this->getTableName()
                ],
                $sql
            );

        return $sql;
    }

    /**
     * Возвращает SQL код уникального поля/полей
     *
     * @param string|array $mFields Поле/поля уникальные для таблицы
     * @param bool         $bIndex  Флаг, создавать индекс
     * @param null|string  $sName   Имя унимального (индекса), если не задан, создасться автоматически
     *
     * @return null|string
     * @unittest
     */
    final public function getSqlAddUnique ($mFields, $bIndex = false, $sName = null)
    {
        return $this->getTable()->getSqlAddUnique($mFields, $bIndex, $sName);
    }

    /**
     * Возвращает SQL запрос удаления триггера для таблицы
     *
     * @param string $sTime  Время срабатывания триггера ('BEFORE' или 'AFTER')
     * @param string $sEvent Событие срабатывания триггера ('INSERT', 'UPDATE' или 'DELETE')
     *
     * @return string SQL код удаления триггера или пустая строка в случае ошибки
     * @unittest
     */
    final public function getSqlDeleteTrigger ($sTime = self::TRIGGER_TIME_BEFORE, $sEvent = self::TRIGGER_EVENT_UPDATE)
    {
        $sTime = strtolower($sTime);
        $sEvent = strtolower($sEvent);
        if (
            !in_array($sTime, [self::TRIGGER_TIME_BEFORE, self::TRIGGER_TIME_AFTER])
            || !in_array($sEvent, [self::TRIGGER_EVENT_INSERT, self::TRIGGER_EVENT_UPDATE, self::TRIGGER_EVENT_DELETE])
        )
        {
            return '';
        }
        $helper = new SqlHelper();

        return 'DROP TRIGGER IF EXISTS '
               . $helper->wrapQuotes($this->getTableName() . '_' . $sTime . '_' . $sEvent)
               . ';';
    }

    /**
     * Возвращает название таблицы в базе. Является оберткой метода связанной таблицы
     *
     * @return string название таблицы в базе
     * @example 'ms_core_options'
     * @unittest
     */
    final public function getTableName ()
    {
        return $this->getTable()->getTableName();
    }

    /**
     * Добавляет значения в таблицу
     *
     * OnBeforeInsert - перед добавлением записи в таблицу
     * OnAfterInsert - после успешного добавления записи в таблицу
     *
     * @param array $arInsert Массив содержащий значения таблицы
     *
     * @return DBResult|string
     * @throws SqlQueryException
     * @throws ValidateException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @unittest
     */
    final public function insert (array $arInsert)
    {
        if (empty($arInsert))
        {
            return new DBResult();
        }

        $this->validateFieldsInsert($arInsert);

        if (!$this->runEventsOnBeforeInsert($arInsert))
        {
            return new DBResult();
        }

        $query = new QueryInsert($arInsert, $this->getTable());

        $res = $query->exec($this->isShowSql());

        if ($this->isShowSql())
        {
            return $res;
        }

        if ($res->isSuccess())
        {
            $res->setInsertID($res->getInsertId());
        }

        $this->runEventsOnAfterInsert($arInsert, $res);

        return $res;
    }

    /**
     * Заполняет таблицу значениями по умолчанию, из объекта описания таблицы
     *
     * @param bool $bShowSql Флаг - выводить SQL код, вместо его выполнения
     *
     * @return bool|DBResult|string
     * @throws SqlQueryException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @unittest
     */
    final public function insertDefaultRows (bool $bShowSql = false)
    {
        $arDefaultValues = $this->getTable()->getDefaultRowsArray();
        if (count($arDefaultValues) > 0)
        {
            $query = new QueryInsert($arDefaultValues, $this->getTable());
            if ($bShowSql)
            {
                return $query->getSql();
            }
            $res = $query->exec();

            return $res;
        }
        else
        {
            return false;
        }
    }

    /**
     * Возвращает TRUE, если таблица с указанным именем существует в БД
     *
     * @param string $tableName
     *
     * @return bool
     * @throws SqlQueryException
     * @unittest
     */
    final public function issetTable (string $tableName = null)
    {
        if (is_null($tableName))
        {
            $tableName = $this->getTableName();
        }
        $query = new QueryBase("SHOW TABLES LIKE '" . $tableName . "'");
        $res = $query->exec();

        return ($res->isSuccess() && $res->getAffectedRows() > 0);
    }

    /**
     * Возвращает список существующих таблиц, производя сравнение по части имени
     *
     * @param string|null $tableName Часть имени таблицы
     *
     * @return array
     */
    final public function getListTables (string $tableName = null)
    {
        if (is_null($tableName))
        {
            $tableName = $this->getTableName();
        }
        $arTables = [];
        $query = new QueryBase("SHOW TABLES LIKE '%" . $tableName . "%'");
        try
        {
            $res = $query->exec();
        }
        catch (SqlQueryException $e)
        {
            return $arTables;
        }

        if ($res->isSuccess())
        {
            while ($arRes = $res->fetch())
            {
                $arValues = array_unique(array_values($arRes));
                $arTables = array_unique(array_merge($arTables, $arValues));
            }
        }

        return $arTables;
    }

    /**
     * Пытается переименовать таблицу. Если таблица с новым именем существует, и нельзя использовать индекс, возвращает
     * false. Иначе подбирает индекс таким образом, чтобы таблицы с индексом не существовало и тогда создает ее
     *
     * @param string &$newTableName Имя таблицы
     * @param bool    $bUseIndex    Использовать ли индекс
     *
     * @return bool
     * @throws SqlQueryException
     * @unittest
     */
    final public function rename (string &$newTableName, bool $bUseIndex = false)
    {
        $newTableName = strtolower($newTableName);
        $helper = new SqlHelper();
        if (!$this->issetTable($newTableName))
        {
            $sql = 'RENAME TABLE ' . $helper->wrapQuotes($this->getTableName()) . ' TO '
                   . $helper->wrapQuotes($newTableName);
            $query = new QueryBase($sql);
            $query->exec();

            return ($this->issetTable($newTableName));
        }
        elseif ($bUseIndex)
        {
            $i = 0;
            do
            {
                $i++;
                if ($i > 0 && $i < 10)
                {
                    $expName = $newTableName . '0' . $i;
                }
                else
                {
                    $expName = $newTableName . $i;
                }
                $issetTable = $this->issetTable($expName);
            }
            while ($issetTable);

            $sql = 'RENAME TABLE ' . $helper->wrapQuotes($this->getTableName()) . ' TO '
                   . $helper->wrapQuotes($expName);
            $query = new QueryBase($sql);
            $query->exec();

            if ($this->issetTable($expName))
            {
                $newTableName = $expName;

                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Обновляет значения в таблице
     *
     * @param mixed       $primary   Поле PRIMARY таблицы
     * @param array       $arUpdate  Массив значений таблицы в поле 'VALUES'
     * @param string|null $sSqlWhere SQL условие WHERE. Если заполнено, параметр primary игнорируется
     *
     * @return DBResult|string
     * @throws SqlQueryException
     * @throws ValidateException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException
     * @unittest
     */
    final public function update ($primary, array $arUpdate, string $sSqlWhere = null)
    {
        if (!$this->updateValidationFields($arUpdate))
        {
            return new DBResult();
        }

        if (!$this->runEventsOnBeforeUpdate($primary, $arUpdate, $sSqlWhere))
        {
            return new DBResult();
        }

        $query = new QueryUpdate($primary, $arUpdate, $this->table, $sSqlWhere);

        if ($this->isShowSql())
        {
            return $query->getSql();
        }

        $res = $query->exec();

        $this->runEventsOnAfterUpdate($primary, $arUpdate, $sSqlWhere, $res);

        return $res;
    }

    /**
     * Обновляет запись в таблице по PRIMARY ключу
     *
     * @param mixed $primary  Значение PRIMARY строки
     * @param array $arUpdate Массив изменяемых полей и их значений
     *
     * @return DBResult|string
     * @throws SqlQueryException
     * @throws ValidateException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException
     * @unittest
     */
    final public function updateByPrimary ($primary, array $arUpdate)
    {
        return $this->update($primary, $arUpdate);
    }

    /**
     * Обновляет запись в таблице по SQL условию WHERE
     *
     * @param string $strSqlWhere SQL условие WHERE
     * @param array  $arUpdate    Массив изменяемых полей и их значений
     *
     * @return DBResult|string
     * @throws SqlQueryException
     * @throws ValidateException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException
     * @unittest
     */
    final public function updateByWhere ($strSqlWhere, array $arUpdate)
    {
        return $this->update(0, $arUpdate, $strSqlWhere);
    }

    /**
     * Если параметры переданы массивом, делает из них объект GetListParams, иначе выбрасывает исключение
     *
     * @param array|GetListParams $getListParams Параметры getList
     *
     * @return GetListParams
     * @throws ArgumentTypeException
     * @throws SystemException
     */
    protected function checkGetListParams ($getListParams)
    {
        if (is_array($getListParams))
        {
            $getListParams = $this->prepareGetListParams($getListParams);
        }
        else //if (!($getListParams instanceof GetListParams))
        {
            throw new ArgumentTypeException(
                'getListParams',
                'array'
            );
        }

        return $getListParams;
    }

    /**
     * Преобразует параметры для getList из массива в объект класса GetListParams
     *
     * @param array $getListParams Массив параметров getList
     *
     * @return GetListParams
     * @throws \Ms\Core\Exceptions\SystemException
     */
    final protected function prepareGetListParams (array $getListParams)
    {
        $obj = new GetListParams($this);
        if (!empty($getListParams))
        {
            foreach ($getListParams as $type => $data)
            {
                switch (strtolower($type))
                {
                    case 'select':
                        if (is_string($data))
                        {
                            $data = [$data];
                        }
                        $obj->parseGetListSelect($data);
                        break;
                    case 'filter':
                        $obj->setFilterFromArray($data);
                        break;
                    case 'group':
                        // $obj->setGroupFromArray($data);
                        break;
                    case 'order':
                        $obj->setOrderFromArray($data);
                        break;
                    case 'limit':
                        $obj->setLimit((int)$data);
                        break;
                    case 'offset':
                        $obj->setOffset((int)$data);
                        break;
                }
            }
        }

        return $obj;
    }

    /**
     * Устанавливает объект таблицы
     *
     * @param TableAbstract $table Объект, описывающий таблицу
     *
     * @return $this
     */
    protected function setTable (TableAbstract $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param array $arInsert
     * @param       $res
     */
    private function runEventsOnAfterInsert (array $arInsert, $res)
    {
        //Обрабатываем событие таблицы после попытки добавления записи
        $this->table->onAfterInsert($arInsert, $res);

        //Обрабатываем системное событие после попытки добавления записи
        ApiAdapter::getInstance()->getEventsApi()->runEvents(
            'core',
            'OnAfterInsert',
            [$this->table, $arInsert, $res]
        )
        ;
    }

    /**
     * @param $primary
     * @param $arUpdate
     * @param $sSqlWhere
     * @param $res
     */
    private function runEventsOnAfterUpdate ($primary, $arUpdate, $sSqlWhere, $res)
    {
        //Обрабатываем событие таблицы после попытки обновления записи
        $this->table->onAfterUpdate($primary, $arUpdate, $sSqlWhere, $res);

        //Обрабатываем системное событие таблицы после попытки обновления записи
        ApiAdapter::getInstance()->getEventsApi()->runEvents(
            'core',
            'OnAfterUpdate',
            [$this->table, $primary, $arUpdate, $sSqlWhere, $res]
        )
        ;
    }

    /**
     * @param array $arInsert
     *
     * @return bool
     */
    private function runEventsOnBeforeInsert (array &$arInsert)
    {
        //Обрабатываем системное событие перед добавлением записи
        $bAdd = ApiAdapter::getInstance()->getEventsApi()->runEvents(
            'core',
            'OnBeforeInsert',
            [$this->table, &$arInsert]
        )
        ;

        if ($bAdd === false)
        {
            return false;
        }

        //Обрабатываем событие таблицы перед добавлением записи
        $bAdd = $this->table->onBeforeInsert($arInsert);

        if ($bAdd === false)
        {
            return false;
        }

        return true;
    }

    /**
     * @param $primary
     * @param $arUpdate
     * @param $sSqlWhere
     *
     * @return bool
     */
    private function runEventsOnBeforeUpdate ($primary, &$arUpdate, &$sSqlWhere)
    {
        //Обрабатываем событие таблицы перед обновлением записи
        $bNext = ApiAdapter::getInstance()->getEventsApi()->runEvents(
            'core',
            'OnBeforeUpdate',
            [$this->table, $primary, &$arUpdate, &$sSqlWhere]
        )
        ;
        if ($bNext === false)
        {
            return false;
        }

        //Обрабатываем событие таблицы перед обновлением записи
        $bNext = $this->table->onBeforeUpdate($primary, $arUpdate, $sSqlWhere);
        if ($bNext === false)
        {
            return false;
        }

        return true;
    }

    /**
     * @param array $arUpdate
     *
     * @return bool
     * @throws ValidateException
     */
    private function updateValidationFields (array &$arUpdate)
    {
        $bValidate = true;
        if (!empty($arUpdate))
        {
            foreach ($arUpdate as $fieldName => &$value)
            {
                $bValidate = $this->validateFields(
                    $fieldName,
                    $value,
                    'update'
                );
            }
            unset($value);
        }

        return $bValidate;
    }

    /**
     * @param IField              $obField
     * @param                     &$mValue
     * @param                     $actionType
     *
     * @return bool
     * @throws ValidateException
     */
    private function validateFieldRequiredValues (IField $obField, &$mValue, $actionType)
    {
        if (is_null($mValue))
        {
            if ($obField->isRequiredNull())
            {
                return true;
            }
            elseif ($obField->isAutocomplete())
            {
                return true;
            }
            elseif (!is_null($obField->getDefaultValue($actionType)))
            {
                return true;
            }
            else
            {
                throw new ValidateException(
                    $obField->getName(),
                    $mValue,
                    null,
                    'Передано значение null, однако поле не может быть null, не является автозаполняемым и не имеет значения по-умолчанию'
                );
            }
        }

        return true;
    }

    /**
     * @param IField              $obField
     * @param                     &$mValue
     * @param                     $actionType
     *
     * @return bool
     * @throws ValidateException
     */
    private function validateFieldType (IField $obField, &$mValue, $actionType)
    {
        if ($obField instanceof BooleanField)
        {
            if (is_bool($mValue))
            {
                return true;
            }
            elseif (is_bool($obField->normalizeValue($mValue)))
            {
                return true;
            }
            elseif (!is_null($obField->getDefaultValue($actionType)))
            {
                return true;
            }
            else
            {
                throw new ValidateException(
                    $obField->getName(),
                    $mValue,
                    null,
                    'Тип поля boolean, значение другого типа'
                );
            }
        }

        return false;
    }

    /**
     * @param array $arInsert
     *
     * @throws ValidateException
     */
    private function validateFieldsInsert (array &$arInsert)
    {
        $fieldsCollection = $this->getMap();
        if (!$fieldsCollection->isEmpty())
        {
            if (isset($arInsert[0]) && is_array($arInsert[0]))
            {
                //обрабатываем массив массивов
                for ($i = 0; $i < count($arInsert); $i++)
                {
                    /** @var ScalarFieldAbstract $obField */
                    foreach ($fieldsCollection as $obField)
                    {
                        $this->validateFields(
                            $obField->getName(),
                            $arInsert[$i][$obField->getName()],
                            self::TRIGGER_EVENT_INSERT
                        );
                    }
                }
            }
            else
            {
                foreach ($fieldsCollection as $obField)
                {
                    $this->validateFields(
                        $obField->getName(),
                        $arInsert[$obField->getName()],
                        self::TRIGGER_EVENT_INSERT
                    );
                }
            }
        }
        $oPrimaryField = $this->getMap()->getPrimaryField();
        $sPrimaryFieldName = $oPrimaryField->getColumnName();
        if (array_key_exists($sPrimaryFieldName,$arInsert) && is_null($arInsert[$sPrimaryFieldName]))
        {
            unset($arInsert[$sPrimaryFieldName]);
        }
    }

    /**
     * @param IField $obField
     * @param        &$mValue
     *
     * @return bool
     * @throws ValidateException
     */
    private function validateRequiredFieldAllowedValues (IField $obField, &$mValue)
    {
        //Если поле обязательно и если есть список допустимых значений, проверяем их
        $arAllowedValues = $obField->getAllowedValues();
        $arAllowedValuesRange = $obField->getAllowedValuesRange();
        if (!is_null($arAllowedValues))
        {
            if (in_array($mValue, $arAllowedValues))
            {
                return true;
            }
            else
            {
                throw new ValidateException(
                    $obField->getName(),
                    $mValue,
                    $arAllowedValues
                );
            }
        }
        elseif (
            !is_null($arAllowedValuesRange)
            && array_key_exists('min', $arAllowedValuesRange)
            && array_key_exists('max', $arAllowedValuesRange)
        )
        {
            if ($mValue >= $arAllowedValuesRange['min'] && $mValue <= $arAllowedValuesRange['max'])
            {
                return true;
            }
            else
            {
                throw new ValidateException(
                    $obField->getName(),
                    $mValue,
                    $arAllowedValuesRange
                );
            }
        }

        return false;
    }
}
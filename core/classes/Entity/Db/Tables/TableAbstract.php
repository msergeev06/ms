<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Tables;

use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\System\Application;

/**
 * Класс Ms\Core\Entity\Db\Tables\Table
 * Базовый класс ORM-таблицы
 */
abstract class TableAbstract
{
    /** @var string */
    protected $additionalName = '';

    /**
     * Конструктор класса TableAbstract
     *
     * @param string $additionalName Постфикс таблицы (для создания нескольких таблиц с одинаковой структурой)
     *
     * @unittest
     */
    public function __construct (string $additionalName = '')
    {
        $this->additionalName = strtolower($additionalName);
    }

    /**
     * Возвращает дополнительный SQL запрос, используемый после создания таблицы
     *
     * @return null|string
     * @unittest
     */
    public function getAdditionalCreateSql ()
    {
        return null;
    }

    /**
     * Возвращает дополнительный SQL запрос, используемый после удаления таблицы
     *
     * @return null|string
     * @unittest
     */
    public function getAdditionalDeleteSql ()
    {
        return null;
    }

    /**
     * Возвращает массив дефолтных значений таблицы, которые добавляются в таблицу при установке ядра или модуля
     *
     * @return array Массив дефолтных значений таблицы
     * @unittest
     */
    public function getDefaultRowsArray (): array
    {
        return [];
    }

    /**
     * Возвращает SQL код, вставляемый при создании таблицы.
     * Можно использовать для добавления индексов
     *
     * @return null|string
     * @unittest
     */
    public function getInnerCreateSql ()
    {
        return null;
    }

    /**
     * Возвращает описание таблицы
     *
     * @return string Текст описания таблицы
     * @example 'Опции'
     * @unittest
     */
    public function getTableTitle (): string
    {
        return '';
    }

    /**
     * Вызывается после создания таблицы
     *
     * @return void
     * @unittest
     */
    public function onAfterCreateTable ()
    {
        return;
    }

    /**
     * Обработчик события после попытки удаления записи из таблицы
     *
     * @param mixed       $primary   Значение PRIMARY поля таблицы
     * @param DBResult    $result    Результат выполнения запроса
     * @param string|null $sSqlWhere Условие удаления записи WHERE
     *
     * @return void
     * @unittest
     */
    public function onAfterDelete ($primary, DBResult $result, string $sSqlWhere = null)
    {
        return;
    }

    /**
     * Обработчик события после удаления таблицы
     *
     * @return void
     * @unittest
     */
    public function onAfterDropTable ()
    {
        return;
    }

    /**
     * Обработчик события после добавления строки в таблицу
     *
     * @param array    $arInsert
     * @param DBResult $result
     *
     * @return void
     * @unittest
     */
    public function onAfterInsert (array $arInsert, DBResult $result)
    {
        return;
    }

    /**
     * Обработчик события после обновления строки в таблице
     *
     * @param mixed         $primary   Имя PRIMARY поля таблицы
     * @param array         $arUpdate  Массив изменяемых полей и их новых значений
     * @param string|null   $sSqlWhere Дополнительный запрос SQL WHERE
     * @param DBResult|null $result    Результат обработки SQL запроса
     *
     * @return void
     * @unittest
     */
    public function onAfterUpdate ($primary, array $arUpdate, string $sSqlWhere = null, DBResult $result = null)
    {
        return;
    }

    /**
     * Обработчик события перед созданием таблицы
     *
     * @return bool
     * @unittest
     */
    public function onBeforeCreateTable (): bool
    {
        return true;
    }

    /**
     * Обработчик события перед удалением строки
     *
     * @param mixed       $primary     Значение PRIMARY поля записи
     * @param null|string $strSqlWhere Условие удаления WHERE
     *
     * @return bool
     * @unittest
     */
    public function onBeforeDelete ($primary, $strSqlWhere): bool
    {
        return true;
    }

    /**
     * Обработчик события перед удалением таблицы
     *
     * @return bool
     * @unittest
     */
    public function onBeforeDropTable (): bool
    {
        return true;
    }

    /**
     * Обработчик события перед добавлением строки в таблицу
     *
     * @param array $arInsert
     *
     * @return bool
     * @unittest
     */
    public function onBeforeInsert (array &$arInsert): bool
    {
        return true;
    }

    /**
     * Обработчик события перед обновление записи таблицы
     *
     * @param mixed $primary   Значение PRIMARY поля изменяемой строки
     * @param array $arUpdate  Изменяемые поля и их новые значения
     * @param null  $sSqlWhere Дополнительный запрос SQL WHERE
     *
     * @return bool
     * @unittest
     */
    public function onBeforeUpdate ($primary, &$arUpdate, &$sSqlWhere = null): bool
    {
        return true;
    }

    /**
     * Очищает дополнительное имя таблицы
     *
     * @return $this
     * @unittest
     */
    final public function clearAdditionalName ()
    {
        $this->additionalName = '';

        return $this;
    }

    /**
     * Возвращает дополнительное имя таблицы
     *
     * @return string
     * @unittest
     */
    final public function getAdditionalName (): string
    {
        return $this->additionalName;
    }

    /**
     * Возвращает имя вызвавшего метод класса
     *
     * @return string
     * @unittest
     */
    final public function getClassName (): string
    {
        return get_called_class();
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
        if (!is_array($mFields))
        {
            $mFields = [$mFields];
        }
        elseif (empty($mFields))
        {
            return null;
        }
        $sql = '';

        foreach ($mFields as $sFieldName)
        {
            $helper = new SqlHelper();
            $sFieldName = strtoupper($sFieldName);
            $sql .= 'INDEX ' . $helper->wrapQuotes('INDEX_' . $sFieldName)
                    . ' (' . $helper->wrapQuotes($sFieldName) . ")\n\t";
        }

        if (strlen($sql) > 0)
        {
            return $sql;
        }

        return null;
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
        if (!is_array($mFields) && strlen($mFields) > 0)
        {
            $mFields = [$mFields];
        }
        elseif (!is_array($mFields))
        {
            return null;
        }
        $helper = new SqlHelper();
        $sql = 'UNIQUE ';
        if ($bIndex)
        {
            $sql .= 'INDEX ';
        }
        if (is_null($sName))
        {
            $sName = 'UNIQUE';
            if ($bIndex)
            {
                $sName .= '_INDEX';
            }
            foreach ($mFields as $field)
            {
                $sName .= '_' . $field;
            }
            $sName = strtolower($sName);
        }
        $sql .= $helper->wrapQuotes($sName) . ' (';
        $bFirst = true;
        foreach ($mFields as $field)
        {
            if ($bFirst)
            {
                $bFirst = false;
            }
            else
            {
                $sql .= ',';
            }
            $sql .= $helper->wrapQuotes($field);
        }
        $sql .= ')';

        return $sql;
    }

    /**
     * Генерирует и возвращает название таблицы в базе
     *
     * @return string название таблицы в базе
     * @example 'ms_core_options'
     * @unittest
     */
    final public function getTableName (): string
    {
        $app = Application::getInstance();
        //Разбираем Brand\ModuleName\Tables\NameTable
        $arClass = explode('\\', $this->getClassName());
        //Сохраняем Brand
        $name = strtolower($arClass[0]) . '_';
        //Сохраняем ModuleName
        $name .= $app->convertPascalCaseToSnakeCase($arClass[1]) . '_';
        //Сохраняет NameTable
        $table = $app->convertPascalCaseToSnakeCase($arClass[3]);
        //Удаляем _table
        $name .= str_replace('_table', '', $table);
        //Если есть дополнительное название, добавляем его
        if (!empty($this->additionalName))
        {
            $name .= '_' . $this->additionalName;
        }

        return $name;
    }

    /**
     * Устанавливает дополнительное имя таблицы
     *
     * @param string $additionalName
     *
     * @return $this
     * @unittest
     */
    final public function setAdditionalName (string $additionalName)
    {
        $this->additionalName = strtolower($additionalName);

        return $this;
    }

    /**
     * Возвращает коллекцию с описанием полей таблицы
     *
     * @return FieldsCollection
     */
    abstract public function getMap (): FieldsCollection;
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Tables\TableAbstract;
use Ms\Core\Entity\Type\Associative;
use Ms\Core\Entity\Type\AssociativeCollection;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Query\QueryInsert
 * Сущность INSERT запроса к базе данных
 */
class QueryInsert extends QueryBase
{
    /** @var AssociativeCollection */
    protected $insertDataCollection = null;
    /**
     * @var array
     */
    protected $arAdd = null;

    /**
     * Заполняет все необходимые параметры для INSERT запроса
     *
     * @param array         $insertArray Массив добавляемый полей => значений
     * @param TableAbstract $table       Объект таблицы БД
     *
     * @throws ArgumentNullException
     * @unittest
     */
    public function __construct (array $insertArray, TableAbstract $table)
    {
        if (isset($insertArray[0]))
        {
            $this->setArray($insertArray);
        }
        else
        {
            $this->setArray([$insertArray]);
        }
        $this->recreateInsertDataCollection();

        $this->setTable($table);

        $this->setSql($this->buildQuery());
    }

    /**
     * Добавляет значение в коллекцию добавляемых значений полей
     *
     * @param string $name  Имя
     * @param mixed  $value Значение
     *
     * @return $this
     */
    protected function addDataToInsertDataCollection (string $name, $value)
    {
        $this->insertDataCollection->addData(new Associative($name, $value));

        return $this;
    }

    /**
     * Возвращет объект Associative из коллеции добавляемых значений полей по его имени
     *
     * @param string $name Имя объект
     *
     * @return Associative|null
     */
    protected function getDataFromInsertDataCollection (string $name)
    {
        return $this->insertDataCollection->getData($name);
    }

    /**
     * Возвращает коллекцию добавляемых значений полей
     *
     * @return AssociativeCollection|null
     */
    protected function getInsertDataCollection ()
    {
        return $this->insertDataCollection;
    }

    /**
     * Возвращает значение объекта Associative из коллекции добавляемых значений полей по его имени
     *
     * @param string $name Имя объекта
     *
     * @return mixed|null
     */
    protected function getValueFromInsertDataCollection (string $name)
    {
        $assoc = $this->getDataFromInsertDataCollection($name);

        return (!is_null($assoc) ? $assoc->getValue() : null);
    }

    /**
     * Очищает коллекцию добавляемых значений полей
     *
     * @return $this
     */
    protected function recreateInsertDataCollection ()
    {
        $this->insertDataCollection = new AssociativeCollection();

        return $this;
    }

    /**
     * Собирает SQL запрос из параметров
     *
     * @return string
     * @throws ArgumentNullException
     */
    protected function buildQuery ()
    {
        $table = $this->getTable();
        $helper = new SqlHelper($table->getTableName());
        $sql = "";
        $bFFirst = true;

        //Бежим по добавляемым записям
        $sql .= "INSERT INTO " . $helper->wrapTableQuotes() . " ";
        foreach ($this->getArray() as $arValue)
        {
            $this->recreateInsertDataCollection();
            /**
             * @var IField $obMap
             */
            foreach ($table->getMap() as $obMap)
            {
                /** @var string $columnName Название поля в базе данных */
                $columnName = $obMap->getColumnName();
                /** @var string $fieldName Название поля в API */
                $fieldName = $obMap->getName();

                //Если среди добавляемых значений есть значение для данного поля
                if (isset($arValue[$fieldName]))
                {
                    $this->getSaveValue($arValue, $obMap, $fieldName, $columnName);
                }
                //Если значения не установлено для данного поля
                else
                {
                    $this->getEmptyValue($arValue, $obMap, $fieldName, $columnName);
                }
            }
            $sql .= $this->getInsertSql($bFFirst);
        }
        $sql .= ';';

        return $sql;
    }

    /**
     * Устанавливает массив значений полей таблицы
     *
     * @param array $array
     */
    protected function setArray (array $array)
    {
        $this->arAdd = $array;
    }

    /**
     * Возвращает массив значений полей таблицы
     *
     * @return array
     */
    protected function getArray ()
    {
        return $this->arAdd;
    }

    /**
     * Получает значения по-умолчанию
     *
     * @param IField $obMap
     * @param string $columnName
     *
     * @return QueryInsert
     */
    protected function getDefaultValue (IField &$obMap, $columnName)
    {
        $helper = new SqlHelper();
        $value = $obMap->getDefaultValue(Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_INSERT);
        if ($obMap->isDefaultSql(Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_INSERT))
        {
            $this->addDataToInsertDataCollection(
                $helper->wrapQuotes($columnName),
                $value
            );
        }
        else
        {
            /** @var IField $fieldClassName */
            $value = $obMap->saveDataModification($value);
            $this->addDataToInsertDataCollection(
                $helper->wrapQuotes($columnName),
                $obMap->getSqlValue($value)
            );
        }

        return $this;
    }

    /**
     * Получает пустые значения
     *
     * @param        $arValue
     * @param IField $obMap
     * @param        $fieldName
     * @param        $columnName
     *
     * @return QueryInsert
     * @throws ArgumentNullException
     */
    private function getEmptyValue (&$arValue, IField &$obMap, $fieldName, $columnName)
    {
        $helper = new SqlHelper();
        //Если данное поле не является обязательным или стоит флаг автокомплита
        if (!$obMap->isRequired() || $obMap->isAutocomplete())
        {
            //Устанавливаем его в значение NULL
            $this->addDataToInsertDataCollection(
                $helper->wrapQuotes($columnName),
                'NULL'
            );
        }
        else
        {
            //Если значение может быть получено из функции
/*            if (!is_null($obMap->getRun()))
            {
                $this->getRunValue($arValue, $obMap, $columnName);
            }*/
            //Если есть значение по-умолчанию
            if (!is_null($obMap->getDefaultValue(Fields\ScalarFieldAbstract::DEFAULT_VALUE_TYPE_INSERT)))
            {
                $this->getDefaultValue($obMap, $columnName);
            }
            elseif ($obMap->isRequired() && $obMap->isRequiredNull())
            {
                $this->addDataToInsertDataCollection(
                    $helper->wrapQuotes($columnName),
                    'NULL'
                );
            }
            else
            {
                throw new ArgumentNullException('field[' . $fieldName . ']');
            }
        }

        return $this;
    }

    /**
     * Возвращает INSERT зпрос
     *
     * @param $bFirst
     *
     * @return string
     */
    private function getInsertSql (&$bFirst)
    {
        $sql = '';
        $collection = $this->getInsertDataCollection();
        if ($bFirst)
        {
            $bFirst = false;
            $sql .= "(" . implode(',', $collection->getKeysArray()) . ")\nVALUES\n\t(" . implode(',',
                                                                                                 $collection->getValuesArray(
                                                                                                 )
                ) . ")";
        }
        else
        {
            $sql .= ",\n\t(" . implode(',', $collection->getValuesArray()) . ")";
        }

        return $sql;
    }

    /* *
     * @param                            $arValue
     * @param Fields\ScalarFieldAbstract $obMap
     * @param                            $columnName
     *
     * @return QueryInsert
     * @throws ArgumentNullException
     * /
    private function getRunValue (&$arValue, Fields\ScalarFieldAbstract &$obMap, $columnName)
    {
        $helper = new SqlHelper();
        $arRun = $obMap->getRun();
        //Если не задана функция, выбрасываем исключение
        if (!isset($arRun['function']))
        {
            throw new ArgumentNullException('$arRun["function"]');
        }
        //Если задана колонка, из которой берется значение
        if (isset($arRun['column']))
        {
            $bSetColumns = true;
            //Если задан массив колонок, проверяем заданы ли они все
            if (is_array($arRun['column']))
            {
                foreach ($arRun['column'] as $col)
                {
                    if (!isset($arValue[$col]))
                    {
                        $bSetColumns = false;
                        break;
                    }
                }
            }
            //Если колонка только одна, проверяем задана ли она
            elseif (!isset($arValue[$arRun['column']]))
            {
                $bSetColumns = false;
            }
            //Если значение не всех колонок было установлено, выбрасываем исключение
            if (!$bSetColumns)
            {
                throw new ArgumentNullException('$arValue[$arRun["column"]]');
            }
            //Иначе
            else
            {
                //Если указанная функция существует и может быть выполнена
                if (is_callable($arRun['function']))
                {
                    //Выполняем функцию, передав ей все заданные колонки
                    $arParams = ["VALUES" => $arValue, 'COLUMNS' => $arRun['column']];
                    $res = call_user_func($arRun['function'], $arParams);
                    //Результат работы функции записываем в значение колонки
                    $this->addDataToInsertDataCollection(
                        $helper->wrapQuotes($columnName),
                        $obMap->getSqlValue($res)
                    );
                }
                //Иначе, выбрасываем исключение
                else
                {
                    throw new \BadFunctionCallException(strval($arRun["function"]));
                }
            }
        }

        return $this;
    }*/

    /**
     * Получает сохраненные значения
     *
     * @param        $arValue
     * @param IField $obMap
     * @param        $fieldName
     * @param        $columnName
     *
     * @return QueryInsert
     */
    private function getSaveValue (&$arValue, IField &$obMap, $fieldName, $columnName)
    {
        $helper = new SqlHelper();
        //Получаем класс данного поля
        //Получаем обработанное значение
        $arValue[$fieldName] = $obMap->saveDataModification($arValue[$fieldName]);
        //Сохраняем значение и имя поля
        $this->addDataToInsertDataCollection(
            $helper->wrapQuotes($columnName),
            $obMap->getSqlValue($arValue[$fieldName])
        );

        return $this;
    }
}
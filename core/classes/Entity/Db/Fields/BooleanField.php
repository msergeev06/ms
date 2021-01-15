<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Interfaces\Db\IField;
use Ms\Rent\Lib\Fields;

/**
 * Класс Ms\Core\Entity\Db\Fields\BooleanField
 * Сущность поля базы данных, содержащего булево значение
 */
class BooleanField extends ScalarFieldAbstract
{
    /**
     * @var int Размер типа поля в базе данных
     */
    protected $size = 1;
    /**
     * Value (false, true) equivalent map
     *
     * @var array
     */
    protected $values;

    /**
     * Обрабатывает значение поля после получения из базы данных
     *
     * @param string            $value
     *
     * @return array|mixed
     * @unittest
     */
    public function fetchDataModification ($value)
    {
        $value = $this->normalizeValue($value);

        $value = parent::fetchDataModification($value);

        return $value;
    }

    /**
     * Обрабатывает значение поля перед записью в базу данных
     *
     * @param mixed             $value
     *
     * @return mixed|string
     * @unittest
     */
    public function saveDataModification ($value)
    {
        $value = $this->normalizeValue($value);

        if ($value)
        {
            $value = 'Y';
        }
        else
        {
            $value = 'N';
        }
        $value = parent::saveDataModification($value);

        return $value;
    }

    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     */
    function __construct ($name)
    {
        parent::__construct($name);

        $this->dataType = 'varchar';
        $this->fieldType = 'boolean';

        $this->values = [false, true];
    }

    /**
     * Возвращает значение по-умолчанию для базы данных
     *
     * @return null|string
     * @unittest
     */
    public function getDefaultValueDB ()
    {
        $value = $this->getDefaultValue();
        if (!is_null($value))
        {
            if ($value === true)
            {
                return 'Y';
            }
            else
            {
                return 'N';
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * Возвращает размер типа поля в базе данных
     *
     * @return int
     * @unittest
     */
    public function getSize ()
    {
        return $this->size;
    }

    /**
     * Возвращает значение приобразованное в формат SQL запроса
     *
     * @param mixed $value значение
     *
     * @return string
     * @unittest
     */
    public function getSqlValue ($value): string
    {
        if ($value === true || $value === 'Y' || $value === 1)
        {
            return "'Y'";
        }
        else
        {
            return "'N'";
        }
    }

    /**
     * Возвращает варианты значений поля
     *
     * @return array
     * @unittest
     */
    public function getValues ()
    {
        return $this->values;
    }

    /**
     * Конвертирует значения, которые можно интерпретировать как TRUE/FALSE в актуальные для поля значения
     *
     * @param boolean|integer|string $value
     *
     * @return bool
     * @unittest
     */
    public function normalizeValue ($value)
    {
        if (
            (is_string($value) && ($value == '1' || $value == '0'))
            || (is_bool($value))
        )
        {
            $value = (int)$value;
        }
        elseif (is_string($value) && ($value == 'true' || $value == 'Y'))
        {
            $value = 1;
        }
        elseif (is_string($value) && ($value == 'false' || $value == 'N'))
        {
            $value = 0;
        }

        if (is_integer($value) && ($value == 1 || $value == 0))
        {
            $value = $this->values[$value];
        }

        return $value;
    }

    /**
     * Устанавливает значение по-умолчанию для действия CREATE
     *
     * @param bool|string $defaultCreate
     *
     * @return $this
     * @throws ArgumentTypeException
     * @unittest
     */
    public function setDefaultCreate ($defaultCreate): IField
    {
        if (!is_bool($defaultCreate) && !is_string($defaultCreate))
        {
            throw new ArgumentTypeException('$defaultCreate', 'bool|string');
        }

        parent::setDefaultCreate($defaultCreate);

        return $this;
    }

    /**
     * Устанавливает значение по-умолчанию для действия INSERT
     *
     * @param bool|string $defaultInsert
     *
     * @return $this
     * @throws ArgumentTypeException
     * @unittest
     */
    public function setDefaultInsert ($defaultInsert): IField
    {
        if (!is_bool($defaultInsert) && !is_string($defaultInsert))
        {
            throw new ArgumentTypeException('$defaultInsert', 'bool|string');
        }

        parent::setDefaultInsert($defaultInsert);

        return $this;
    }

    /**
     * Устанавливает значение по-умолчанию для действия UPDATE
     *
     * @param bool|string $defaultUpdate
     *
     * @return $this
     * @throws ArgumentTypeException
     * @unittest
     */
    public function setDefaultUpdate ($defaultUpdate): IField
    {
        if (!is_bool($defaultUpdate) && !is_string($defaultUpdate))
        {
            throw new ArgumentTypeException('$defaultUpdate', 'bool|string');
        }
        parent::setDefaultUpdate($defaultUpdate);

        return $this;
    }

    /**
     * Устанавливает значение по-умолчанию для всех действий
     *
     * @param bool|string $defaultValue
     *
     * @return $this
     * @throws ArgumentTypeException
     * @unittest
     */
    public function setDefaultValue ($defaultValue): IField
    {
        if (!is_bool($defaultValue) && !is_string($defaultValue))
        {
            throw new ArgumentTypeException('$defaultValue', 'bool|string');
        }

        parent::setDefaultValue($defaultValue);

        return $this;
    }

    /**
     * Возвращает SQL код устанавливающий размерность поля, если необходимо, либо пустую строку
     *
     * @return string
     */
    public function getSizeSql (): string
    {
        return '(' . $this->getSize() . ')';
    }
}
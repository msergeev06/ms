<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Entity\System\Application;
use Ms\Core\Exceptions;
use Ms\Core\Interfaces\Db\IField;

/**
 * Класс Ms\Core\Entity\Db\Fields\StringField
 * Сущность поля базы данных, содержащего строку
 */
class StringField extends ScalarFieldAbstract
{
    /**
     * @var int Размер типа varchar базы данных
     */
    protected $size = 255;

    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     */
    public function __construct ($name)
    {
        parent::__construct($name);

        $this->dataType = 'varchar';
        $this->fieldType = 'string';
    }

    /**
     * Обрабатывает значение поля после получения из базы данных
     *
     * @param string $value
     *
     * @return array|mixed
     * @unittest
     */
    public function fetchDataModification ($value)
    {
        $value = parent::fetchDataModification($value);

        return $value;
    }

    /**
     * Возвращает размер поля в базе данных (в символах)
     *
     * @return int|null
     * @unittest
     */
    public function getSize ()
    {
        return $this->size;
    }

    /**
     * Обрабатывает значение поля перед сохранением в базе данных
     *
     * @param                   $value
     *
     * @return mixed|string
     * @unittest
     */
    public function saveDataModification ($value)
    {
        $DB = Application::getInstance()->getConnection();
        $value = parent::saveDataModification($value);
        //$value = mysql_real_escape_string($value);
        if (!is_null($value) && $value != '')
        {
            $value = $DB->getRealEscapeString($value);
        }
        $value = str_replace("%", "\%", $value);

        return $value;
    }

    /**
     * Устанавливает значение по-умолчанию для действия CREATE
     *
     * @param string $defaultCreate
     *
     * @return $this
     * @throws Exceptions\Arguments\ArgumentTypeException
     * @unittest
     */
    public function setDefaultCreate ($defaultCreate): IField
    {
        if (!is_string($defaultCreate))
        {
            throw new Exceptions\Arguments\ArgumentTypeException('$defaultCreate', 'string');
        }

        return parent::setDefaultCreate($defaultCreate);
    }

    /**
     * Устанавливает значение по-умолчанию для действия INSERT
     *
     * @param string $defaultInsert
     *
     * @return $this
     * @throws Exceptions\Arguments\ArgumentTypeException
     * @unittest
     */
    public function setDefaultInsert ($defaultInsert): IField
    {
        if (!is_string($defaultInsert))
        {
            throw new Exceptions\Arguments\ArgumentTypeException('$defaultInsert', 'string');
        }

        return parent::setDefaultInsert($defaultInsert);
    }

    /**
     * Устанавливает значение по-умолчанию для действия UPDATE
     *
     * @param string $defaultUpdate
     *
     * @return $this
     * @throws Exceptions\Arguments\ArgumentTypeException
     * @unittest
     */
    public function setDefaultUpdate ($defaultUpdate): IField
    {
        if (!is_string($defaultUpdate))
        {
            throw new Exceptions\Arguments\ArgumentTypeException('$defaultUpdate', 'string');
        }

        return parent::setDefaultUpdate($defaultUpdate);
    }

    /**
     * Устанавливает значение по-умолчанию для всех действий
     *
     * @param string $defaultValue
     *
     * @return $this
     * @throws Exceptions\Arguments\ArgumentTypeException
     * @unittest
     */
    public function setDefaultValue ($defaultValue): IField
    {
        if (!is_string($defaultValue))
        {
            throw new Exceptions\Arguments\ArgumentTypeException('$defaultValue', 'string');
        }

        return parent::setDefaultValue($defaultValue);
    }

    /**
     * Устанавливает размер строки, сохраняемой в БД
     *
     * @param int $size
     *
     * @return $this
     * @unittest
     */
    public function setSize (int $size = 255)
    {
        if ((int)$size > 0 && (int)$size <= 255)
        {
            $this->size = $size;
        }
        else
        {
            $this->size = 255;
        }

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
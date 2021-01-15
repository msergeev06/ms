<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Entity\Db\Fields\FloatField
 * Сущность поля базы данных, содержащего вещественное число
 */
class FloatField extends ScalarFieldAbstract
{
    /**
     * @var int Точность, знаков после запятой
     */
    protected $scale = 2;

    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     */
    public function __construct ($name)
    {
        parent::__construct($name);

        $this->dataType = $this->fieldType = 'float';
    }

    /**
     * Обрабатывает значение поля после получения из базы данных
     *
     * @param                   $value
     *
     * @return array|float|mixed
     * @unittest
     */
    public function fetchDataModification ($value)
    {
        if (!is_null($value))
        {
            $scale = $this->getScale();
            $value = round($value, $scale);
        }

        return $value;
    }

    /**
     * Возвращает точность (количество знаков после запятой)
     *
     * @return int|null
     * @unittest
     */
    public function getScale ()
    {
        return $this->scale;
    }

    /**
     * Возвращает значение поля в SQL формате
     *
     * @param float $value
     *
     * @return string
     * @unittest
     */
    public function getSqlValue ($value): string
    {
        return (string)$value;
    }

    /**
     * Обрабатывает значение поля перед сохранением в базе данных
     *
     * @param                   $value
     *
     * @return float|mixed|string
     * @unittest
     */
    public function saveDataModification ($value)
    {
        if (!is_null($value))
        {
            $scale = $this->getScale();
            $value = round($value, $scale);
        }

        return $value;
    }

    /**
     * Устанавливает точность (количество знаков после запятой) значения
     *
     * @param int $scale
     *
     * @return FloatField
     * @unittest
     */
    public function setScale (int $scale)
    {
        $this->scale = $scale;

        return $this;
    }
}
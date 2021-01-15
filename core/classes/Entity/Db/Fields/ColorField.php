<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Entity\Type\Color;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;

/**
 * Класс Ms\Core\Entity\Db\Fields\ColorField
 * Сущность поля базы данных, содержащего цвет
 */
class ColorField extends IntegerField
{
    /**
     * Обрабатывает значение поля после получения из базы данных
     *
     * @param Color|int       $value
     *
     * @return array|bool|mixed|string
     */
    public function fetchDataModification ($value)
    {
        if (!is_null($value))
        {
            if ($value instanceof Color)
            {
                $color = $value;
            }
            else
            {
                $color = (new Color())->setFromRgbInt($value);
            }
            if ($color->isCorrect())
            {
                $value = $color;
            }
        }

        return $value;
    }

    /**
     * Обрабатывает значение поля перед сохранением в базу данных
     *
     * @param Color|null      $value
     *
     * @return bool|mixed|int
     * @throws ArgumentTypeException
     */
    public function saveDataModification ($value)
    {
        if (!is_null($value))
        {
            if (!($value instanceof Color))
            {
                throw new ArgumentTypeException((int)$value, 'Ms\Core\Entity\Type\Color');
            }
            else
            {
                $value = $value->getFormatInteger();
            }
        }

        return $value;
    }

    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     */
    public function __construct ($name)
    {
        parent::__construct($name);

        $this->dataType = 'int';
        $this->fieldType = Color::class;
    }
}
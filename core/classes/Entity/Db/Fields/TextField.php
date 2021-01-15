<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Entity\Db\Fields\TextField
 * Сущность поля базы данных, содержащего текст
 */
class TextField extends StringField
{
    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     */
    function __construct ($name)
    {
        parent::__construct($name);

        $this->dataType = $this->fieldType = 'text';

        return $this;
    }
}
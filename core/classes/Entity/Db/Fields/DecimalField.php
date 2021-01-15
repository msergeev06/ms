<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Entity\Db\Fields\DecimalField
 * Поле для хранения точных десятичных дробей
 */
class DecimalField extends FloatField
{
    protected $size = 10;

    public function __construct ($name)
    {
        parent::__construct($name);

        $this->dataType = 'decimal';
        $this->fieldType = 'float';
    }

    public function getSize ()
    {
        return $this->size;
    }

    public function setSize (int $size = 10)
    {
        $this->size = (int)$size;

        return $this;
    }

    /**
     * Возвращает SQL код устанавливающий размерность поля, если необходимо, либо пустую строку
     *
     * @return string
     */
    public function getSizeSql (): string
    {
        return '(' . $this->getSize() . ', ' . $this->getScale() . ')';
    }
}
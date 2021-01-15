<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Type;

/**
 * Класс Ms\Core\Entity\Type\Associative
 * Содержит название поля и его значение. Может служить заменой записи в ассоциативном массиве
 */
class Associative
{
    /** @var string */
    protected $name = null;
    /** @var null|mixed */
    protected $value = null;

    public function __construct (string $name, $value = null)
    {
        $this->setName($name);
        $this->setValue($value);
    }

    /**
     * Возвращает значение свойства name
     *
     * @return string
     */
    public function getName (): string
    {
        return $this->name;
    }

    /**
     * Устанавливает значение свойства name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName (string $name): Associative
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Возвращает значение свойства value
     *
     * @return mixed|null
     */
    public function getValue ()
    {
        return $this->value;
    }

    /**
     * Устанавливает значение свойства value
     *
     * @param mixed|null $value
     *
     * @return $this
     */
    public function setValue ($value = null)
    {
        $this->value = $value;

        return $this;
    }
}
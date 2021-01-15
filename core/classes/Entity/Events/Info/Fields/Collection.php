<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Events\Info\Fields;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\Events\Info\Fields\Collection
 * Коллекция полей массива, передаваемого в качестве параметра в обработчик события. Также может описывать поля
 * многомерных массивов
 */
class Collection extends Dictionary
{
    public function addField (Field $field)
    {
        $this->offsetSet($field->getName(),$field);
    }

    public function isset(string $name)
    {
        return $this->offsetExists($name);
    }

    public function getField (string $name)
    {
        if (!$this->isset($name))
        {
            return null;
        }

        return $this->offsetGet($name);
    }
}
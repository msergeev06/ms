<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\System\Multiton;

/**
 * Класс Ms\Core\Lib\ConstantsAbstract
 * Абстрактный класс констант
 */
abstract class ConstantsAbstract extends Multiton
{
    protected $reflection = null;

    protected function __construct ()
    {
        try
        {
            $this->reflection = new \ReflectionClass(get_called_class());
        }
        catch (\ReflectionException $e)
        {

        }
    }

    public function getConstants ()
    {
        if (!is_null($this->reflection))
        {
            return $this->reflection->getConstants();
        }

        return [];
    }

    public function getList ()
    {
        $arConstants = $this->getConstants();
        if (empty($arConstants))
        {
            return [];
        }

        return array_values($arConstants);
    }

    abstract public function getName ($value);
}
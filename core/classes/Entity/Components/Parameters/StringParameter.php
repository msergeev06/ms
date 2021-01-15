<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core\Entity\Components\Parameters
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Components\Parameters;

/**
 * Класс Ms\Core\Entity\Components\Parameters\StringParameter
 * Параметр компонента типа STRING
 */
class StringParameter extends Parameter
{
    const COLS_DEFAULT = 20;

    protected $cols = null;

    public function __construct (string $code)
    {
        parent::__construct($code);
        $this->cols = self::COLS_DEFAULT;
        try
        {
            $this->setType('STRING');
        }
        catch (\Exception $e)
        {
        }
    }

    public function getCols ()
    {
        return $this->cols;
    }

    public function setCols (int $cols)
    {
        $this->cols = (int)$cols;

        return $this;
    }

    public function getValue ()
    {
        return (string)parent::getValue();
    }
}
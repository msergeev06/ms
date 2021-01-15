<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core\Entity\Components\Parameters
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Components\Parameters;

use Ms\Core\Entity\Errors\FileLogger;
use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;

/**
 * Класс Ms\Core\Entity\Components\Parameters\ListParameter
 * Параметр компонента типа LIST
 */
class ListParameter extends Parameter
{
    const SIZE_DEFAULT = 1;
    const COLS_DEFAULT = 20;

    protected $arValues = [];
    protected $size = null;
    protected $cols = null;

    public function __construct (string $code)
    {
        parent::__construct($code);
        try
        {
            $this->setType('LIST');
        }
        catch (\Exception $e)
        {
        }
        $this->size = self::SIZE_DEFAULT;
        $this->cols = self::COLS_DEFAULT;
    }

    public function getValues ()
    {
        return $this->arValues;
    }

    public function setValues (array $arValues = [])
    {
        $this->arValues = $arValues;

        return $this;
    }

    public function addValue (string $value, string $name)
    {
        $this->arValues[$value] = $name;

        return $this;
    }

    public function getSize ()
    {
        return (int)$this->size;
    }

    public function setSize (int $size)
    {
        $this->size = (int)$size;

        return $this;
    }

    public function getCols ()
    {
        return $this->cols;
    }

    public function setCols (int $cols)
    {
        $this->cols = $cols;

        return $this;
    }

    public function setValue ($value = null)
    {
        $arList = [];
        if (!empty($this->arValues))
        {
            foreach ($this->arValues as $key=>$name)
            {
                $arList[] = $key;
            }
        }
        if (!in_array($value,$arList))
        {
            try
            {
                throw new ArgumentOutOfRangeException($value, $arList);
            }
            catch (ArgumentOutOfRangeException $e)
            {
                $e->addMessageToLog(new FileLogger('core'));
                $value = null;
            }
        }

        parent::setValue($value);

        return $this;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Modules\Versions;

use Ms\Core\Entity\System\Multiton;
use Ms\Core\Exceptions\Arguments\ArgumentException;

/**
 * Класс Ms\Core\Entity\Modules\VersionComparator
 * Сравнение версий модулей
 */
class VersionComparator extends Multiton
{
    /**
     * Сравнивает две версии с использованием указанного оператора
     *
     * @param string|Version $version1 Первая сравниваемая версия
     * @param string         $operator Оператор сравнения
     * @param string|Version $version2 Вторая сравниваемая версия
     *
     * @return bool
     * @throws ArgumentException
     * @unittest
     */
    public function compare($version1, string $operator, $version2)
    {
        if ($version1 instanceof Version)
        {
            $v1 = $version1;
        }
        elseif (is_string($version1))
        {
            $v1 = new Version($version1);
        }
        else
        {
            throw new ArgumentException('Неверный формат переменной', 'version1', __FILE__, __LINE__);
        }
        if ($version2 instanceof Version)
        {
            $v2 = $version2;
        }
        elseif (is_string($version2))
        {
            $v2 = new Version($version2);
        }
        else
        {
            throw new ArgumentException('Неверный формат переменной', 'version2', __FILE__, __LINE__);
        }
        if (!$this->checkClearVersions($v1, $v2))
        {
            throw new ArgumentException('Неверный формат переменных', 'version1 и version2', __FILE__, __LINE__);
        }

        switch (strtolower($operator))
        {
            case '>':
                return $this->greaterThan($v1, $v2);
            case '>=':
                return $this->greaterThanOrEqualTo($v1, $v2);
            case '<':
                return $this->lessThan($v1, $v2);
            case '<=':
                return $this->lessThanOrEqualTo($v1, $v2);
            case '!=':
            case '!':
                return $this->notEqualTo($v1, $v2);
            case '=':
                return $this->equalTo($v1, $v2);
            case '~':
                return $this->isStable ($v1, $v2);
            case '^':
                return $this->notCritical ($v1, $v2);
            default:
                throw new ArgumentException('Неверный формат переменной', 'operator', __FILE__, __LINE__);
        }
    }

    /**
     * Возвращает TRUE, если обе версии правильного формата, иначе FALSE
     *
     * @param Version $v1 Первая сравниваемая версия
     * @param Version $v2 Вторая сравниваемая версия
     *
     * @return bool
     */
    protected function checkClearVersions (Version $v1, Version $v2)
    {
        return ($v1->getClearVersion() && $v2->getClearVersion());
    }

    /**
     * Возвращает TRUE, если обе версии можно считать равными
     *
     * @param Version $version1 Первая сравниваемая версия
     * @param Version $version2 Вторая сравниваемая версия
     *
     * @return bool
     */
    protected function equalTo(Version $version1, Version $version2)
    {
        $bOk = ($version1->getClearVersion() === $version2->getClearVersion());
        if ($bOk)
        {
            return true;
        }

        if ($version1->getMajor() != $version2->getMajor())
        {
            return false;
        }

        if ($version1->getMinor() != $version2->getMinor())
        {
            if (is_null($version1->getMinor()) || is_null($version2->getMinor()))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        if ($version1->getPatch() != $version2->getPatch())
        {
            if (is_null($version1->getPatch()) || is_null($version2->getPatch()))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Возвращает TRUE, если version1 больше version2
     *
     * @param Version $version1 Первая сравниваемая версия
     * @param Version $version2 Вторая сравниваемая версия
     *
     * @return bool
     */
    protected function greaterThan(Version $version1, Version $version2)
    {
        if ($version1->getMajor() > $version2->getMajor())
        {
            return true;
        }
        elseif ($version1->getMajor() < $version2->getMajor())
        {
            return false;
        }

        $minor1 = $version1->getMinor();
        if (is_null($minor1))
        {
            $minor1 = 0;
        }
        if (is_null($version2->getMinor()))
        {
            if ($minor1 > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        elseif ($minor1 > $version2->getMinor())
        {
            return true;
        }
        elseif ($minor1 < $version2->getMinor())
        {
            return false;
        }

        $patch1 = $version1->getPatch();
        if (is_null($patch1))
        {
            $patch1 = 0;
        }
        if (is_null($version2->getPatch()))
        {
            if ($patch1 > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        elseif ($patch1 > $version2->getPatch())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Возвращает TRUE, если первая сравниваемая версия больше, либо равна второй
     *
     * @param Version $version1 Первая сравниваемая версия
     * @param Version $version2 Вторая сравниваемая версия
     *
     * @return bool
     */
    protected function greaterThanOrEqualTo(Version $version1, Version $version2)
    {
        $bOk = $this->equalTo($version1, $version2);
        if ($bOk)
        {
            return true;
        }

        return $this->greaterThan($version1, $version2);
    }

    /**
     * Возвращает TRUE, если version1 является стабильной, начиная с version2,
     * т.е. для version2 ~1.2.3 стабильными будут 1.2.3, 1.2.4 ... до 1.3 (не включительно),
     * т.е. любые новые patch версии, включая указанную
     *
     * @param Version $version1 Первая сравниваемая версия
     * @param Version $version2 Вторая сравниваемая версия
     *
     * @return bool
     */
    protected function isStable (Version $version1, Version $version2)
    {
        if ($version1->getMajor() > $version2->getMajor() || $version1->getMajor() < $version2->getMajor())
        {
            return false;
        }

        $minor1 = $version1->getMinor();
        if (is_null($minor1))
        {
            $minor1 = 0;
        }
        $minor2 = $version2->getMinor();
        if (is_null($minor2))
        {
            $minor2 = 0;
        }
        if ($minor1 > $minor2 || $minor1 < $minor2)
        {
            return false;
        }

        $patch1 = $version1->getPatch();
        if (is_null($patch1))
        {
            $patch1 = 0;
        }
        $patch2 = $version2->getPatch();
        if (is_null($patch2))
        {
            $patch2 = 0;
        }
        if ($patch1 < $patch2)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Возвращает TRUE, если version1 меньше version2
     *
     * @param Version $version1 Первая сравниваемая версия
     * @param Version $version2 Вторая сравниваемая версия
     *
     * @return bool
     */
    protected function lessThan(Version $version1, Version $version2)
    {
        if ($version1->getMajor() < $version2->getMajor())
        {
            return true;
        }
        elseif ($version1->getMajor() > $version2->getMajor())
        {
            return false;
        }

        $minor1 = $version1->getMinor();
        if (is_null($minor1))
        {
            $minor1 = 0;
        }
        if (is_null($version2->getMinor()))
        {
            return false;
        }
        elseif ($minor1 < $version2->getMinor())
        {
            return true;
        }
        elseif ($minor1 > $version2->getMinor())
        {
            return false;
        }

        $patch1 = $version1->getPatch();
        if (is_null($patch1))
        {
            $patch1 = 0;
        }
        if (is_null($version2->getPatch()))
        {
            return false;
        }
        elseif ($patch1 < $version2->getPatch())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Возвращает TRUE, если первая сравниваемая версия меньше, либо равна второй
     *
     * @param Version $version1 Первая сравниваемая версия
     * @param Version $version2 Вторая сравниваемая версия
     *
     * @return bool
     */
    protected function lessThanOrEqualTo(Version $version1, Version $version2)
    {
        $bOk = $this->equalTo($version1, $version2);
        if ($bOk)
        {
            return true;
        }

        return $this->lessThan($version1, $version2);
    }

    /**
     * Возвращает TRUE, если version1 не является критической, начиная с version2,
     * т.е. для version2 ^1.2.3 не критическими будут 1.2.3, 1.2.4, 1.3, 1.4 ... до 2.0 (не включительно),
     * т.е. любые новые patch и minor версии, включая указанные
     *
     * @param Version $version1 Первая сравниваемая версия
     * @param Version $version2 Вторая сравниваемая версия
     *
     * @return bool
     */
    protected function notCritical (Version $version1, Version $version2)
    {
        if ($version1->getMajor() > $version2->getMajor() || $version1->getMajor() < $version2->getMajor())
        {
            return false;
        }

        $minor1 = $version1->getMinor();
        if (is_null($minor1))
        {
            $minor1 = 0;
        }
        if (is_null($version2->getMinor()))
        {
            return true;
        }
        elseif ($minor1 < $version2->getMinor())
        {
            return false;
        }
        elseif ($minor1 > $version2->getMinor())
        {
            return true;
        }

        $patch1 = $version1->getPatch();
        if (is_null($patch1))
        {
            $patch1 = 0;
        }
        if (is_null($version2->getPatch()))
        {
            return true;
        }
        elseif ($patch1 < $version2->getPatch())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Возвращает TRUE, если версии нельзя считать равными, иначе возвращает FALSE
     *
     * @param Version $version1 Первая сравниваемая версия
     * @param Version $version2 Вторая сравниваемая версия
     *
     * @return bool
     */
    protected function notEqualTo(Version $version1, Version $version2)
    {
        return !$this->equalTo($version1, $version2);
    }
}
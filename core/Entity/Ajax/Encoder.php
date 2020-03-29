<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Ajax;

use Ms\Core\Entity\System\Application;

/**
 * Класс Ms\Core\Entity\Ajax\Encoder
 * Перекодирует при необходимости запрос в/из UTF-8
 */
class Encoder
{
    public function convertToUTF($handle)
    {
        if (LANG_CHARSET !== 'UTF-8')
        {
            if (is_array($handle))
            {
                foreach ($handle as $key => $val)
                {
                    unset($handle[$key]);
                    $key = self::convertToUFT($key);
                    $handle[$key] = self::convertToUTF($val);
                }
            }
            else
            {
                $handle = Application::getInstance()->convertCharset($handle, LANG_CHARSET, 'UTF-8');
            }
        }

        return $handle;
    }

    public function convertFromUTF ($handle)
    {
        if (LANG_CHARSET !== 'UTF-8')
        {
            if (is_array($handle))
            {
                foreach ($handle as $key => $val)
                {
                    unset($handle[$key]);
                    $key = self::convertFromUTF($key);
                    $handle[$key] = self::convertFromUTF($val);
                }
            }
            else
            {
                $handle = Application::getInstance()->convertCharset($handle, 'UTF-8', LANG_CHARSET);
            }
        }

        return $handle;
    }

    public function hardConvertToUTF($handle, $from = LANG_CHARSET)
    {
        if (is_array($handle))
        {
            foreach ($handle as $key => $val)
            {
                unset($handle[$key]);
                $key = self::hardConvertToUTF($key, $from);
                $handle[$key] = self::hardConvertToUTF($val, $from);
            }
        }
        else
        {
            $handle = Application::getInstance()->convertCharset($handle, $from, 'UTF-8');
        }

        return $handle;
    }

    public function hardConvertFromUTF($handle, $to = LANG_CHARSET)
    {
        if (is_array($handle))
        {
            foreach ($handle as $key => $val)
            {
                unset($handle[$key]);
                $key = self::hardConvertFromUTF($key, $to);
                $handle[$key] = self::hardConvertFromUTF($val, $to);
            }
        }
        else
        {
            $handle = Application::getInstance()->convertCharset($handle, 'UTF-8', $to);
        }

        return $handle;
    }
}

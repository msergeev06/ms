<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Localization;

use Ms\Core\Entity\System\Dictionary;

/**
 * Класс Ms\Core\Entity\System\Localization\MessagesCollection
 * Коллекция языковых сообщений для языка сайта
 */
class MessagesCollection extends Dictionary
{
    /**
     * Добавляет языковые фразы из массива
     *
     * @param array  $arMessages Массив языковых фраз
     * @param string $prefix     Префикс ключа фразы
     *
     * @return $this
     */
    public function addFromArray (array $arMessages, string $prefix = '')
    {
        if (empty($arMessages))
        {
            return $this;
        }

        foreach ($arMessages as $key => $message)
        {
            $this->addMessage($key, $message, $prefix);
        }

        return $this;
    }

    /**
     * Добавляет языковую фразу
     *
     * @param string $key     Ключ языковой фразы
     * @param string $message Фраза
     * @param string $prefix  Префикс ключа фразы
     *
     * @return $this
     */
    public function addMessage (string $key, string $message, string $prefix = '')
    {
        $key = str_replace($prefix, '', $key);
        $offset = strtolower($prefix . $key);
        $this->offsetSet($offset, $message);

        return $this;
    }

    /**
     * Возвращает языковую фразу из коллекции, либо FALSE
     *
     * @param string $key    Ключ языковой фразы
     * @param string $prefix Префикс языковой фразы
     *
     * @return bool|string
     */
    public function getMessage (string $key, string $prefix = '')
    {
        if (!$this->offsetExists(strtolower($prefix . $key)))
        {
            return false;
        }

        return $this->offsetGet(strtolower($prefix . $key));
    }
}
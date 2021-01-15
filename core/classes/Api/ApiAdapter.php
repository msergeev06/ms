<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Api;

use Ms\Core\Entity\System\Multiton;

/**
 * Класс Ms\Core\Api\ApiAdapter
 * Адаптер к API ядра
 */
class ApiAdapter extends Multiton
{
    /**
     * Возвращает ссылку на объект доступа к API методам работы с событиями
     *
     * @return \Ms\Core\Api\Events
     */
    public function getEventsApi ()
    {
        return \Ms\Core\Api\Events::getInstance();
    }

    /**
     * Возвращает ссылку на объект доступа к API методам работы с настройками
     *
     * @return \Ms\Core\Api\Options
     */
    public function getOptionsApi ()
    {
        return \Ms\Core\Api\Options::getInstance();
    }

    /**
     * Возвращает ссылку на объект доступа к API методам работы с базой данных и таблицами
     *
     * @return \Ms\Core\Api\Db
     */
    public function getDbApi ()
    {
        return \Ms\Core\Api\Db::getInstance();
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\DbHandlers;

use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Tables\UsersTable;

/**
 * Класс Ms\Core\DbHandlers\UsersDbHandler
 * Работает с таблицей пользователей БД
 */
class UsersDbHandler extends Multiton
{
    protected $orm = null;

    protected function __construct ()
    {
        $this->orm = ORMController::getInstance(new UsersTable());
    }

    /**
     * Возвращает ORM таблицы ms_core_users
     *
     * @return ORMController|null
     */
    public function getOrm ()
    {
        return $this->orm;
    }

    /**
     * Выполняет getList для таблицы ms_core_users
     *
     * @param array $arParams
     * @param bool  $bShowSql
     *
     * @return array|bool|string
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentTypeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     */
    public function getList (array $arParams, bool $bShowSql = false)
    {
        return $this->orm->getList($arParams, $bShowSql);
    }

    /**
     * Обновляет HASH пароля пользователя
     *
     * @param int         $userID ID пользователя
     * @param string|null $hash   Новый HASH
     *
     * @return \Ms\Core\Entity\Db\Result\DBResult|string
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     * @throws \Ms\Core\Exceptions\Db\ValidateException
     * @throws \Ms\Core\Exceptions\SystemException
     */
    public function updateHash (int $userID, string $hash = null)
    {
        $now = new Date();

        return $this->orm->updateByPrimary($userID,['LAST_ACTIVITY'=>$now, 'HASH' => $hash]);
    }

    /**
     * Проверяет правильность пары ID пользователя и HASH. Если они верны, возвращает их в виде массива, иначе FALSE
     *
     * @param int    $userID ID пользователя
     * @param string $hash   HASH
     *
     * @return array|bool|string
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentTypeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     */
    public function checkHash (int $userID, string $hash)
    {
        return $this->orm->getList(
            [
                'select' => ['ID','HASH'],
                'filter' => [
                    'ID' => $userID,
                    'HASH' => $hash
                ]
            ]
        );
    }
}
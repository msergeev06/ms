<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Interfaces;

use Ms\Core\Exceptions\Db\ConnectionException;

/**
 * Интерфейс Ms\Core\Interfaces\ISqlDriver
 * Драйвер для работы с базой данных
 */
interface ISqlDriver
{
    public function __construct();

    public function __destruct();

    public function close(): bool;

    public function commitTransaction ();

    /**
     * <Описание>
     *
     * @param string $host
     * @param string $base
     * @param string $user
     * @param string $pass
     *
     * @return mixed
     * @throws ConnectionException
     */
    public function connect (string $host, string $base, string $user, string $pass);

    public function fetchArray ();

    public function getAffectedRows (): int;

    public function getConnection ();

    public function getError (): string;

    public function getErrorNo (): int;

    public function getInsertId (): int;

    public function getNumFields (): int;

    public function getNumRows (): int;

    public function getRealEscapeString (string $string): string;

    public function getResult ();

    public function getSql (): string;

    public function isSuccess (): bool;

    public function ping ();

    public function query (string $sql): self;

    public function reconnect ();

    public function rollbackTransaction ();

    public function setCharset (string $charset);

    public function startTransaction ();
}
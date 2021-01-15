<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2017 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

use Ms\Core\Lib;

/**
 * Абстрактный класс Ms\Core\Entity\System\Request
 * Содержит текущий запрос
 */
abstract class Request extends ParameterDictionary
{
    /**
     * Запрошенная страница
     *
     * @var string
     */
    protected $requestedPage = null;
    /**
     * Запрошенный раздел
     *
     * @var string
     */
    protected $requestedPageDirectory = null;
    /**
     * Объект сервер
     *
     * @var Server
     */
    protected $server;

    /**
     * Создает объект запроса
     *
     * @param Server $server  Объект сервер
     * @param array  $request Массив параметров запроса
     */
    public function __construct (Server $server, array $request)
    {
        parent::__construct($request);

        $this->server = $server;
    }

    /**
     * Возвращает имя файла скрипта, который сейчас выполняется, относительно корня
     * Является оберткой функции Server::getPhpSelf
     *
     * @return null|string
     * @uses Server::getPhpSelf
     */
    public function getPhpSelf ()
    {
        return $this->server->getPhpSelf();
    }

    /**
     * Возвращает путь к запрошенной странице
     *
     * @return mixed|null|string
     * @throws \Ms\Core\Exceptions\IO\InvalidPathException
     */
    public function getRequestedPage ()
    {
        if ($this->requestedPage === null)
        {
            $page = $this->getScriptName();
            if (!empty($page))
            {
                $page = Lib\IO\Path::normalize($page);

                if (substr($page, 0, 1) !== "/" && !preg_match("#^[a-z]:[/\\\\]#i", $page))
                {
                    $page = "/" . $page;
                }
            }
            $this->requestedPage = $page;
        }

        return $this->requestedPage;
    }

    /**
     * Возвращает путь к запрошенному разделу
     *
     * @return string
     * @throws \Ms\Core\Exceptions\IO\InvalidPathException
     */
    public function getRequestedPageDirectory ()
    {
        if ($this->requestedPageDirectory === null)
        {
            $requestedPage = $this->getRequestedPage();
            $this->requestedPageDirectory = Lib\IO\Path::getDirectory($requestedPage);
        }

        return $this->requestedPageDirectory;
    }

    /**
     * Возвращает абсолютный путь к скрипту, который в данный момент исполняется
     * Является оберткой функции Server::getScriptName
     *
     * @return null|string
     * @uses Server::getScriptName
     *
     */
    public function getScriptName ()
    {
        return $this->server->getScriptName();
    }

    /**
     * Возвращает true, есди текущий запрос является AJAX запросом
     *
     * @return bool
     */
    public function isAjaxRequest ()
    {
        return (
            $this->server->get("HTTP_MS_AJAX") !== null
            || $this->server->get("HTTP_X_REQUESTED_WITH") === "XMLHttpRequest"
        );
    }
}
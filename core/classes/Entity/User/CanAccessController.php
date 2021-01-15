<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\User;

use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Exceptions\Access\CanAccessHandlerNotExistsException;
use Ms\Core\Exceptions\Classes\ClassNotFoundException;
use Ms\Core\Exceptions\Classes\ObjectNotInstanceOfAClassException;
use Ms\Core\Exceptions\Modules\WrongModuleNameException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Interfaces\ICanAccessHandler;
use Ms\Core\Lib\Modules;
use Ms\Core\Tables\CanAccessHandlersTable;

/**
 * Класс Ms\Core\Entity\User\CanAccessController
 * Объект контроллера обработчиков прав доступа
 */
class CanAccessController extends Multiton
{
    /** @var ICanAccessHandler[] */
    protected $arHandlers = [];

    /**
     * Возвращает значение флага указанного права доступа для пользователя, используя обработчик прав доступа
     *
     * @param User   $user         Пользователь, для которого определяются права доступа
     * @param string $moduleName   Имя модуля, для которого проверяются права доступа
     * @param string $accessName   Имя прав доступа. Будет преобразовано к нижнему регистру
     * @param array  $arParams     Массив дополнительных параметров, для определения прав доступа
     * @param bool   $bIgnoreAdmin Флаг необходимости игнорирования неограниченных прав администратора
     *
     * @return bool
     * @throws CanAccessHandlerNotExistsException
     * @throws ClassNotFoundException
     * @throws ObjectNotInstanceOfAClassException
     * @throws WrongModuleNameException
     */
    public function can (User $user, string $moduleName, string $accessName, array $arParams = [], bool $bIgnoreAdmin = false)
    {
        $accessName = strtolower($accessName);
        if (!Modules::checkModuleName($moduleName))
        {
            throw new WrongModuleNameException($moduleName,__FILE__,__LINE__);
        }

        if (isset($this->arHandlers[$moduleName][$accessName]))
        {
            return $this->arHandlers[$moduleName][$accessName]->can($user, $moduleName, $accessName,$arParams, $bIgnoreAdmin);
        }

        $this->arHandlers[$moduleName][$accessName] = $this->getHandler($moduleName, $accessName);

        return $this->arHandlers[$moduleName][$accessName]->can($user, $moduleName, $accessName, $arParams, $bIgnoreAdmin);
    }

    /**
     * Возвращает объект обработчика прав доступа
     *
     * @param string $moduleName Имя модуля, для которого проверяются права доступа
     * @param string $accessName Имя прав доступа. Будет преобразован в нижний регистр
     *
     * @return mixed
     * @throws CanAccessHandlerNotExistsException
     * @throws ClassNotFoundException
     * @throws ObjectNotInstanceOfAClassException
     * @throws WrongModuleNameException
     */
    public function getHandler (string $moduleName, string $accessName)
    {
        $accessName = strtolower($accessName);
        if (!Modules::checkModuleName($moduleName))
        {
            throw new WrongModuleNameException($moduleName,__FILE__,__LINE__);
        }

        if (isset($this->arHandlers[$moduleName][$accessName]))
        {
            return $this->arHandlers[$moduleName][$accessName];
        }

        try
        {
            $arRes = $this->getOrmCanAccessHandlersTable()->getOne(
                [
                    'filter' => ['CODE' => $accessName, 'MODULE'=>$moduleName]
                ]
            );
        }
        catch (SystemException $e)
        {
            $arRes = false;
        }
        if (!$arRes)
        {
            throw new CanAccessHandlerNotExistsException($accessName);
        }

        if (!class_exists($arRes['HANDLER']))
        {
            throw new ClassNotFoundException($arRes['HANDLER']);
        }

        $obj = new $arRes['HANDLER']();
        if (!($obj instanceof ICanAccessHandler))
        {
            throw new ObjectNotInstanceOfAClassException(
                '\Ms\Core\Interfaces\ICanAccessHandler',
                __FILE__,
                __LINE__
            );
        }

        return $obj;
    }

    /**
     * Добавляет, либо обновляет обработчик
     *
     * @param string            $moduleName       Имя модуля, который добавил обработчик
     * @param string            $accessName       Имя права доступа
     * @param ICanAccessHandler $canAccessHandler Объект обработчик
     *
     * @return bool
     * @throws WrongModuleNameException
     */
    public function setHandler (string $moduleName, string $accessName, ICanAccessHandler $canAccessHandler)
    {
        $accessName = strtolower($accessName);
        if (!Modules::checkModuleName($moduleName))
        {
            throw new WrongModuleNameException($moduleName);
        }

        try
        {
            $arRes = $this->getOrmCanAccessHandlersTable()->getOne(
                [
                    'filter' => ['CODE' => $accessName, 'MODULE'=>$moduleName]
                ]
            );
        }
        catch (SystemException $e)
        {
            $arRes = false;
        }
        $arAddUpdate = [
            'CODE' => $accessName,
            'MODULE' => $moduleName,
            'HANDLER' => $canAccessHandler->getClassName()
        ];
        if (!$arRes)
        {
            try
            {
                $res = $this->getOrmCanAccessHandlersTable()->insert($arAddUpdate);
            }
            catch (SystemException $e)
            {
                $res = new DBResult();
            }
        }
        else
        {
            unset($arAddUpdate['CODE']);
            try
            {
                $res = $this->getOrmCanAccessHandlersTable()->update($accessName, $arAddUpdate);
            }
            catch (SystemException $e)
            {
                $res = new DBResult();
            }
        }

        if ($res->isSuccess())
        {
            $this->arHandlers[$moduleName][$accessName] = $canAccessHandler;
        }

        return $res->isSuccess();
    }

    /**
     * Удаляет зарегистрированный обработчик прав доступа
     *
     * @param string            $moduleName       Имя модуля, добавившего обработчик
     * @param string            $accessName       Имя права доступа. Будет преобразовано к нижнему регистру
     * @param ICanAccessHandler $canAccessHandler Объект-обработчик
     *
     * @return bool
     * @throws WrongModuleNameException
     */
    public function removeHandler (string $moduleName, string $accessName, ICanAccessHandler $canAccessHandler)
    {
        if (!Modules::checkModuleName($moduleName))
        {
            throw new WrongModuleNameException($moduleName);
        }
        $accessName = strtolower($accessName);

        try
        {

            $arRes = $this->getOrmCanAccessHandlersTable()->getOne(
                [
                    'filter' => [
                        'CODE'    => $accessName,
                        'MODULE'  => $moduleName,
                        'HANDLER' => $canAccessHandler->getClassName()
                    ]
                ]
            );
        }
        catch (SystemException $e)
        {
            return false;
        }
        if ($arRes)
        {
            try
            {
                $res = $this->getOrmCanAccessHandlersTable()->delete($accessName);
            }
            catch (SystemException $e)
            {
                return false;
            }

            if ($res->isSuccess() && isset($this->arHandlers[$accessName]))
            {
                unset($this->arHandlers[$accessName]);
            }

            return $res->isSuccess();
        }

        return false;
    }

    private function getOrmCanAccessHandlersTable()
    {
        return ORMController::getInstance(new CanAccessHandlersTable());
    }
}
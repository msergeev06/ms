<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Ajax;

use Ms\Core\Entity\System\Multiton;
use Ms\Core\Exceptions\AjaxException;
use Ms\Core\Exceptions\Classes\MethodNotFoundException;
use Ms\Core\Exceptions\Classes\MethodNotPublicException;
use Ms\Core\Exceptions\NotSupportedException;

/**
 * Абстрактный класс \Ms\Core\Entity\Ajax\AjaxHandlerAbstract
 * Обработчик AJAX-запросов
 */
abstract class AjaxHandlerAbstract extends Multiton
{
    /**
     * Возвращает TRUE, если метод, указанный в action существует в классе
     *
     * @param $action
     *
     * @return bool
     */
    public function methodExists ($action)
    {
        if (empty($action))
        {
            return false;
        }

        if (method_exists($this, $action))
        {
            if (class_exists('ReflectionClass'))
            {
                try
                {
                    $checker = new \ReflectionClass ($this);
                    if ($checker->getMethod($action)->isPublic())
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                catch (\ReflectionException $e)
                {
                    return false;
                }
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Обрабатывает запрос
     *
     * @param array  $request Массив с данными запроса
     * @param string $file    Имя файла, где произошло исключение
     * @param int    $line    Номер строки в файле, где произошло исключение
     *
     * @return array
     */
    public function processRequest (array $request, string $file = __FILE__, int $line = __LINE__)
    {
        try
        {
            $that = static::getInstance();
            $action = $request['action'];

            if (empty($action))
            {
                throw new AjaxException('Action not set',$file,$line);
            }

            if (!$this->methodExists($action))
            {
                throw new AjaxException('Unknown action "'.$action.'"',$file,$line);
            }

            $data = &$request['data'];

            $data = call_user_func([$that, $action], $data);
            $success = true;
        }
        catch (\Throwable $e)
        {
            $success = false;
            $data = [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }

        return [
            'success' => $success,
            'data' => $data
        ];
    }
}

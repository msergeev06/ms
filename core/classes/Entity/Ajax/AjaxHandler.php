<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core\Entity\Ajax
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Ajax;

use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Exceptions\Classes\ClassNotFoundException;
use Ms\Core\Exceptions\Classes\MethodNotFoundException;

/**
 * Класс Ms\Core\Entity\Ajax\AjaxHandler
 * Обработчик Ajax запросов ядра
 */
class AjaxHandler extends AjaxHandlerAbstract
{
    /**
     * Передает поля формы для проверки указанному обработчику, возвращая флаг успеха, либо список ошибок
     *
     * @param array $arPost Массив полученных данных из формы
     *
     * @return array
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Classes\ClassNotFoundException
     * @throws \Ms\Core\Exceptions\Classes\MethodNotFoundException
     */
    public function checkForm ($arPost)
    {
        if (!isset($arPost['class_name']))
        {
            throw new ArgumentNullException('class_name');
        }
        if (!isset($arPost['method_name']))
        {
            throw new ArgumentNullException('method_name');
        }
        if (!class_exists($arPost['class_name']))
        {
            throw new ClassNotFoundException($arPost['class_name']);
        }
        if (!method_exists($arPost['class_name'],$arPost['method_name']))
        {
            throw new MethodNotFoundException($arPost['class_name'],$arPost['method_name']);
        }
        $handler = $arPost['class_name'] . '::' . $arPost['method_name'];
        //TODO: Добавить проверку на интерфейс или базовый класс обработчика

        $arErrList = [];
        $arData = [
            'result' => 'success'
        ];
        try
        {
            $bOk = $handler($arPost, $arErrList);
        }
        catch (\Exception $e)
        {
            $bOk = false;
            $arErrList[] = 'Ошибка ['.$e->getCode().']: '.$e->getMessage();
        }
        if (!$bOk)
        {
            $arData['result'] = 'errors';
            if (!empty($arErrList))
            {
                $html = '<ul>';

                foreach ($arErrList as $err)
                {
                    $html .= '<li>' . $err . '</li>';
                }

                $html .= '</ul>';
                $arData['err_list'] = $arErrList;
                $arData['error_html'] = $html;
            }
        }

        return $arData;
    }

    public function checkFormDate ($arPost)
    {
        if (!isset($arPost['namespace']))
        {
            throw new ArgumentNullException('namespace');
        }
        if (!class_exists($arPost['namespace']))
        {
            throw new ClassNotFoundException($arPost['namespace']);
        }
        if (!isset($arPost['func']))
        {
            throw new ArgumentNullException('func');
        }
        if (!method_exists($arPost['namespace'],$arPost['func']))
        {
            throw new MethodNotFoundException($arPost['namespace'],$arPost['func']);
        }
        if (!isset($arPost['value']))
        {
            throw new ArgumentNullException('value');
        }
        $value = new Date($arPost['value'],'db');
        $arReturn = [
            'result' => 'success',
            'namespace' => $arPost['namespace'],
            'func' => $arPost['func'],
            'value' => $arPost['value'],
            'valueObj' => (string)$value
        ];

        $res = call_user_func([$arPost['namespace'],$arPost['func']],$value);
        if ($res === true)
        {
            return $arReturn;
        }
        else
        {
            $arReturn['result'] = 'error';
            $arReturn['err'] = $res;

            return $arReturn;
        }
    }

    public function checkFormField ($arPost)
    {
        $arReturn = [
            'result' => 'success'
        ];
        if (!isset($arPost['namespace']))
        {
            throw new ArgumentNullException('namespace');
        }
        if (!class_exists($arPost['namespace']))
        {
            throw new ClassNotFoundException('namespace');
        }
        $arReturn['namespace'] = $arPost['namespace'];
        if (!isset($arPost['func']))
        {
            throw new ArgumentNullException('func');
        }
        if (!method_exists($arPost['namespace'],$arPost['func']))
        {
            throw new MethodNotFoundException($arPost['namespace'],$arPost['func']);
        }
        if (!isset($arPost['value']))
        {
            throw new ArgumentNullException('value');
        }
        $arReturn['value'] = $arPost['value'];

        $res = call_user_func([$arPost['namespace'],$arPost['func']],$arPost['value']);
        if ($res === true)
        {
            return $arReturn;
        }
        else
        {
            $arReturn['result'] = 'error';
            $arReturn['err'] = $res;

            return $arReturn;
        }
    }

    public function checkFormMonth ($arPost)
    {
        $arReturn = [
            'result' => 'success'
        ];
        if (!isset($arPost['namespace']))
        {
            throw new ArgumentNullException('namespace');
        }
        if (!class_exists('namespace'))
        {
            throw new ClassNotFoundException($arPost['namespace']);
        }
        $arReturn['namespace'] = $arPost['namespace'];
        if (!isset($arPost['func']))
        {
            throw new ArgumentNullException('func');
        }
        if (!method_exists($arPost['namespace'],$arPost['func']))
        {
            throw new MethodNotFoundException($arPost['namespace'],$arPost['func']);
        }
        if (!isset($arPost['value']))
        {
            throw new ArgumentNullException('value');
        }
        $arReturn['value'] = $arPost['value'];
        $value = new Date($arPost['value'].'-01','db');
        $arReturn['valueObj'] = (string)$value;

        $res = call_user_func([$arPost['namespace'],$arPost['func']],$value);
        if ($res === true)
        {
            return $arReturn;
        }
        else
        {
            $arReturn['result'] = 'error';
            $arReturn['err'] = $res;

            return $arReturn;
        }
    }

    public function checkFormNumber ($arPost)
    {
        $arReturn = [
            'result' => 'success'
        ];
        if (!isset($arPost['namespace']))
        {
            throw new ArgumentNullException('namespace');
        }
        if (!class_exists($arPost['namespace']))
        {
            throw new ClassNotFoundException($arPost['namespace']);
        }
        $arReturn['namespace'] = $arPost['namespace'];
        if (!isset($arPost['func']))
        {
            throw new ArgumentNullException('func');
        }
        if (!method_exists($arPost['namespace'],$arPost['func']))
        {
            throw new MethodNotFoundException($arPost['namespace'],$arPost['func']);
        }
        $arReturn['func'] = $arPost['func'];
        if (!isset($arPost['value']))
        {
            throw new ArgumentNullException('value');
        }
        $arReturn['value'] = $arPost['value'];
        if (!isset($arPost['step']) || $arPost['step'] == '')
        {
            $arReturn['step'] = false;
        }
        if (!isset($arPost['min']) || $arPost['min'] == '')
        {
            $arReturn['min'] = false;
        }
        if (!isset($arPost['max']) || $arPost['max'] == '')
        {
            $arReturn['max'] = false;
        }

        $res = call_user_func(
            [$arReturn['namespace'],$arReturn['func']],
            $arReturn['value'],
            $arReturn['step'],
            $arReturn['min'],
            $arReturn['max']
        );
        if ($res === true)
        {
            return $arReturn;
        }
        else
        {
            $arReturn['result'] = 'error';
            $arReturn['err'] = $res;

            return $arReturn;
        }
    }
}
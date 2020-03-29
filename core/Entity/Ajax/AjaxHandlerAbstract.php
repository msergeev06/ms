<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */


namespace Ms\Core\Entity\Ajax;

use Ms\Core\Entity\System\Multiton;

/**
 * Абстрактный класс \Ms\Core\Entity\Ajax\ AjaxHandlerAbstract
 * Обработчик AJAX-запросов
 */
abstract class AjaxHandlerAbstract extends Multiton
{
    /**
     * @var \Ms\Core\Entity\Ajax\Encoder $encoder Объект декодера
     */
    protected $encoder;

    /**
     * Конструктор класса AjaxHandlerAbstract
     */
    protected function __construct ()
    {
        $this->encoder = new Encoder();

        parent::__construct();
    }

    /**
     * Возвращает объект декодера
     *
     * @return \Ms\Core\Entity\Ajax\Encoder
     */
    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * Обрабатывает запрос
     *
     * @param array $request Массив с данными запроса
     *
     * @return array
     */
    public function processRequest (array $request)
    {
        try
        {
            $that = self::getInstance();
            $action = $request['action'];

            if (empty($action))
            {
                throw new AjaxException('Action not set',__FILE__,__LINE__);
            }

            if (!method_exists($that, $action))
            {
                throw new AjaxException('Unknown action',__FILE__,__LINE__);
            }

            $data = &$request['data'];
            $data = $this->encoder->convertFromUTF($data);

            $data = call_user_func([$that, $action], $data);
            $success = true;
        }
        catch (\Exception $e)
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

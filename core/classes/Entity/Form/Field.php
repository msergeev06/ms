<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Form;

use Ms\Core\Entity\Errors\FileLogger;
use Ms\Core\Entity\System\Application;
use Ms\Core\Exceptions\IO\FileNotFoundException;

/**
 * Абстрактный класс Ms\Core\Entity\Form\FieldAbstract
 * Базовый класс полей веб-формы
 */
abstract class Field
{
    /**
     * @var mixed значение по-умолчанию для поля
     */
    protected $default_value = null;
    /**
     * @var array функция проверки значения поля
     */
    protected $functionCheck = [];
    /**
     * @var string подсказка для поля
     */
    protected $help = null;
    /** @var FileLogger */
    protected $logger = null;
    /**
     * @var string имя поля
     */
    protected $name = null;
    /**
     * @var bool флаг обязательного поля
     */
    protected $requiredValue = false;
    /**
     * @var string заголовок поля
     */
    protected $title = null;
    /**
     * @var string тип поля
     */
    protected $type = null;

    /**
     * Конструктор
     *
     * @param string $type
     * @param string $title
     * @param string $help
     * @param string $name
     * @param mixed  $default_value
     * @param bool   $requiredValue
     * @param array  $functionCheck
     */
    protected function __construct (
        $type = null, $title = null, $help = null, $name = null, $default_value = null, $requiredValue = false,
        $functionCheck = null
    ) {
        $this->type = $type;
        $this->title = $title;
        $this->help = $help;
        $this->name = $name;
        $this->default_value = $default_value;
        $this->requiredValue = $requiredValue;
        $this->functionCheck = $functionCheck;
        $this->logger = new FileLogger('core');
    }

    /**
     * Возвращает функцию проверки значения поля
     *
     * @return array
     * @unittest
     */
    public function check ()
    {
        if (is_null($this->functionCheck))
        {
            return [\Ms\Core\Lib\Form::class, 'checkAll'];
        }
        else
        {
            return $this->functionCheck;
        }
    }

    /**
     * Возвращает значение по-умолчанию для поля
     *
     * @return mixed
     * @unittest
     */
    public function getDefaultValue ()
    {
        return $this->default_value;
    }

    /**
     * Возвращает полученное имя функции проверяющей значение поля
     *
     * @return array
     * @unittest
     */
    public function getFunctionCheck ()
    {
        return $this->functionCheck;
    }

    /**
     * Возвращает имя поля
     *
     * @return string
     * @unittest
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * Возвращает заголовок поля
     *
     * @return string
     * @unittest
     */
    public function getTitle ()
    {
        return $this->title;
    }

    /**
     * Возвращает тип поля
     *
     * @return string
     * @unittest
     */
    public function getType ()
    {
        return $this->type;
    }

    /**
     * Возвращает html-код поля. Необходимо переопределить
     *
     * @param null $value
     */
    abstract public function showField ($value = null);

    /**
     * Возвращает путь к шаблонам полей формы
     *
     * @param $field
     *
     * @return string
     */
    final protected function getFormTemplatesPath ($field)
    {
        IncludeLangFile(__FILE__);

        $siteTemplate = Application::getInstance()->getSiteTemplate();
        $templRoot = Application::getInstance()->getSettings()->getTemplatesRoot();
        try
        {
            if (file_exists($templRoot . '/' . $siteTemplate . '/form/' . $field . '.tpl'))
            {
                return $templRoot . '/' . $siteTemplate . '/form/' . $field . '.tpl';
            }
            elseif (file_exists($templRoot . '/.default/form/' . $field . '.tpl'))
            {
                return $templRoot . '/.default/form/' . $field . '.tpl';
            }
            else
            {
                $this->logger->addMessage(
                    \GetCoreMessage(
                        'file_not_found_exception',
                        ['FILE' => $templRoot . '/.default/form/' . $field . '.tpl']
                    )
                );
                throw new FileNotFoundException($templRoot . '/.default/form/' . $field . '.tpl');
            }
        }
        catch (FileNotFoundException $e)
        {
            die($e->showException());
        }
    }
}
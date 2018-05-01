<?php
/**
 * Базовый класс полей веб-формы
 *
 * @package Ms\Core
 * @subpackage Entity\Form
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Form;

use Ms\Core\Entity\Application;
use Ms\Core\Exception\Io\FileNotFoundException;
use Ms\Core\Lib\Loc;
use Ms\Core\Lib\Logs;

Loc::includeLocFile(__FILE__);

/**
 * Class Field
 *
 * @package Ms\Core
 * @subpackage Entity\Form
 */
abstract class Field
{
	/**
	 * @var string тип поля
	 */
	protected $type = null;

	/**
	 * @var string заголовок поля
	 */
	protected $title=null;

	/**
	 * @var string подсказка для поля
	 */
	protected $help=null;

	/**
	 * @var string имя поля
	 */
	protected $name = null;

	/**
	 * @var mixed значение по-умолчанию для поля
	 */
	protected $default_value=null;

	/**
	 * @var bool флаг обязательного поля
	 */
	protected $requiredValue=false;

	/**
	 * @var array функция проверки значения поля
	 */
	protected $functionCheck=array();

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
	protected function __construct($type=null,$title=null,$help=null,$name=null,$default_value=null,$requiredValue=false,$functionCheck=null)
	{
		$this->type = $type;
		$this->title = $title;
		$this->help = $help;
		$this->name = $name;
		$this->default_value = $default_value;
		$this->requiredValue = $requiredValue;
		$this->functionCheck = $functionCheck;
	}

	/**
	 * Возвращает функцию проверки значения поля
	 *
	 * @return array
	 */
	public function check ()
	{
		if (is_null($this->functionCheck))
		{
			return array('Ms\Core\Lib\Form','checkAll');
		}
		else
		{
			return $this->functionCheck;
		}
	}

	/**
	 * Возвращает тип поля
	 *
	 * @return string
	 */
	public function getType ()
	{
		return $this->type;
	}

	/**
	 * Возвращает заголовок поля
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Возвращает имя поля
	 *
	 * @return string
	 */
	public function getName ()
	{
		return $this->name;
	}

	/**
	 * Возвращает значение по-умолчанию для поля
	 *
	 * @return mixed
	 */
	public function getDefaultValue ()
	{
		return $this->default_value;
	}

	/**
	 * Возвращает полученное имя функции проверяющей значение поля
	 *
	 * @return array
	 */
	public function getFunctionCheck ()
	{
		return $this->functionCheck;
	}

	/**
	 * Возвращает html-код поля. Необходимо переопределить
	 *
	 * @param null $value
	 */
	abstract public function showField ($value=null);

	/**
	 * Возвращает путь к шаблонам полей формы
	 *
	 * @param $field
	 *
	 * @return string
	 */
	final protected function getFormTemplatesPath ($field)
	{
		$siteTemplate = Application::getInstance()->getSiteTemplate();
		$templRoot = Application::getInstance()->getSettings()->getTemplatesRoot();
		try
		{
			if (file_exists($templRoot.'/'.$siteTemplate.'/form/'.$field.'.tpl'))
			{
				return $templRoot.'/'.$siteTemplate.'/form/'.$field.'.tpl';
			}
			elseif (file_exists($templRoot.'/.default/form/'.$field.'.tpl'))
			{
				return $templRoot.'/.default/form/'.$field.'.tpl';
			}
			else
			{
				Logs::setCritical(Loc::getCoreMessage('file_not_found_exception',array ('FILE'=>$templRoot.'/.default/form/'.$field.'.tpl')));
				throw new FileNotFoundException($templRoot.'/.default/form/'.$field.'.tpl');
			}
		}
		catch (FileNotFoundException $e)
		{
			die($e->showException());
		}
	}
}
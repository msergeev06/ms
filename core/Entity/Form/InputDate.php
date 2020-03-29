<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Form;

use Ms\Core\Entity\Application;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Lib\IO\Files;
use Ms\Core\Lib\Loc;

Loc::includeLocFile(__FILE__);

/**
 * Класс Ms\Core\Entity\Form\InputDate
 * Поле веб-формы input type="date"
 */
class InputDate extends Field
{
	/**
	 * @var Date
	 */
	protected $default_value=null;

	/**
	 * @var Date
	 */
	protected $min = null;

	/**
	 * @var Date
	 */
	protected $max = null;

	/**
	 * Конструктор
	 *
	 * @param string $title - заголовок поля
	 * @param string $help - подсказака для поля
	 * @param string $name - имя поля
	 * @param Date $default_value - значение поля
	 * @param Date $min - минимальная дата
	 * @param Date $max - максимальная дата
	 * @param bool $requiredValue - флаг обязательного поля
	 * @param null $functionCheck - функция обработки значения поля
	 */
	public function __construct ($title=null,$help=null,$name=null,Date $default_value=null,Date $min=null,Date $max=null,$requiredValue=false,$functionCheck=null)
	{
		parent::__construct('InputDate',$title,$help,$name,$default_value,$requiredValue,$functionCheck);
		if (is_null($min))
		{
			$this->min = new Date('0000-01-01 00:00:00', 'db_datetime');
		}
		else
		{
			$this->min = $min;
		}
		if (is_null($max))
		{
			$this->max = new Date('9999-12-31 23:59:59', 'db_datetime');
		}
		else
		{
			$this->max = $max;
		}
	}

	/**
	 * Возвращает html-код поля
	 *
	 * @param Date $value
	 *
	 * @return string
	 */
	public function showField($value=null)
	{
		$fieldTemplate = $this->getFormTemplatesPath('input_date');
		$template = Loc::getCoreMessage('no_template_exists');
		if ($fieldTemplate)
		{
			$template = Files::loadFile($fieldTemplate);
		}
		if (is_null($value))
		{
			$value = $this->default_value;
		}
		/*
		 * Поля шаблона:
		 * #FIELD_NAME# - имя поля
		 * #FIELD_TITLE# - имя поля (также можно добавить флаг обязательности заполнения)
		 * #INPUT_DATE# - input text (само поле)
		 * #ERROR_TEXT_FIELD_REQUIRED# - Текст ошибки: "Поле обязательно для заполнения"
		 * #FIELD_REQUIRED# - если поле обязательно, нужно передать 1
		 * #NAMESPACE# - класс обработчик
		 * #FUNCTION# - функция обработчик
		 * #PATH# - путь к обработчику
		 */
		$app = Application::getInstance();
		list ($namespace, $func) = $this->check();
		$path = $app->getSitePath($app->getSettings()->getCoreRoot().'/tools/check_form_date.php');
		$title = $this->title;
		if ($this->requiredValue)
		{
			$title .= '<span class="field_required" style="color:red;font-size: 20px;">*</span>';
			$template = str_replace('#FIELD_REQUIRED#','1',$template);
		}
		else
		{
			$template = str_replace('#FIELD_REQUIRED#','',$template);
		}

		$template = str_replace('#FIELD_NAME#',$this->name,$template);
		$template = str_replace('#FIELD_TITLE#',$title,$template);
		$template = str_replace('#INPUT_DATE#',\InputDate($this->name,$this->default_value,$this->min,$this->max,'class="'.strtolower($this->name).' form-control"'),$template);
		if (is_null($this->help))
		{
			$help = '';
		}
		else
		{
			$help = $this->help;
		}
		$template = str_replace('#FIELD_HELP#',$help,$template);
		$template = str_replace('#ERROR_TEXT_FIELD_REQUIRED#',Loc::getCoreMessage('field_required'),$template);
		$template = str_replace('#NAMESPACE#',str_replace('\\','\\\\',$namespace),$template);
		$template = str_replace('#FUNCTION#',$func,$template);
		$template = str_replace('#PATH#',$path,$template);

		return $template;
	}

	/**
	 * Возвращает функцию обработчик значения поля
	 *
	 * @return array
	 */
	public function check ()
	{
		if (is_null($this->functionCheck))
		{
			return array('Ms\Core\Lib\Form','checkInputDate');
		}
		else
		{
			return $this->functionCheck;
		}
	}
}
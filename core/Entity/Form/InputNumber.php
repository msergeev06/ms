<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Form;

use Ms\Core\Entity\Application;
use Ms\Core\Lib\IO\Files;
use Ms\Core\Lib\Loc;

Loc::includeLocFile(__FILE__);

/**
 * Класс Ms\Core\Entity\Form\InputNumber
 * Поле веб-формы input type="number"
 */
class InputNumber extends Field
{
	/**
	 * @var bool|float|int
	 */
	protected $min=false;

	/**
	 * @var bool|float|int
	 */
	protected $max=false;

	/**
	 * @var bool|float|int
	 */
	protected $step=false;

	/**
	 * Конструктор
	 *
	 * @param string $title - заголовок поля
	 * @param string $help - подсказка для поля
	 * @param string $name - имя поля
	 * @param int|float $default_value - значение поля
	 * @param bool $requiredValue - флаг обязательного поля
	 * @param bool|float|int $step - шаг значения
	 * @param bool|float|int $min - минимальное значение
	 * @param bool|float|int $max - максимальное значение
	 * @param array $functionCheck - функция проверки значения поля
	 */
	public function __construct ($title=null,$help=null,$name=null,$default_value=null,$requiredValue=false,$step=false,$min=false,$max=false,$functionCheck=null)
	{
		parent::__construct('InputNumber',$title,$help,$name,$default_value,$requiredValue,$functionCheck);
		$this->min = $min;
		$this->max = $max;
		$this->step = $step;
	}

	/**
	 * Возвращает минимальное значение поля
	 *
	 * @return bool|float|int
	 */
	public function getMin ()
	{
		return $this->min;
	}

	/**
	 * Возвращает максимальное значение поля
	 *
	 * @return bool|float|int
	 */
	public function getMax ()
	{
		return $this->max;
	}

	/**
	 * Возвращает шаг значения поля
	 *
	 * @return bool|float|int
	 */
	public function getStep()
	{
		return $this->step;
	}

	/**
	 * Возвращает html-код поля
	 *
	 * @param float|int $value
	 *
	 * @return mixed|string
	 */
	public function showField($value=null)
	{
		$fieldTemplate = $this->getFormTemplatesPath('input_number');
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
		 * #INPUT_TEXT# - input text (само поле)
		 * $FIELD_HELP# - подсказка к полю
		 * #ERROR_TEXT_FIELD_REQUIRED# - Текст ошибки: "Поле обязательно для заполнения"
		 * #FIELD_REQUIRED# - если поле обязательно, нужно передать 1
		 * #STEP# - чему равен 1 шаг
		 * #MIN# - минимальное значение
		 * #MAX# - максимальное значение
		 * #NAMESPACE# - класс обработчик
		 * #FUNCTION# - функция обработчик
		 * #PATH# - путь к обработчику
		 */
		$app = Application::getInstance();
		list ($namespace, $func) = $this->check();
		$path = $app->getSitePath($app->getSettings()->getCoreRoot().'/tools/check_form_number.php');
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
		$template = str_replace('#INPUT_NUMBER#',\InputNumber($this->name,$value,$this->min,$this->max,$this->step,'class="'.strtolower($this->name).' form-control"'),$template);
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
		if ($this->step !== false)
		{
			$step = $this->step;
		}
		else
		{
			$step = '';
		}
		if ($this->min !== false)
		{
			$min = $this->min;
		}
		else
		{
			$min = '';
		}
		if ($this->max !== false)
		{
			$max = $this->max;
		}
		else
		{
			$max = '';
		}
		$template = str_replace('#STEP#',$step,$template);
		$template = str_replace('#MIN#',$min,$template);
		$template = str_replace('#MAX#',$max,$template);
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
			return array('Ms\Core\Lib\Form','checkInputNumber');
		}
		else
		{
			return $this->functionCheck;
		}
	}
}
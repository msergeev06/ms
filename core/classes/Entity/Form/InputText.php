<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Form;

use Ms\Core\Entity\System\Application;
use Ms\Core\Lib\IO\Files;
use Ms\Core\Lib\Loc;

Loc::includeLocFile(__FILE__);

/**
 * Класс Ms\Core\Entity\Form\InputText
 * Поле веб-формы input type="text"
 */
class InputText extends Field
{
	/**
	 * Конструктор
	 *
	 * @param string $title - заголовок поля
	 * @param string $help - подсказка для поля
	 * @param string $name - имя поля
	 * @param string $default_value - значение поля
	 * @param bool   $requiredValue - флаг обязательного поля
	 * @param array  $functionCheck - функция обработки значения поля
	 */
	public function __construct ($title=null,$help=null,$name=null,$default_value=null,$requiredValue=false,$functionCheck=null)
	{
		parent::__construct('InputText',$title,$help,$name,$default_value,$requiredValue,$functionCheck);
	}

	/**
	 * Возвращает html-код поля
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function showField ($value=null)
	{
		$fieldTemplate = $this->getFormTemplatesPath('input_text');
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
		 * #ERROR_TEXT_FIELD_REQUIRED# - Текст ошибки: "Поле обязательно для заполнения"
		 * #FIELD_REQUIRED# - если поле обязательно, нужно передать 1
		 * #NAMESPACE# - класс обработчик
		 * #FUNCTION# - функция обработчик
		 * #PATH# - путь к обработчику
		 */
		$app = Application::getInstance();
		list ($namespace, $func) = $this->check();
		$path = $app->getSitePath($app->getSettings()->getCoreRoot().'/tools/check_form_field.php');
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
		$template = str_replace('#INPUT_TEXT#',\InputText($this->name,$value,'class="'.strtolower($this->name).' form-control"'),$template);
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
			return array('Ms\Core\Lib\Form','checkInputText');
		}
		else
		{
			return $this->functionCheck;
		}
	}
}
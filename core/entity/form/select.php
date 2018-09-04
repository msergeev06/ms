<?php
/**
 * Поле веб-формы select
 *
 * @package Ms\Core
 * @subpackage Entity\Form
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Form;

use Ms\Core\Entity\Application;
use Ms\Core\Exception\ArgumentTypeException;
use Ms\Core\Lib\IO\Files;
use Ms\Core\Lib\Loc;

Loc::includeLocFile(__FILE__);

/**
 * Class Select
 *
 * @package Ms\Core
 * @subpackage Entity\Form
 */
class Select extends Field
{
	/**
	 * Значения списка
	 * @var array
	 */
	protected $arValues = null;

	/**
	 * Текст при выбранном пустом значении
	 * @var string
	 */
	protected $textSelect="";

	/**
	 * Конструктор
	 *
	 * @param string $title - заголовок поля
	 * @param string $help - подсказка для поля
	 * @param string $name - имя поля
	 * @param mixed  $default_value - значение поля
	 * @param bool   $requiredValue - флаг обязательного поля
	 * @param string $textSelect - текст пустого значения
	 * @param array  $arValues - массив значений списка, где VALUE - значение, NAME - подпись
	 * @param array  $functionCheck - функция проверки значения поля
	 */
	public function __construct ($title=null,$help=null,$name=null,$default_value=null,$requiredValue=false,$textSelect="",$arValues=null,$functionCheck=null)
	{
		parent::__construct('Select',$title,$help,$name,$default_value,$requiredValue,$functionCheck);
		$this->arValues = $arValues;
		$this->textSelect = $textSelect;
	}

	/**
	 * Возвращает список значений поля
	 *
	 * @return array|null
	 */
	public function getArValues ()
	{
		return $this->arValues;
	}

	/**
	 * Возвращает html-код поля
	 *
	 * @param mixed $value
	 *
	 * @return mixed|string
	 * @throws ArgumentTypeException
	 */
	public function showField ($value=null)
	{
		$fieldTemplate = $this->getFormTemplatesPath('select');
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
		 * #SELECT_BOX# - <select> (само поле)
		 * #FIELD_REQUIRED# - флаг обязательного поля, если обязательное нужно поставить 1, иначе 0
		 * #ERROR_TEXT_FIELD_REQUIRED# - текст ошибки "Поле обязательно для заполнения"
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
			$template = str_replace('#FIELD_REQUIRED#',1,$template);
		}
		else
		{
			$template = str_replace('#FIELD_REQUIRED#',0,$template);
		}

		$template = str_replace('#ERROR_TEXT_FIELD_REQUIRED#',Loc::getCoreMessage('field_required'),$template);
		$template = str_replace('#FIELD_NAME#',$this->name,$template);
		$template = str_replace('#FIELD_TITLE#',$title,$template);
		$template = str_replace('#SELECT_BOX#',\SelectBox($this->name,$this->arValues,$this->textSelect,$value,'class="'.strtolower($this->name).' form-control"'),$template);
		if (is_null($this->help))
		{
			$help = '';
		}
		else
		{
			$help = $this->help;
		}
		$template = str_replace('#FIELD_HELP#',$help,$template);
		$template = str_replace('#NAMESPACE#',str_replace('\\','\\\\',$namespace),$template);
		$template = str_replace('#FUNCTION#',$func,$template);
		$template = str_replace('#PATH#',$path,$template);

		return $template;
	}
}
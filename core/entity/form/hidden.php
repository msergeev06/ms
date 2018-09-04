<?php
/**
 * Поле веб-формы input type="hidden"
 *
 * @package Ms\Core
 * @subpackage Entity\Form
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Form;

use Ms\Core\Lib\IO\Files;
use Ms\Core\Lib\Loc;

Loc::includeLocFile(__FILE__);

/**
 * Class Hidden
 *
 * @package Ms\Core
 * @subpackage Entity\Form
 */
class Hidden extends Field
{
	/**
	 * Конструктор
	 *
	 * @param string            $name - имя поля
	 * @param string|int|float  $default_value - значение поля
	 */
	public function __construct ($name=null,$default_value=null)
	{
		parent::__construct('Hidden','','',$name,$default_value,false);
	}

	/**
	 * Возвращает html-код поля
	 *
	 * @param string|int|float $value
	 *
	 * @return string
	 */
	public function showField ($value=null)
	{
		$fieldTemplate = $this->getFormTemplatesPath('hidden');
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
		 * #FIELD_VALUE# - значение поля
		 */
		$template = str_replace('#FIELD_NAME#',$this->name,$template);
		$template = str_replace('#FIELD_VALUE#',$value,$template);

		return $template;
	}
}
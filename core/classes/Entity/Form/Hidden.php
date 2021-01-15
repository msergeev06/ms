<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Form;

use Ms\Core\Lib\IO\Files;

/**
 * Класс Ms\Core\Entity\Form\Hidden
 * Поле веб-формы input type="hidden"
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
        \IncludeLangFile(__FILE__);

		$fieldTemplate = $this->getFormTemplatesPath('hidden');
		$template = \GetCoreMessage('no_template_exists');
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
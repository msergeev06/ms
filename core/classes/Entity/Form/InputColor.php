<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Form;

/**
 * Класс Ms\Core\Entity\Form\InputColor
 * Поле веб-формы input type="color"
 */
class InputColor extends Field
{
	/**
	 * Конструктор
	 *
	 * @param string $title
	 * @param string $name
	 * @param string $default_value
	 * @param bool $requiredValue
	 * @param array $functionCheck
	 */
	public function __construct ($title=null,$name=null,$default_value=null,$requiredValue=false,$functionCheck=null)
	{
		parent::__construct('InputColor',$title,$name,$default_value,$requiredValue,$functionCheck);
	}

	/**
	 * Возвращает html-код поля
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function showField($value=null)
	{
		return '';
	}
}
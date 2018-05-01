<?php
/**
 * Поле веб-формы input type="color"
 *
 * @package Ms\Core
 * @subpackage Entity\Form
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Form;

/**
 * Class InputColor
 *
 * @package Ms\Core
 * @subpackage Entity\Form
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
<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Form;

/**
 * Класс Ms\Core\Entity\Form\InputSearch
 * Поле веб-формы input type="search"
 */
class InputSearch extends Field
{
	public function __construct ($title=null,$name=null,$default_value=null,$requiredValue=false,$functionCheck=null)
	{
		parent::__construct('InputSearch',$title,$name,$default_value,$requiredValue,$functionCheck);
	}

	public function showField($value=null)
	{

	}
}
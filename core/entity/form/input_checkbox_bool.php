<?php
/**
 * Поле веб-формы input type="checkbox" для типа bool
 *
 * @package Ms\Core
 * @subpackage Entity\Form
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Form;

/**
 * Class InputCheckboxBool
 *
 * @package Ms\Core
 * @subpackage Entity\Form
 */
class InputCheckboxBool extends Field
{
	public function __construct ($title=null,$name=null,$default_value=null,$requiredValue=false,$functionCheck=null)
	{
		parent::__construct('InputCheckboxBool',$title,$name,$default_value,$requiredValue,$functionCheck);
	}

	public function showField($value=null)
	{

	}
}
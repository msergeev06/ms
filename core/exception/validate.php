<?php
/**
 * Ms\Core\Exception\ValidateException
 * Класс исключений, связанных с валидацией полученных значений перед добавлением или изменением данных в БД
 *
 * @package Ms\Core
 * @subpackage Exception
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2019 Mikhail Sergeev
 */

namespace Ms\Core\Exception;

class ValidateException extends ArgumentException
{
	protected $sFieldName = null;
	protected $mValue = null;
	protected $arAllowedValues = [];

	public function __construct ($message, $sFieldName, $mValue=null, $arAllowedValues=[], \Exception $previous = null)
	{
		$message = 'Ошибка! Неверное значение поля "'.$sFieldName.'": '.$message;
		$this->sFieldName = $sFieldName;
		$this->mValue = $mValue;
		$this->arAllowedValues = $arAllowedValues;

		parent::__construct($message, $sFieldName, $previous);
	}

	public function getParameter ()
	{
		return $this->sFieldName;
	}

	public function getFieldName ()
	{
		return $this->sFieldName;
	}

	public function getValue ()
	{
		return $this->mValue;
	}

	public function getAllowedValues ()
	{
		return $this->arAllowedValues;
	}
}
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

use Ms\Core\Lib\Errors;

class ValidateException extends ArgumentException
{
	protected $sFieldName = null;
	protected $mValue = null;
	protected $arAllowedValues = [];

	public function __construct ($sFieldName, $mValue=null, $arAllowedValues=[], $message='', $code=Errors::VALIDATION_EXCEPTION, $file='', $line='', \Exception $previous = null)
	{
		$this->sFieldName = $sFieldName;
		$this->mValue = $mValue;
		$this->arAllowedValues = $arAllowedValues;

		if (array_key_exists('min',$this->arAllowedValues) && array_key_exists('max',$this->arAllowedValues))
		{
			$exceptionMessage = 'Значение ['.$mValue.'] не входит в допустимый диапазон значений (от '.$arAllowedValues['min'].' до '.$arAllowedValues['max'].'): '.$message;
			parent::__construct($exceptionMessage, $sFieldName, $code, $file, $line, $previous);
		}
		elseif (array_key_exists('min',$this->arAllowedValues))
		{
			$exceptionMessage = 'Значение ['.$mValue.'] не входит в допустимый диапазон значений (от '.$arAllowedValues['min'].'): '.$message;
			parent::__construct($exceptionMessage, $sFieldName, $code, $file, $line, $previous);
		}
		elseif (array_key_exists('max',$this->arAllowedValues))
		{
			$exceptionMessage = 'Значение ['.$mValue.'] не входит в допустимый диапазон значений (до '.$arAllowedValues['max'].'): '.$message;
			parent::__construct($exceptionMessage, $sFieldName, $code, $file, $line, $previous);
		}
		elseif (!is_null($arAllowedValues) && !empty($arAllowedValues))
		{
			$exceptionMessage = 'Значение ['.$mValue.'] не совпадает с возможными вариантами значений ('.implode (', ',$arAllowedValues).'): '.$message;
			parent::__construct($exceptionMessage, $sFieldName, $code, $file, $line, $previous);
		}
		else
		{
			parent::__construct($message, $sFieldName, $code, $file, $line, $previous);
		}
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
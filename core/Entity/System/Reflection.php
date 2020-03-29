<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

/**
 * Абстрактный класс Ms\Core\Entity\System\Reflection
 * Служит для интроспекции других классов
 */
abstract class Reflection
{
	/**
	 * @var null|\ReflectionClass
	 */
	protected $reflectionClass = null;

	public function getConstants ()
	{
		if (!$this->checkReflectionClass())
		{
			return [];
		}

		return $this->reflectionClass->getConstants();
	}

	public function getConstValuesList (string $prefix = null)
	{
		if (!$this->checkReflectionClass())
		{
			return false;
		}

		$arTemp = $this->getConstants();
		$arConstants = [];
		if (!empty($arTemp))
		{
			foreach ($arTemp as $constName => $constValue)
			{
				if (is_null($prefix) || $prefix == '')
				{
					$arConstants[] = $constValue;
				}
				else
				{
					if (substr($constName, 0, strlen($prefix)) == $prefix)
					{
						$arConstants[] = $constValue;
					}
				}
			}
		}

		return $arConstants;
	}

	protected function checkReflectionClass ()
	{
		if (is_null($this->reflectionClass))
		{
			try
			{
				$this->reflectionClass = new \ReflectionClass(get_called_class());
			}
			catch (\ReflectionException $e)
			{
				$this->reflectionClass = null;
			}
		}
		if (!is_null($this->reflectionClass))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
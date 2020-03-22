<?php

namespace Ms\Core\Entity;

use Ms\Core\Exception\ArgumentException;
use Ms\Core\Lib\Tools;

class ComponentPaths
{
	/** @var string */
	private $componentBrand=null;

	/** @var string */
	private $componentName=null;

	/** @var string */
	private $condition=null;

	/** @var string */
	private $rule=null;

	/** @var string */
	private $path=null;

	/**
	 * ComponentPaths constructor.
	 *
	 * @param string $component Название компонента в формате бренд:имя
	 *
	 * @throws ArgumentException
	 */
	public function __construct ($component)
	{
		if (strpos($component,':') !== false)
		{
			list ($this->componentBrand,$this->componentName) = explode(':',$component);
		}
		else
		{
			throw new ArgumentException('Необходимо указать имя компонента в формате бренд:имя','$component');
		}
		$this->path = Tools::getCurPath();

		return $this;
	}

	//<editor-fold defaultstate="collapse" desc=">>> Getters and Setters">
	/**
	 * Возвращает бренд компонента
	 *
	 * @return string
	 */
	public function getComponentBrand ()
	{
		return $this->componentBrand;
	}

	/**
	 * Возвращает имя компонента
	 *
	 * @return string
	 */
	public function getComponentName ()
	{
		return $this->componentName;
	}

	/**
	 * Возвращает полное имя компонента в формате бренд:имя
	 *
	 * @return string
	 */
	public function getComponentFullName ()
	{
		return $this->componentBrand . ':' . $this->componentName;
	}

	/**
	 * Возвращает условие использования пути
	 *
	 * @return string
	 */
	public function getCondition ()
	{
		return $this->condition;
	}

	/**
	 * Устанавливает условие использования пути
	 *
	 * @param string $condition
	 *
	 * @return ComponentPaths
	 */
	public function setCondition ($condition)
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * Возвращает правило использования пути
	 *
	 * @return string
	 */
	public function getRule ()
	{
		return $this->rule;
	}

	/**
	 * Устанавливает правило использования пути
	 *
	 * @param string $rule
	 *
	 * @return ComponentPaths
	 */
	public function setRule ($rule)
	{
		$this->rule = $rule;

		return $this;
	}

	/**
	 * Возвращает путь
	 *
	 * @return string
	 */
	public function getPath ()
	{
		return $this->path;
	}

	/**
	 * Устанавливает путь
	 *
	 * @param string $path
	 *
	 * @return ComponentPaths
	 */
	public function setPath ($path)
	{
		$this->path = $path;

		return $this;
	}
	//</editor-fold>

	public function checkObject ()
	{
		return (
			!is_null($this->componentBrand)
			&& !is_null($this->componentName)
			&& !is_null($this->condition)
			&& !is_null($this->rule)
			&& !is_null($this->path)
		);
	}
}
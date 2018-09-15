<?php

namespace Ms\Core\Entity;

class Breadcrumbs
{
	protected static $instance=null;

	private $arBreadcrumbs=array();

	private function __construct (){}

	public static function getInstance()
	{
		if (is_null(static::$instance))
		{
			static::$instance = new static();
		}

		return static::$instance;
	}

	public static function onEndBufferPageHandler (&$arParams)
	{
		$path = Application::getInstance()->getAppParam('breadcrumbs_path');
		Application::getInstance()->startBufferContent('breadcrumbs_component');
		if (file_exists($path))
		{
			include($path);
		}
		Application::getInstance()->cleanBufferContent('breadcrumbs_component');
	}

	public function addNavChain ($sTitle, $sUrl=null, $sName=null)
	{
		$sUrl = Application::getInstance()->getSitePath($sUrl);

		$this->arBreadcrumbs[] = array (
			'TITLE' => $sTitle,
			'URL' => $sUrl,
			'NAME' => $sName
		);
	}

	public function deleteNavChainByIndex ($index)
	{
		$index = (int)$index;

		if (isset($this->arBreadcrumbs[$index]))
		{
			unset($this->arBreadcrumbs[$index]);
			return true;
		}

		return false;
	}

	public function deleteNavChainByName ($sName)
	{
		$bSuccess = false;

		if (!empty($this->arBreadcrumbs))
		{
			foreach ($this->arBreadcrumbs as $key=>$value)
			{
				if ($value['NAME']==$sName)
				{
					unset($this->arBreadcrumbs[$key]);
					$bSuccess = true;
				}
			}
		}

		return $bSuccess;
	}

	public function getIndexByName ($sName)
	{
		if (!empty($this->arBreadcrumbs))
		{
			foreach ($this->arBreadcrumbs as $key=>$value)
			{
				if ($value['NAME']==$sName)
				{
					return (int)$key;
				}
			}
		}

		return false;
	}

	public function changeChainByIndex ($index, $sTitle=null, $sUrl=null, $sName=null)
	{
		$index = (int)$index;
		$bSuccess = false;
		if (isset($this->arBreadcrumbs[$index]))
		{
			if (!is_null($sTitle))
			{
				$this->arBreadcrumbs[$index]['TITLE'] = $sTitle;
				$bSuccess = true;
			}
			if (!is_null($sUrl))
			{
				$this->arBreadcrumbs[$index]['URL'] = Application::getInstance()->getSitePath($sUrl);
				$bSuccess = true;
			}
			if (!is_null($sName))
			{
				$this->arBreadcrumbs[$index]['NAME'] = $sName;
				$bSuccess = true;
			}
		}

		return $bSuccess;
	}

	public function getNavArray()
	{
		return $this->arBreadcrumbs;
	}
}
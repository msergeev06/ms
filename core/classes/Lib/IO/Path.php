<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 */

namespace Ms\Core\Lib\IO;

use Ms\Core\Lib;
use Ms\Core\Exceptions\IO\InvalidPathException;

/**
 * Класс Ms\Core\Lib\IO\Path
 * Методы для обработки путей
 */
class Path
{
	const DIRECTORY_SEPARATOR = '/';
	const DIRECTORY_SEPARATOR_PATTERN = '\/';
	const DIRECTORY_SEPARATOR_ALT = '\\';
	const DIRECTORY_SEPARATOR_ALT_PATTERN = '\\\\';
	const PATH_SEPARATOR = PATH_SEPARATOR;

	const INVALID_FILENAME_CHARS = "\\/:*?\"'<>|~#&;";

	protected static $physicalEncoding = "";
	protected static $logicalEncoding = "";

	protected static $directoryIndex = null;

	public static function getSeparator ()
	{
		if(Lib\Tools::isWindowsOs())
		{
			//windows
			return self::DIRECTORY_SEPARATOR_ALT;
		}
		else
		{
			//unix
			return self::DIRECTORY_SEPARATOR;
		}
	}

	public static function getSeparatorPattern ()
	{
		if(Lib\Tools::isWindowsOs())
		{
			//windows
			return self::DIRECTORY_SEPARATOR_ALT_PATTERN;
		}
		else
		{
			//unix
			return self::DIRECTORY_SEPARATOR_PATTERN;
		}
	}

	/**
	 * @param $path
	 *
	 * @return null|string|string[]
	 * @throws InvalidPathException
	 */
	public static function normalize($path)
	{
		if (!is_string($path) || ($path == ""))
			return null;

		//slashes doesn't matter for Windows
		static $pattern = null, $tailPattern;
		if (!$pattern)
		{
			if (Lib\Tools::isWindowsOs())
			{
				//windows
				$pattern = "'[\\\\/]+'";
				$tailPattern = "\0.\\/+ ";
			}
			else
			{
				//unix
				$pattern = "'[/]+'";
				$tailPattern = "\0/";
			}
		}
		$pathTmp = preg_replace($pattern, "/", $path);

		if (strpos($pathTmp, "\0") !== false)
			throw new InvalidPathException($path);

		if (preg_match("#(^|/)(\\.|\\.\\.)(/|\$)#", $pathTmp))
		{
			$arPathTmp = explode('/', $pathTmp);
			$arPathStack = array();
			foreach ($arPathTmp as $i => $pathPart)
			{
				if ($pathPart === '.')
					continue;

				if ($pathPart === "..")
				{
					if (array_pop($arPathStack) === null)
						throw new InvalidPathException($path);
				}
				else
				{
					array_push($arPathStack, $pathPart);
				}
			}
			$pathTmp = implode("/", $arPathStack);
		}

		$pathTmp = rtrim($pathTmp, $tailPattern);

		if (substr($path, 0, 1) === "/" && substr($pathTmp, 0, 1) !== "/")
			$pathTmp = "/".$pathTmp;

		if ($pathTmp === '')
			$pathTmp = "/";

		return $pathTmp;
	}

	public static function getName($path)
	{
		//$path = self::normalize($path);

		$p = Lib\Text\UtfSafeString::getLastPosition($path, self::DIRECTORY_SEPARATOR);
		if ($p !== false)
			return substr($path, $p + 1);

		return $path;
	}

	public static function getDirectory($path)
	{
		return substr($path, 0, -strlen(self::getName($path)) - 1);
	}


}
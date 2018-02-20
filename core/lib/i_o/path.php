<?php

namespace MSergeev\Core\Lib\IO;

use MSergeev\Core\Lib;
use MSergeev\Core\Exception\Io\InvalidPathException;

class Path
{
	const DIRECTORY_SEPARATOR = '/';
	const DIRECTORY_SEPARATOR_ALT = '\\';
	const PATH_SEPARATOR = PATH_SEPARATOR;

	const INVALID_FILENAME_CHARS = "\\/:*?\"'<>|~#&;";

	protected static $physicalEncoding = "";
	protected static $logicalEncoding = "";

	protected static $directoryIndex = null;

	public static function normalize($path)
	{
		if (!is_string($path) || ($path == ""))
			return null;

		//slashes doesn't matter for Windows
		static $pattern = null, $tailPattern;
		if (!$pattern)
		{
			if(strncasecmp(PHP_OS, "WIN", 3) == 0)
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
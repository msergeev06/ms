<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 */

namespace Ms\Core\Lib\IO;

use Ms\Core\Entity;
use Ms\Core\Lib\Tools;
use Ms\Core\Entity\System\Application;

/**
 * Класс Ms\Core\Lib\IO\Files
 * Работа с файловой системой
 */
class Files
{
	/**
	 * @var \Ms\Core\Entity\Errors\ErrorCollection
	 */
	protected static $errors;

	/**
	 * Возвращает права доступа к директории
	 *
	 * @return int
	 */
	public static function getDirChmod ()
	{

		$dirChmod = Application::getInstance()->getSettings()->getChmodDir();
		if (!$dirChmod)
		{
			$dirChmod = 0755;
		}

		return $dirChmod;
	}

	/**
	 * Возвращает права доступа к файлу
	 *
	 * @return int
	 */
	public static function getFileChmod ()
	{
		$fileChmod = Application::getInstance()->getSettings()->getChmodFile();
		if (!$fileChmod)
		{
			$fileChmod = 0644;
		}

		return $fileChmod;
	}

	/**
	 * Создает указанную директорию
	 *
	 * Если в пути оказываются несуществующие родительские директории - они также создаются
	 *
	 * @param string $path
	 */
	public static function createDir ($path)
	{
		$arPath = explode ('/',$path);
		for ($i=1; $i<count($arPath); $i++)
		{
			$tmpPath = '';
			for ($j=1; $j<=$i; $j++) {
				$tmpPath .= '/'.$arPath[$j];
			}
			if (!file_exists($tmpPath))
			{
				try
				{
					mkdir($tmpPath, self::getDirChmod());
				}
				catch (\Exception $e)
				{
					static::$errors['NOT_CREATE_DIRS'] = 'Error not create dirs: '.$tmpPath;
				}
			}
		}
	}

	/**
	 * Записывает информацию в файл и устанавливает права доступа к файлу
	 *
	 * Использует функцию file_put_contents для записи данных в файл
	 * @link http://php.net/manual/ru/function.file-put-contents.php
	 *
	 * @param string $filename
	 * @param string $data
	 *
	 * @return int
	 */
	public static function saveFile ($filename, $data)
	{
		$res = file_put_contents($filename, $data);
		@chmod($filename, self::getFileChmod());

		return $res;

	}

	/**
	 * Возвращает загруженную из файла информацию
	 *
	 * Использует функцию file_get_contents
	 * @link http://php.net/manual/ru/function.file-get-contents.php
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public static function loadFile ($filename)
	{
		return file_get_contents($filename);
	}

	/**
	 * Возвращает список файлов в указанной директории
	 *
	 * Игнорирует поддиректории, а также '.' и '..'
	 *
	 * @param string $dir       путь к директории
	 * @param array  $arIgnore  массив игнорируемых файлов
	 *
	 * @return array|bool
	 */
	public static function getListFiles ($dir, $arIgnore=array())
	{
		if (!file_exists($dir))
		{
			return false;
		}
		$arList = array();
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if (
					    !is_dir($dir.'/'.$file)
                        && $file != '.'
                        && $file != '..'
                        && !in_array($file,$arIgnore)
                    ) {
						$arList[] = $file;
					}
				}
				closedir($dh);
			}
		}

		if (!empty($arList))
		{
			return $arList;
		}

		return false;
	}

	/**
	 * Возвращает массив, содержащий дерево подкаталогов и файлов, начиная с указанного каталога
	 *
	 * @param string $destination Начальный каталог
	 * @param string $sort        Сортировка
	 *
	 * @return array
	 * @since 0.2.0
	 */
	public static function getFilesTree($destination, $sort = 'name')
	{
		if (substr($destination, -1) == '/' || substr($destination, -1) == '\\')
		{
			$destination = substr($destination, 0, strlen($destination) - 1);
		}

		$res = array();

		if (!is_dir($destination))
			return $res;

		if ($dir = @opendir($destination))
		{
			while (($file = readdir($dir)) !== false)
			{
				if (is_dir($destination . "/" . $file) && ($file != '.') && ($file != '..'))
				{
					$tmp = static::getFilesTree($destination . "/" . $file);
					if (is_array($tmp))
					{
						foreach ($tmp as $elem)
						{
							$res[] = $elem;
						}
					}
				}
				elseif (is_file($destination . "/" . $file))
				{
					$res[] = ($destination . "/" . $file);
				}
			}
			closedir($dir);
		}

		if ($sort == 'name')
		{
			sort($res, SORT_STRING);
		}

		return $res;
	}

	/**
	 * Проверяет указанный путь. Если не существует, создает нужные директории
	 *
	 * @param string $sPath         Путь
	 * @param bool   $bPermission   Проверять права
	 */
	public static function checkDirPath ($sPath, $bPermission=true)
	{
		$badDirs=Array();
		$path = str_replace(array("\\", "//"), "/", $sPath);
		$dirPerm = Entity\Application::getInstance()->getSettings()->getChmodDir();

		if($path[strlen($path)-1]!="/") //отрежем имя файла
		{
			$p=Tools::ms_strrpos ($path, "/");
			$path = substr($path, 0, $p);
		}

		while(strlen($path)>1 && $path[strlen($path)-1]=="/") //отрежем / в конце, если есть
		{
			$path=substr($path, 0, strlen($path)-1);
		}

		$p=Tools::ms_strrpos ($path, "/");
		while($p>0)
		{
			if(file_exists($path) && is_dir($path))
			{
				if($bPermission)
				{
					if(!is_writable($path))
					{
						@chmod($path, $dirPerm);
					}
				}
				break;
			}
			$badDirs[]=substr($path, $p+1);
			$path = substr($path, 0, $p);
			$p=Tools::ms_strrpos ($path, "/");
		}

		for($i=count($badDirs)-1; $i>=0; $i--)
		{
			$path = $path."/".$badDirs[$i];
			if(!is_dir($path))
			{
				mkdir($path, $dirPerm);
			}
		}
	}

	/**
	 * Копирует файл или директорию со всеми файлами из одного расположения в другое
	 *
	 * @param string $sPathFrom         Путь к файлу или директории (что копируется)
	 * @param string $sPathTo           Путь к файлу или директории (куда копируется)
	 * @param bool   $bRewrite          Флаг, обозначающий, перезапись существуюших файлов
	 * @param bool   $bRecursive        Флаг, обозначающий рекурсивное копирование файлов из поддиректорий
	 * @param bool   $bDeleteAfterCopy  Флаг, обозначающий удаление исходных файлов после копирования
	 * @param string $sExclude          Строка, которая будет исключена из пути, перед копированием
	 *
	 * @return bool Возвращает TRUE, если копирование успешно
	 */
	public static function copyDirFiles ($sPathFrom, $sPathTo, $bRewrite = TRUE, $bRecursive = FALSE, $bDeleteAfterCopy = FALSE, $sExclude = "")
	{
		$filePerm = Entity\Application::getInstance()->getSettings()->getChmodFile();
		if (strpos($sPathTo."/", $sPathFrom."/")===0)
		{
			return FALSE;
		}

		if (is_dir($sPathFrom))
		{
			static::checkDirPath($sPathTo."/");
		}
		elseif(is_file($sPathFrom))
		{
			$p = Tools::ms_strrpos($sPathTo, "/");
			$path_to_dir = substr($sPathTo, 0, $p);
			static::checkDirPath($path_to_dir."/");

			if (file_exists($sPathTo) && !$bRewrite)
			{
				return FALSE;
			}

			@copy($sPathFrom, $sPathTo);
			if(is_file($sPathTo))
			{
				@chmod($sPathTo, $filePerm);
			}

			if ($bDeleteAfterCopy)
			{
				@unlink($sPathFrom);
			}

			return TRUE;
		}
		else
		{
			return TRUE;
		}

		if ($handle = @opendir($sPathFrom))
		{
			while (($file = readdir($handle)) !== false)
			{
				if ($file == "." || $file == "..")
				{
					continue;
				}

				if (strlen($sExclude)>0 && substr($file, 0, strlen($sExclude))==$sExclude)
				{
					continue;
				}

				if (is_dir($sPathFrom."/".$file) && $bRecursive)
				{
					static::copyDirFiles($sPathFrom."/".$file, $sPathTo."/".$file, $bRewrite, $bRecursive, $bDeleteAfterCopy, $sExclude);
					if ($bDeleteAfterCopy)
					{
						@rmdir($sPathFrom."/".$file);
					}
				}
				elseif (is_file($sPathFrom."/".$file))
				{
					if (file_exists($sPathTo."/".$file) && !$bRewrite)
					{
						continue;
					}

					@copy($sPathFrom."/".$file, $sPathTo."/".$file);
					@chmod($sPathTo."/".$file, $filePerm);

					if($bDeleteAfterCopy)
					{
						@unlink($sPathFrom."/".$file);
					}
				}
			}
			@closedir($handle);

			if ($bDeleteAfterCopy)
			{
				@rmdir($sPathFrom);
			}

			return true;
		}

		return false;
	}

	/**
	 * Возвращает расширение файла по указанному пути
	 *
	 * @param string $sPath Путь к файлу
	 *
	 * @return bool|string
	 */
	public static function getFileExtension ($sPath)
	{
		$sPath = rtrim($sPath, "\0.\\/+ ");
		$pos = strrpos($sPath, ".");
		return substr($sPath, $pos+1);
	}

	/**
	 * Возвращает тип файла по указанному пути (определяет по расширению файла)
	 *
	 * @param string $sPath Путь к файлу
	 *
	 * @return string
	 */
	public static function getFileType($sPath)
	{
		$ext = static::getFileExtension(strtolower($sPath));
		switch ($ext)
		{
			case "jpg":
			case "jpeg":
			case "gif":
			case "bmp":
			case "png":
				$type = "IMAGE";
				break;
			case "swf":
				$type = "FLASH";
				break;
			case "html":
			case "htm":
			case "asp":
			case "aspx":
			case "phtml":
			case "php":
			case "php3":
			case "php4":
			case "php5":
			case "php6":
			case "shtml":
			case "sql":
			case "txt":
			case "inc":
			case "js":
			case "vbs":
			case "tpl":
			case "css":
			case "shtm":
				$type = "SOURCE";
				break;
			default:
				$type = "UNKNOWN";
		}

		return $type;
	}

	/**
	 * Удаляет файлы из указанного расположения
	 *
	 * @param string $sPath Путь к удаляемым файлам
	 *
	 * @return bool
	 */
	public static function deleteDirFiles ($sPath)
	{
		if(strlen($sPath) <= 0)
		{
			return FALSE;
		}
		$docRoot = Entity\Application::getInstance()->getDocumentRoot();
		$sPath = str_replace($docRoot,'',$sPath);

		$src = $docRoot.$sPath;
		$src = str_replace(array("\\", "//"),"/",$src);
		if($src == $docRoot)
		{
			return FALSE;
		}

		$f = FALSE;
		if(is_file($docRoot.$sPath))
		{
			if(@unlink($docRoot.$sPath))
			{
				return FALSE;
			}

			return FALSE;
		}

		if($handle = @opendir($docRoot.$sPath))
		{
			while(($file = readdir($handle)) !== FALSE)
			{
				if($file == "." || $file == "..") continue;

				if(is_dir($docRoot.$sPath."/".$file))
				{
					if(!static::deleteDirFiles($sPath."/".$file))
					{
						$f = FALSE;
					}
				}
				else
				{
					if(!@unlink($docRoot.$sPath."/".$file))
					{
						$f = FALSE;
					}
				}
			}
			closedir($handle);
		}

		if(!@rmdir($docRoot.$sPath))
		{
			return FALSE;
		}
		else
		{
			return $f;
		}
	}

	/**
	 * Перезаписывает указанный файл
	 *
	 * @param string $sPath    Путь к файлу
	 * @param string $sContent Содержимое файла
	 *
	 * @return bool
	 */
	public static function rewriteFile ($sPath, $sContent)
	{
		$filePerm = Entity\Application::getInstance()->getSettings()->getChmodFile();
		$docRoot = Entity\Application::getInstance()->getDocumentRoot();
		$sPath = str_replace($docRoot,'',$sPath);
		$sPath = str_replace(array('\\','//'),'/',$sPath);
		$sPath = $docRoot.$sPath;
		static::checkDirPath($sPath);
		if(file_exists($sPath) && !is_writable($sPath))
		{
			@chmod($sPath, $filePerm);
		}
		$fd = fopen($sPath, "wb");
		if(!fwrite($fd, $sContent))
		{
			return FALSE;
		}
		@chmod($sPath, $filePerm);
		fclose($fd);

		return TRUE;
	}


}
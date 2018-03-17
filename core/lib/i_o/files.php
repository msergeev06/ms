<?php
/**
 * Ms\Core\Lib\IO\Files
 * Работа с файловой системой
 *
 * @package Ms\Core
 * @subpackage Lib\IO
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Lib\IO;

use Ms\Core\Entity;

class Files
{
	/**
	 * @var Entity\ErrorCollection
	 */
	protected static $errors;

	/**
	 * Возвращает права доступа к директории
	 *
	 * @return int
	 */
	public static function getDirChmod ()
	{

		$dirChmod = Entity\Application::getInstance()->getSettings()->getChmodDir();
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
		$fileChmod = Entity\Application::getInstance()->getSettings()->getChmodFile();
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
					if (!is_dir($dir.'/'.$file) && $file != '.' && $file != '..' && !in_array($file,$arIgnore))
					{
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
}
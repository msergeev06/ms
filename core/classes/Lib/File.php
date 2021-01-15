<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2016 Mikhail Sergeev
 */

namespace Ms\Core\Lib;

use Ms\Core\Entity\Db\Result\DBResult;
use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\System\Loader;
use Ms\Core\Exceptions\Arguments\ArgumentNullException;
use Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\Classes\ObjectNotFoundException;
use Ms\Core\Exceptions\Db\SqlQueryException;
use Ms\Core\Exceptions\Db\ValidateException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Tables;

/**
 * Класс Ms\Core\Lib\File
 * Работа с загружаемыми файлами
 *
 * Events:
 * OnBeforeUploadNewFile (&$arFile, &$arAdd)
 * OnAfterUploadNewFile (&$arFile, &$arAdd)
 * OnBeforeAddNewFile (&$arAdd, &$arFile)
 * OnAfterAddNewFile ($arFile,$res->getInsertId())
 */
class File
{
    /**
     * @var string
     */
    private static $documentRoot = null;

    /**
     * @var string
     */
    private static $uploadDir = null;

    /**
     * Инициализация основных параметров
     */
    private static function init()
    {
        $app = Application::getInstance();
        if (is_null(self::$documentRoot))
        {
            self::$documentRoot = Options::getOptionStr(
                'DOCUMENT_ROOT',
                $app->getDocumentRoot()
            );
        }
        if (is_null(self::$uploadDir))
        {
            self::$uploadDir = Options::getOptionStr(
                'UPLOAD_DIR',
                $app->getSettings()->getUploadDir()
            );
        }
    }

    /**
     * Возвращает параметры файла из базы по его ID
     *
     * @param int $fileID
     *
     * @return array|bool
     * @throws ArgumentNullException
     */
    private static function getByID($fileID = null)
    {
        if (is_null($fileID))
        {
            throw new ArgumentNullException('$fileID');
        }

        try
        {
            $arRes = Tables\FileTable::getOne(
                [
                    'filter' => ['ID' => $fileID]
                ]
            );
        }
        catch (SystemException $e)
        {
            return false;
        }

        return $arRes;
    }

    /**
     * Добавляет новое изображение
     *
     * @param string $moduleName - имя модуля, добавляющего изображение
     * @param array  $arFile     - параметры файла
     *
     * @return bool|int
     */
    public static function addNewImg($moduleName, array $arFile)
    {
        self::init();
        if (strpos($arFile['type'], 'image') === false)
        {
            return false;
        }
        $data = [];
        if (file_exists($arFile['tmp_name']))
        {
            list(
                $data['width'], $data['height'], $data['type_num'], $data['attr']
                )
                = getimagesize($arFile['tmp_name']);
            $arFile['width'] = $data['width'];
            $arFile['height'] = $data['height'];
            $arFile['type_num'] = $data['type_num'];
        }
        $arAdd = [];
        if (strlen($moduleName) <= 0 || !Loader::issetModule($moduleName))
        {
            return false;
        }
        else
        {
            $arAdd['MODULE'] = htmlspecialchars($moduleName);
        }
        if (isset($arFile['name']))
        {
            $arAdd['ORIGINAL_NAME'] = basename($arFile['name']);
        }
        if (isset($arFile['type']))
        {
            $arAdd['CONTENT_TYPE'] = htmlspecialchars($arFile['type']);
        }
        if (isset($arFile['size']))
        {
            $arAdd['FILE_SIZE'] = intval($arFile['size']);
        }
        if (isset($arFile['width']) && intval($arFile['width']) > 0)
        {
            $arAdd['WIDTH'] = intval($arFile['width']);
        }
        if (isset($arFile['height']) && intval($arFile['height']) > 0)
        {
            $arAdd['HEIGHT'] = intval($arFile['height']);
        }
        if (isset($arFile['title']))
        {
            $arAdd['DESCRIPTION'] = htmlspecialchars($arFile['title']);
        }
        $newName = md5($arFile['name'] . time());
        $arExt = explode('.', $arFile['name']);
        $countExt = count($arExt);
        $ext = $arExt[$countExt - 1];
        $sub = substr($newName, 0, 3);
        $arAdd['SUBDIR'] = $arAdd['MODULE'] . '/' . $sub;
        $arAdd['FILE_NAME'] = $newName . '.' . $ext;
        $uploadDir = self::$uploadDir;

        Events::runEvents(
            'core',
            'OnBeforeUploadNewFile',
            [&$arFile, &$arAdd]
        );

        if (!file_exists($uploadDir . '/' . $arAdd['MODULE']))
        {
            mkdir($uploadDir . '/' . $arAdd['MODULE']);
        }
        if (!file_exists($uploadDir . '/' . $arAdd['SUBDIR']))
        {
            mkdir($uploadDir . '/' . $arAdd['SUBDIR']);
        }
        if (isset($arFile['tmp_name']) && file_exists($arFile['tmp_name']))
        {
            move_uploaded_file(
                $arFile['tmp_name'],
                $uploadDir . '/' . $arAdd['SUBDIR'] . '/'
                . $arAdd['FILE_NAME']
            );
        }

        Events::runEvents(
            'core',
            'OnAfterUploadNewFile',
            [&$arFile, &$arAdd]
        );

        Events::runEvents(
            'core',
            'OnBeforeAddNewFile',
            [&$arFile, &$arAdd]
        );

        try
        {
            $res = Tables\FileTable::add($arAdd);
        }
        catch (SystemException $e)
        {
            return false;
        }
        if ($res->getResult())
        {
            Events::runEvents(
                'core',
                'OnAfterAddNewFile',
                [$arFile, $res->getInsertId()]
            );

            return $res->getInsertId();
        }

        return false;
    }

    /**
     * Возвращает html-код изображения или превью и большого изображения в popup
     *
     * @param mixed $strImage               ID файла или путь к файлу на текущем
     *                                      сайте либо URL к файлу лежащем на
     *                                      другом сайте. Если задается путь к
     *                                      файлу на текущем сайте, то его
     *                                      необходимо задавать относительно корня
     * @param int $maxWidth                 Максимальная ширина изображения.
     *                                      Если ширина картинки больше maxWidth,
     *                                      то она будет пропорционально
     *                                      смаштабирована. Необязательный.
     *                                      По умолчанию -
     *                                      "0" - без ограничений
     * @param int $maxHeight                Максимальная высота изображения.
     *                                      Если высота картинки больше maxHeight,
     *                                      то она будет пропорционально
     *                                      смаштабирована. Необязательный.
     *                                      По умолчанию -
     *                                      "0" - без ограничений. Если maxWidth
     *                                      установлен в 0, то maxHeight
     *                                      учитываться не будет. Чтобы
     *                                      ограничить высоту можно установить
     *                                      максимальную ширину в некое бо́льшее значение
     *                                      (например, 9999) вместо 0
     * @param string $sParams               Произвольный HTML добавляемый в тэг IMG:
     *                                      <img image_params ...>
     *                                      Необязательный. По умолчанию "null".
     *                                      Если в этом параметре передать атрибут
     *                                      alt="текст", то в теге <img> будет
     *                                      использовано это значение. Иначе, если
     *                                      картинка имеет описание в таблице,
     *                                      для атрибута alt будет использовано
     *                                      это описание.
     * @param string $imageUrl              Ссылка для перехода при нажатии на картинку.
     *                                      Необязательный. По умолчанию "" - не
     *                                      выводить ссылку.
     * @param bool $bPopup                  Открывать ли при клике на изображении
     *                                      дополнительное popup окно с
     *                                      увеличенным изображением.
     *                                      Необязательный. По умолчанию - "false".
     * @param bool $popupTitle              Текст всплывающей подсказки на
     *                                      изображении (только если popup = true)
     *                                      Необязательный. По умолчанию
     *                                      выводится фраза "Увеличить" на языке
     *                                      страницы.
     * @param int $sizeWHTTP                Ширина изображения (в пикселах)
     *                                      (только если в параметре image задан URL
     *                                      начинающийся с "http://")
     *                                      Необязательный. По умолчанию "0".
     * @param int $sizeHHTTP                Высота изображения (в пикселах)
     *                                      (только если в параметре image задан URL
     *                                      начинающийся с "http://")
     *                                      Необязательный. По умолчанию "0".
     *
     * @return string
     */
    public static function showImage(
        $strImage,
        $maxWidth = 0,
        $maxHeight = 0,
        $sParams = null,
        $imageUrl = '',
        $bPopup = false,
        $popupTitle = false,
        $sizeWHTTP = 0,
        $sizeHHTTP = 0
    ) {
        if (is_array($strImage))
        {
            $arImgParams = $strImage;
            $iImageID = isset($arImgParams['ID']) ? intval($arImgParams['ID']) : 0;
        }
        else
        {
            $arImgParams = self::getImgParams($strImage, $sizeWHTTP, $sizeHHTTP);
            $iImageID = intval($strImage);
        }

        if (!$arImgParams)
        {
            return "";
        }

        $iMaxW = intval($maxWidth);
        $iMaxH = intval($maxHeight);
        $intWidth = $arImgParams['WIDTH'];
        $intHeight = $arImgParams['HEIGHT'];

        if (
            $iMaxW > 0 && $iMaxH > 0
            && ($intWidth > $iMaxW || $intHeight > $iMaxH)
        )
        {
            $coeff = (
                $intWidth / $iMaxW > $intHeight / $iMaxH
                    ? $intWidth / $iMaxW
                    : $intHeight / $iMaxH
            );
            $iHeight = intval(Tools::roundEx($intHeight / $coeff));
            $iWidth = intval(Tools::roundEx($intWidth / $coeff));
        }
        else
        {
            $coeff = 1;
            $iHeight = $intHeight;
            $iWidth = $intWidth;
        }

        $strImage = $arImgParams['SRC'];

        //if (!preg_match("/^https?:/i", $strImage))
        //$strImage = urlencode($strImage);

        if (self::getFileType($strImage) == "FLASH")
        {
            $strReturn
                = '
                <object
                    classid="clsid:D27CDB6E-AE6D-11CF-96B8-444553540000"
                    codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
                    id="banner"
                    WIDTH="' . $iWidth . '"
                    HEIGHT="' . $iHeight . '"
                    ALIGN="">
                        <PARAM NAME="movie" VALUE="' . $strImage . '" />
                        <PARAM NAME="quality" VALUE="high" />
                        <PARAM NAME="bgcolor" VALUE="#FFFFFF" />
                        <embed
                            src="' . $strImage . '"
                            quality="high"
                            bgcolor="#FFFFFF"
                            WIDTH="' . $iWidth . '"
                            HEIGHT="' . $iHeight . '"
                            NAME="banner"
                            ALIGN=""
                            TYPE="application/x-shockwave-flash"
                            PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
                        </embed>
                </object>
            ';
        }
        else
        {
            $strAlt = $arImgParams['ALT'] ? $arImgParams['ALT'] : $arImgParams['DESCRIPTION'];

            if ($sParams === null || $sParams === false)
            {
                $sParams = 'border="0" alt="' . Tools::htmlspecialchars($strAlt) . '"';
            }
            elseif (!preg_match('/(^|\\s)alt\\s*=\\s*(["\']?)(.*?)(\\2)/is', $sParams))
            {
                $sParams .= ' alt="' . Tools::htmlspecialchars($strAlt) . '"';
            }

            if ($coeff === 1 && !$bPopup)
            {
                $strReturn = '<img src="' . $strImage . '" ' . $sParams . ' width="'
                             . $iWidth . '" height="' . $iHeight
                             . '" />';
            }
            else
            {
                Loc::includeLocFile(__FILE__, 'ms_core_');
                if ($popupTitle === false)
                {
                    $popupTitle = Loc::getModuleMessage('core', 'file_enlarge');
                }

                Application::getInstance()->includePlugin('magnific-popup');

                if (intval($iImageID) <= 0)
                {
                    $iImageID = str_replace(
                        ' ',
                        '',
                        str_replace(
                            '0.',
                            '',
                            microtime(false)
                        )
                    );
                }
                if (strlen($imageUrl) > 0)
                {
                    $strReturn = '<a href="' . $imageUrl . '" title="'
                                 . $popupTitle . '" class="popup-link-'
                                 . $iImageID . '">'
                                 . '<img src="' . $strImage . '" ' . $sParams
                                 . ' width="' . $iWidth . '" height="'
                                 . $iHeight . '" title="'
                                 . Tools::htmlspecialchars($popupTitle) . '" />'
                                 . '</a>';
                }
                else
                {
                    $strReturn = '<a href="' . $strImage . '" title="' . $strAlt
                                 . '" class="popup-link-' . $iImageID
                                 . '">'
                                 . '<img src="' . $strImage . '" ' . $sParams
                                 . ' width="' . $iWidth . '" height="'
                                 . $iHeight . '" title="'
                                 . Tools::htmlspecialchars($popupTitle) . '" />'
                                 . '</a>';
                }
                Application::getInstance()->addJsToDownPage(
                    "$('.popup-link-" . $iImageID
                    . "').magnificPopup({type: 'image'});"
                )
                ;
            }
        }

        return $strReturn;
    }

    /**
     * Удаляет файл с указанным ID
     *
     * @param int $fileID
     *
     * @return bool
     */
    public static function deleteFile($fileID = null)
    {
        if (!is_null($fileID) && intval($fileID) > 0)
        {
            try
            {
                $arFile = self::getFileArray($fileID);
            }
            catch (ArgumentNullException $e)
            {
                $arFile = false;
            }
            if ($arFile)
            {
                if (file_exists($arFile['FILE_PATH']))
                {
                    unlink($arFile['FILE_PATH']);
                    if (!file_exists($arFile['FILE_PATH']))
                    {
                        try
                        {
                            $res = Tables\FileTable::delete($arFile['ID']);
                        }
                        catch (SystemException $e)
                        {
                            $res = new DBResult();
                        }
                        if ($res->isSuccess())
                        {
                            return true;
                        }
                        else
                        {
                            try
                            {
                                $res = Tables\FileTable::update(
                                    $arFile['ID'],
                                    ['EXTERNAL_ID' => 'DELETE']
                                );
                            }
                            catch (SystemException $e)
                            {
                                $res = new DBResult();
                            }
                            if ($res->isSuccess())
                            {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Возвращает параметры файла изображения
     *
     * @param string $strImage
     * @param int    $iSizeWHTTP
     * @param int    $iSizeHHTTP
     *
     * @return array|bool
     */
    protected static function getImgParams($strImage, $iSizeWHTTP = 0, $iSizeHHTTP = 0)
    {
        if (strlen($strImage) <= 0)
        {
            return false;
        }

        if (intval($strImage) > 0)
        {
            try
            {
                $arFile = self::getFileArray($strImage);
            }
            catch (ArgumentNullException $e)
            {
                $arFile = false;
            }
            if ($arFile)
            {
                $strImage = $arFile["SRC"];
                $intWidth = intval($arFile["WIDTH"]);
                $intHeight = intval($arFile["HEIGHT"]);
                $strAlt = $arFile["DESCRIPTION"];
            }
            else
            {
                return false;
            }
        }
        else
        {
            if (!preg_match("#^https?://#", $strImage))
            {
                self::init();
                if (file_exists(self::$documentRoot . $strImage))
                {
                    $arSize = getimagesize(self::$documentRoot . $strImage);
                    $intWidth = intval($arSize[0]);
                    $intHeight = intval($arSize[1]);
                    $strAlt = "";
                }
                else
                {
                    return false;
                }
            }
            else
            {
                $intWidth = intval($iSizeWHTTP);
                $intHeight = intval($iSizeHHTTP);
                $strAlt = "";
            }
        }

        return [
            "SRC"    => $strImage,
            "WIDTH"  => $intWidth,
            "HEIGHT" => $intHeight,
            "ALT"    => $strAlt,
        ];
    }

    /**
     * Возвращает массив параметров файла из базы по ID файла
     *
     * @param int         $fileID
     * @param string|bool $upload_dir
     *
     * @return array|bool
     * @throws ArgumentNullException
     */
    protected static function getFileArray($fileID, $upload_dir = false)
    {
        if (!$upload_dir)
        {
            self::init();
            $upload_dir = Application::getInstance()->getSitePath(self::$uploadDir);
        }

        if (!is_array($fileID) && intval($fileID) > 0)
        {
            if ($arFile = self::getByID($fileID))
            {
                $arFile['SRC'] = $upload_dir . '/' . $arFile['SUBDIR']
                                 . '/' . $arFile['FILE_NAME'];
                $arFile['FILE_PATH'] = self::$uploadDir . '/' . $arFile['SUBDIR']
                                       . '/' . $arFile['FILE_NAME'];

                return $arFile;
            }
        }

        return false;
    }

    /**
     * Возвращает тип файла
     *
     * @param string $path
     *
     * @return string
     */
    public static function getFileType($path)
    {
        $extension = self::getFileExtension(strtolower($path));
        switch ($extension)
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
     * Возвращает расширение файла
     *
     * @param string $path
     *
     * @return string
     */
    public static function getFileExtension($path)
    {
        $path = self::getFileName($path);
        if ($path <> '')
        {
            $pos = Tools::strrpos($path, '.');
            if ($pos !== false)
            {
                return substr($path, $pos + 1);
            }
        }

        return '';
    }

    /**
     * Возвращает имя файла
     *
     * @param string $path
     *
     * @return mixed|string
     */
    public static function getFileName($path)
    {
        $path = Tools::trimUnsafe($path);
        $path = str_replace("\\", "/", $path);
        $path = rtrim($path, "/");

        $p = Tools::strrpos($path, "/");
        if ($p !== false)
        {
            return substr($path, $p + 1);
        }

        return $path;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core\Entity\Components\Parameters
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Components\Parameters;

/**
 * Класс Ms\Core\Entity\Components\Parameters\FileParameter
 * Параметр компонента типа FILE
 */
class FileParameter extends Parameter
{
    const EXT_DEFAULT = 'wmv,wma,flv,vp6,mp3,mp4,aac,jpg,jpeg,gif,png';

    protected $ext = null;
    protected $upload = false;

    public function __construct (string $code)
    {
        parent::__construct($code);
        $this->ext = self::EXT_DEFAULT;
    }

    public function getExt ()
    {
        return $this->ext;
    }

    public function getExtList ()
    {
        return explode(',',$this->ext);
    }

    public function setExt (string $ext)
    {
        $this->ext = strtolower($ext);

        return $this;
    }

    public function setExtList (array $arExt)
    {
        $this->ext = strtolower(implode(',',$arExt));

        return $this;
    }

    public function isUpload ()
    {
        return $this->upload;
    }

    public function setUpload (bool $upload = true)
    {
        $this->upload = $upload;

        return $this;
    }
}
<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\System;

use Ms\Core\Entity\Modules\Loader;
use Ms\Core\Entity\Options\Options;

/**
 * Класс Ms\Core\Entity\System\Starter
 * Инициализирует ядро и выполняет основные стартовые действия
 */
class Starter
{
    protected $pageStart = null;
    protected $coreRoot = null;
    protected $settings = null;

    public function __construct($pageStart, $coreRoot)
    {
        $this->pageStart = $pageStart;
        $this->coreRoot = $coreRoot;

        $GLOBALS["MS_STATE"] = "PB";
        if(isset($_REQUEST["MS_STATE"])) unset($_REQUEST["MS_STATE"]);
        if(isset($_GET["MS_STATE"])) unset($_GET["MS_STATE"]);
        if(isset($_POST["MS_STATE"])) unset($_POST["MS_STATE"]);
        if(isset($_COOKIE["MS_STATE"])) unset($_COOKIE["MS_STATE"]);
        if(isset($_FILES["MS_STATE"])) unset($_FILES["MS_STATE"]);

        include_once ($this->coreRoot . '/classes/Entity/System/Multiton.php');
        include_once ($this->coreRoot . '/classes/Entity/System/Settings.php');

        $this->settings = Settings::getInstance();
        $settingsFilePath = str_replace ('/core','',$this->coreRoot) . '/.settings.php';
        if (file_exists($settingsFilePath))
        {
            $arSettings = include_once ($settingsFilePath);
        }
        else
        {
            $arSettings = [];
        }

        $this->settings->init($arSettings);

        include_once ($this->coreRoot . '/classes/Entity/System/Autoloader.php');
        include_once ($this->coreRoot . '/classes/Entity/Modules/Loader.php');

        Autoloader::getInstance();
        // set_error_handler([ErrorHandler::class,'userErrorHandler']);
        // set_exception_handler([ErrorHandler::class,'exceptionHandler']);

        $app = (Application::getInstance())->init();
        $app->setTimes ('START_EXEC_PROLOG_BEFORE_1',$this->pageStart);
        $app->setState('PB');

        Loader::getInstance();

        if (!defined('MS_AUTOLOAD_CLASSES_ENABLED'))
        {
            define('MS_AUTOLOAD_CLASSES_ENABLED',true);
        }

        Options::getInstance();

    }
}
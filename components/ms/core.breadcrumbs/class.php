<?php
/**
 * Компонент ядра ms:code.breadcrumbs
 *
 * @package    Ms\Core
 * @subpackage Entity\Components
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2018 Mikhail Sergeev
 * @since      0.2.0
 */

namespace Ms\Core\Entity\Components;

use Ms\Core\Entity\Errors\FileLogger;
use Ms\Core\Entity\Events\EventRegistrar;
use Ms\Core\Entity\System\Application;
use Ms\Core\Exceptions\Classes\ClassNotFoundException;
use Ms\Core\Exceptions\Classes\MethodNotFoundException;
use Ms\Core\Exceptions\IO\FileNotFoundException;
use Ms\Core\Exceptions\Modules\WrongModuleNameException;

class BreadcrumbsComponent extends Component
{
    protected $logger = null;

    public function __construct ($component, $template = '.default', $arParams = [])
    {
        parent::__construct($component, $template, $arParams);
        $this->logger = new FileLogger('core');
    }

    public function run ()
    {
        $app = Application::getInstance();

        $path = $this->getIncludeTemplatePath();
        if (!is_null($path))
        {
            $app->showBufferContent('breadcrumbs_component');
            $app->setAppParams('breadcrumbs_path', $path);
            try
            {
                EventRegistrar::getInstance()->addEventHandler(
                    'core',
                    'OnEndBufferPage',
                    '\Ms\Core\Entity\System\Breadcrumbs',
                    'onEndBufferPageHandler'
                );
            }
            catch (ClassNotFoundException $e)
            {
                $e->addMessageToLog($this->logger);
            }
            catch (MethodNotFoundException $e)
            {
                $e->addMessageToLog($this->logger);
            }
            catch (FileNotFoundException $e)
            {
                $e->addMessageToLog($this->logger);
            }
            catch (WrongModuleNameException $e)
            {
                $e->addMessageToLog($this->logger);
            }
        }
    }
}
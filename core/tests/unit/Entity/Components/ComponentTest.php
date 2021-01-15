<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use Ms\Core\Entity\Components\Component;

class TestComponent extends Component
{
    public $isRun = false;

    public function run ()
    {
        $this->isRun = true;
    }

    public function getNamespace ()
    {
        return $this->namespace;
    }

    public function getComponentName ()
    {
        return $this->componentName;
    }

    public function getComponentTemplate ()
    {
        return $this->componentTemplate;
    }

    public function getComponentsRoot ()
    {
        return $this->componentsRoot;
    }

    public function getTemplatesRoot ()
    {
        return $this->templatesRoot;
    }

    public function getSiteTemplate ()
    {
        return $this->siteTemplate;
    }
}

/**
 * Класс \ComponentTest
 * Тесты класса Ms\Core\Entity\Components\Component
 */
class ComponentTest extends \PHPUnit\Framework\TestCase
{
    protected $app = null;

    protected function setUp ()
    {
        $this->app = \Ms\Core\Entity\System\Application::getInstance();
        $this->app
            ->setSettings()
            ->setServer($_SERVER)
        ;
    }

    /**
     * @covers \Ms\Core\Entity\Components\Component::__construct
     * @covers \Ms\Core\Entity\Components\Component::getClassName
     * @covers \Ms\Core\Entity\Components\Component::getIncludeTemplatePath
     * @covers \Ms\Core\Entity\Components\Component::getTemplatePath
     * @covers \Ms\Core\Entity\Components\Component::run
     * @covers \Ms\Core\Entity\Components\Component::includeTemplate
     */
    public function testComponentInit ()
    {
        try
        {
            $comp = new TestComponent('ms:component.test');
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentException $e)
        {
            $this->assertTrue(false);
            return;
        }
        $this->assertEquals(TestComponent::class,$comp->getClassName());
        $this->assertEquals('ms',$comp->getNamespace());
        $this->assertEquals('component.test',$comp->getComponentName());
        $this->assertEquals('.default',$comp->getComponentTemplate());
        $this->assertContains('/ms/components',$comp->getComponentsRoot());
        $this->assertContains('/ms/templates',$comp->getTemplatesRoot());
        $this->assertEquals('.default',$comp->getSiteTemplate());
        $this->assertFalse($comp->getTemplatePath());
        $this->assertTrue(is_null($comp->getIncludeTemplatePath()));
        $this->assertTrue($comp->isRun);
        ob_start();
        $this->assertTrue(is_null($comp->includeTemplate()));
        $out = ob_get_contents();
        ob_end_clean();
        $this->assertEquals(
            '<span style="color: red">Ошибка подключения шаблона "'
            .$comp->getComponentTemplate()
            .'" компонента "'.$comp->getComponentName().'". Шаблон не найден!</span><br>'
            ,
            $out
        );
    }


}
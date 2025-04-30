<?php

namespace ZendTwig\Test\Extension;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Laminas\Mvc\MvcEvent;
use ZendTwig\Extension\Extension;
use ZendTwig\Module;
use ZendTwig\Test\Bootstrap;
use ZendTwig\Test\Fixture\DummyClass;
use ZendTwig\Test\Fixture\Extension\InstanceOfExtension;
use ZendTwig\View\TwigStrategy;
use ZendTwig\Renderer\TwigRenderer;

class CustomExtensionTest extends TestCase
{
    public function testExtensionFactory()
    {
        $config = include(__DIR__ . '/../../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../../Fixture/config/extensions/{{,*.}instanceOf}.php',
        ];

        /* @var InstanceOfExtension $extension */
        $sm = Bootstrap::getInstance($config)->getServiceManager();
        $extension = $sm->get(InstanceOfExtension::class);

        $exRender = $extension->getRenderer();
        $exSm     = $extension->getServiceManager();

        $this->assertInstanceOf(TwigRenderer::class, $exRender);
        $this->assertInstanceOf('\Interop\Container\ContainerInterface', $exSm);
        $this->assertSame($sm, $exSm);
    }

    public function testExtensionConstruct()
    {
        /**
         * @var \ZendTwig\Extension\Extension $extension
         */
        $sm        = Bootstrap::getInstance()->getServiceManager();
        $renderer  = $sm->get(\ZendTwig\Renderer\TwigRenderer::class);
        $extension = new Extension($sm, $renderer);

        $exRender = $extension->getRenderer();
        $exSm     = $extension->getServiceManager();

        $this->assertInstanceOf('\ZendTwig\Renderer\TwigRenderer', $exRender);
        $this->assertInstanceOf('\Interop\Container\ContainerInterface', $exSm);

        $this->assertSame($sm, $exSm);
        $this->assertSame($renderer, $exRender);
    }

    /**
     * @dataProvider generatorExtensionData
     *
     * @param array  $vars
     * @param string $expected
     */
    public function testExtension($vars, $expected)
    {
        $config = include(__DIR__ . '/../../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../../Fixture/config/extensions/{{,*.}instanceOf}.php',
        ];

        $model = new \Laminas\View\Model\ViewModel($vars);
        $model->setTemplate('Extensions/InstanceOf');

        $e = new MvcEvent();
        $e->setApplication(Bootstrap::getInstance($config)->getApplication());

        $module = new Module();
        $module->onBootstrap($e);

        /**
         * @var \Laminas\View\View $view
         */
        $sm           = Bootstrap::getInstance($config)->getServiceManager();
        $strategyTwig = $sm->get(TwigStrategy::class);
        $view         = $sm->get('View');
        $request      = $sm->get('Request');
        $response     = $sm->get('Response');

        $e = $view->getEventManager();
        $strategyTwig->attach($e, 100);

        $view->setEventManager($e)
            ->setRequest($request)
            ->setResponse($response)
            ->render($model);

        $result = $view->getResponse()
            ->getContent();

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public static function generatorExtensionData()
    {
        return [
            [
                [
                 'varClass' => new DummyClass(),
                 'varInstance' => DummyClass::class,
                ],
                "<span>Yes</span>\n"
            ],
            [
                [
                    'varClass' => new DummyClass(),
                    'varInstance' => \ZendTwig\Module::class,
                ],
                "<span>No</span>\n"
            ],
        ];
    }
}

<?php

namespace ZendTwig\Test\Extension;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\MvcEvent;
use Zend\Server\Reflection\ReflectionClass;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\View\ViewEvent;
use ZendTwig\Module;
use ZendTwig\Renderer\TwigRenderer;
use ZendTwig\Test\Bootstrap;
use ZendTwig\Test\Fixture\DummyClass;
use ZendTwig\View\TwigStrategy;

class InstanceOfText extends TestCase
{
    /**
     * @dataProvider generatorFallbackToZendHelpers
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

        $model = new \Zend\View\Model\ViewModel($vars);
        $model->setTemplate('Extensions/InstanceOf');

        $e = new MvcEvent();
        $e->setApplication(Bootstrap::getInstance($config)->getApplication());

        $module = new Module();
        $module->onBootstrap($e);

        /**
         * @var \Zend\View\View $view
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
    public function generatorFallbackToZendHelpers()
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

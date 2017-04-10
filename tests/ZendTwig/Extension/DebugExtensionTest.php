<?php

namespace ZendTwig\Test\Extension;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\MvcEvent;
use ZendTwig\Module;
use ZendTwig\Test\Bootstrap;
use ZendTwig\View\TwigStrategy;

class DebugExtensionTest extends TestCase
{
    /**
     * @dataProvider generatorDebugExtension
     *
     * @param $vars
     * @param $expected
     */
    public function testDebugExtension($vars, $expected)
    {
        $config = include(__DIR__ . '/../../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../../Fixture/config/extensions/{{,*.}debug}.php',
        ];

        $model = new \Zend\View\Model\ViewModel($vars);
        $model->setTemplate('Extensions/Debug');

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

        $this->assertContains($expected, $result);
    }

    /**
     * @return array
     */
    public function generatorDebugExtension()
    {
        $randInt = mt_rand(PHP_INT_MAX / 2, PHP_INT_MAX);

        return [
            [
                [
                    'variable' => $randInt,
                ],
                "int($randInt)",
            ],
            [
                [
                    'variable' => false,
                ],
                "bool(false)",
            ],
        ];
    }
}

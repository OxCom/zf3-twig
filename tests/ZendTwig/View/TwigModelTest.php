<?php

namespace ZendTwig\Test\View;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\Response;
use Zend\View\Model\ViewModel;
use ZendTwig\Module;
use ZendTwig\Test\Bootstrap;
use ZendTwig\View\TwigModel;

class TwigModelTest extends TestCase
{
    public function testThreeRenders()
    {
        /**
         * @var \Twig_Environment $env
         */
        $config = include(__DIR__ . '/../../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../../Fixture/config/model/{{,*.}local}.php',
        ];

        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         * @var \Zend\View\View $view
         */
        $bootstrap = Bootstrap::getInstance($config);

        $e = new MvcEvent();
        $e->setApplication($bootstrap->getApplication());

        $module = new Module();
        $module->onBootstrap($e);

        $vm = new \Zend\Mvc\View\Http\ViewManager();
        $vm->onBootstrap($e);

        $view         = $bootstrap->getServiceManager()->get(\Zend\View\View::class);
        $response     = new Response();
        $twigModel    = new TwigModel(['value' => 'twig']);
        $genericModel = new ViewModel(['value' => 'php']);

        $view->setResponse($response);
        $twigModel->setTemplate('View/model/testTwigModel');
        $genericModel->setTemplate('View/model/testPhpModel');

        $view->render($twigModel);
        $result = $view->getResponse();

        $this->assertEquals('TWIG:twig', $result->getContent());

        $view->render($genericModel);
        $result = $view->getResponse();

        $this->assertEquals('PHP:php', $result->getContent());
    }
}

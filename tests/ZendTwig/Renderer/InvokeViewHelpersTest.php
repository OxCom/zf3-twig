<?php

namespace ZendTwig\Test\Renderer;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\MvcEvent;
use ZendTwig\Module;
use ZendTwig\Test\Bootstrap;
use ZendTwig\Renderer\TwigRenderer;

class InvokeViewHelpersTest extends TestCase
{
    /**
     * @see https://github.com/OxCom/zf3-twig/issues/7
     */
    public function testRenderModelString()
    {
        $config = include(__DIR__ . '/../../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../../Fixture/config/view_helpers/{{,*.}local}.php',
        ];

        $bootstrap = Bootstrap::getInstance($config);
        $e = new MvcEvent();
        $e->setApplication($bootstrap->getApplication());

        $module = new Module();
        $module->onBootstrap($e);

        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm        = $bootstrap->getServiceManager();
        $render    = $sm->get(TwigRenderer::class);

        $result = $render->render('View/issue-7/ViewHelpers', []);
        $this->assertEquals("permission a, b, c", $result);

        $result = $render->render('View/issue-7/ViewHelpersInvoke', []);
        $this->assertEquals("permission a, b, c", $result);
    }
}

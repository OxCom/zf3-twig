<?php

namespace ZendTwig\Test;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Laminas\Mvc\MvcEvent;
use ZendTwig\Module;
use ZendTwig\Renderer\TwigRenderer;
use ZendTwig\Test\Fixture\Extension\DummyExtension;

class ModuleTest extends TestCase
{
    /**
     * Module loaded
     */
    public function testOnBootstrap()
    {
        $e = new MvcEvent();
        $e->setApplication(Bootstrap::getInstance()->getApplication());

        $module = new Module();
        $module->onBootstrap($e);

        $render = $e->getApplication()->getServiceManager()->get(TwigRenderer::class);
        $this->assertInstanceOf(TwigRenderer::class, $render);
    }

    public function testOnBootstrapNullExtension()
    {
        $config = include(__DIR__ . '/../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../Fixture/config/extensions/{{,*.}empty}.php',
        ];

        $e = new MvcEvent();
        $e->setApplication(Bootstrap::getInstance($config)->getApplication());

        $module = new Module();
        $module->onBootstrap($e);

        $render = $e->getApplication()->getServiceManager()->get(TwigRenderer::class);
        $this->assertInstanceOf(TwigRenderer::class, $render);
    }

    /**
     * @expectedException \Laminas\View\Exception\InvalidArgumentException
     */
    public function testOnBootstrapExceptionExtension()
    {
        $config = include(__DIR__ . '/../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../Fixture/config/extensions/{{,*.}exception}.php',
        ];

        $e = new MvcEvent();
        $e->setApplication(Bootstrap::getInstance($config)->getApplication());

        $module = new Module();
        $module->onBootstrap($e);
    }

    public function testOnBootstrapFactoryExtension()
    {
        $config = include(__DIR__ . '/../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../Fixture/config/extensions/{{,*.}factory}.php',
        ];

        $e = new MvcEvent();
        $e->setApplication(Bootstrap::getInstance($config)->getApplication());

        $module = new Module();
        $module->onBootstrap($e);

        /**
         * @var Environment $twig
         */
        $twig = $e->getApplication()->getServiceManager()->get(Environment::class);
        $ex   = $twig->getExtensions();

        $render = $e->getApplication()->getServiceManager()->get(TwigRenderer::class);
        $this->assertInstanceOf(TwigRenderer::class, $render);
        $this->assertNotEmpty($ex[DummyExtension::class]);
    }

    /**
     * Check that module was loaded
     */
    public function testLoadModule()
    {
        /**
         * @var \ZendTwig\Module $module
         */
        $module = Bootstrap::getInstance()->getModule('ZendTwig');

        $this->assertInstanceOf(Module::class, $module);

        $configA = include(__DIR__ . '/../../config/module.config.php');
        $configB = $module->getConfig();

        $this->assertEquals($configA, $configB);
    }

    /**
     * Check that config was loaded
     */
    public function testLoadConfig()
    {
        $config = Bootstrap::getInstance()->getServiceManager()->get('Configuration');

        $this->assertTrue(isset($config[Module::MODULE_NAME]));

        $configA = include(__DIR__ . '/../../config/module.config.php');
        $configB = $config[Module::MODULE_NAME];

        $this->assertEquals($configA[Module::MODULE_NAME], $configB);
    }

    /**
     * @expectedException \Laminas\ServiceManager\Exception\ServiceNotCreatedException
     */
    public function testLoadConfigWithInvalidHelpersClass()
    {
        $config = include(__DIR__ . '/../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../Fixture/config/helpers/{{,*.}class-exception}.php',
        ];

        $e = new MvcEvent();
        $e->setApplication(Bootstrap::getInstance($config)->getApplication());

        $module = new Module();
        $module->onBootstrap($e);
    }

    /**
     * @expectedException \Laminas\ServiceManager\Exception\ServiceNotCreatedException
     */
    public function testLoadConfigWithInvalidHelpersInstance()
    {
        $config = include(__DIR__ . '/../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../Fixture/config/helpers/{{,*.}instance-exception}.php',
        ];

        $e = new MvcEvent();
        $e->setApplication(Bootstrap::getInstance($config)->getApplication());

        $module = new Module();
        $module->onBootstrap($e);
    }

    /**
     * @expectedException \Laminas\ServiceManager\Exception\ServiceNotCreatedException
     */
    public function testLoadConfigWithInvalidLoaderClass()
    {
        $config = include(__DIR__ . '/../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../Fixture/config/loader/{{,*.}exception}.php',
        ];

        $e = new MvcEvent();
        $e->setApplication(Bootstrap::getInstance($config)->getApplication());

        $module = new Module();
        $module->onBootstrap($e);
    }
}

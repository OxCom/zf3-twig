<?php

namespace ZendTwig\Test;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\MvcEvent;
use ZendTwig\Module;

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
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
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
         * @var \Twig_Environment $twig
         */
        $twig = $e->getApplication()->getServiceManager()->get('Twig_Environment');
        $ex   = $twig->getExtensions();

        $this->assertNotEmpty($ex['ZendTwig\Test\Fixture\Extension\DummyExtension']);
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

        $this->assertInstanceOf('\ZendTwig\Module', $module);

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
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    public function testLoadConfigWithInvalidHelpersClass()
    {
        $config = include(__DIR__ . '/../Fixture/config/application.config.php');
        $config['module_listener_options']['config_glob_paths'] = [
            realpath(__DIR__) . '/../Fixture/config/helpers/{{,*.}exception}.php',
        ];

        $e = new MvcEvent();
        $e->setApplication(Bootstrap::getInstance($config)->getApplication());

        $module = new Module();
        $module->onBootstrap($e);
    }

    /**
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotCreatedException
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

<?php

namespace ZendTwig\Test;

use PHPUnit_Framework_TestCase as TestCase;
use ZendTwig\Module;

class ModuleTest extends TestCase
{

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
}

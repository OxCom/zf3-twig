<?php

namespace ZendTwig\Test;

use PHPUnit_Framework_TestCase as TestCase;

class ModuleTest extends TestCase
{

    /**
     * Check that module was loaded
     */
    public function testLoad()
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
}

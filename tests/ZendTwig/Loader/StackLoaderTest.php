<?php

namespace ZendTwig\Test\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use ZendTwig\Loader\StackLoader;
use ZendTwig\Service\TwigLoaderFactory;
use ZendTwig\Test\Bootstrap;

class StackLoaderTest extends TestCase
{
    public function testGetSuffix()
    {
        /**
         * @var \ZendTwig\Loader\StackLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(StackLoader::class);

        $this->assertEquals(TwigLoaderFactory::DEFAULT_SUFFIX, $loader->getSuffix());
    }

    public function testSetSuffix()
    {
        /**
         * @var \ZendTwig\Loader\StackLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(StackLoader::class);

        $this->assertEquals(TwigLoaderFactory::DEFAULT_SUFFIX, $loader->getSuffix());

        $loader->setSuffix('.sfx');
        $this->assertEquals('sfx', $loader->getSuffix());

        $loader->setSuffix('sfy');
        $this->assertEquals('sfy', $loader->getSuffix());

        $loader->setSuffix(TwigLoaderFactory::DEFAULT_SUFFIX);
        $this->assertEquals(TwigLoaderFactory::DEFAULT_SUFFIX, $loader->getSuffix());
    }

    /**
     * @expectedException \Twig_Error_Loader
     * @expectedExceptionMessageRegExp /Unable to find template/
     */
    public function testFindTemplateExNoTemplate()
    {
        /**
         * @var \ZendTwig\Loader\StackLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(StackLoader::class);

        $loader->getSourceContext('testFindTemplateExNoTemplate');
    }

    public function testFindTemplateNoExNoTemplate()
    {
        /**
         * @var \ZendTwig\Loader\StackLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(StackLoader::class);

        $reflection = new \ReflectionClass($loader);
        $method = $reflection->getMethod('findTemplate');
        $method->setAccessible(true);

        $value = $method->invokeArgs($loader, ['testFindTemplateNoExNoTemplate', false]);
        $this->assertFalse($value);
    }

    /**
     * @expectedException \Twig_Error_Loader
     * @expectedExceptionMessageRegExp /There are no registered paths for namespace/
     */
    public function testFindTemplateExNamespace()
    {
        /**
         * @var \ZendTwig\Loader\StackLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(StackLoader::class);

        $loader->getSourceContext('@ns/testFindTemplate');
    }

    public function testFindTemplateNoExNamespace()
    {
        /**
         * @var \ZendTwig\Loader\StackLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(StackLoader::class);

        $reflection = new \ReflectionClass($loader);
        $method = $reflection->getMethod('findTemplate');
        $method->setAccessible(true);

        $value = $method->invokeArgs($loader, ['@ns/testFindTemplate', false]);
        $this->assertFalse($value);
    }

    public function testFindTemplate()
    {
        /**
         * @var \ZendTwig\Loader\StackLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(StackLoader::class);

        $template = $loader->getSourceContext('View/testFindTemplate');
        $this->assertNotEmpty($template);
    }

    public function testFindTemplateCache()
    {
        /**
         * @var \ZendTwig\Loader\StackLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(StackLoader::class);

        // check that cache empty
        $ref      = new \ReflectionClass($loader);
        $property = $ref->getProperty('cache');
        $property->setAccessible(true);

        $cacheBefore = $property->getValue($loader);

        $template = $loader->getSourceContext('View/testFindTemplateCache.twig');
        $this->assertNotEmpty($template);

        $cacheAfter = $property->getValue($loader);
        $this->assertNotEmpty($cacheAfter);

        $this->assertNotEquals($cacheBefore, $cacheAfter);
    }
}

<?php

namespace ZendTwig\Test\Loader;

use PHPUnit\Framework\TestCase;
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

    public function testFindTemplateExNoTemplate()
    {
        /**
         * @var \ZendTwig\Loader\StackLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(StackLoader::class);

        $this->expectException(\Twig\Error\LoaderError::class);
        $this->expectExceptionMessageMatches('/Unable to find template/');
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
        $this->assertEmpty($value);
    }

    public function testFindTemplateExNamespace()
    {
        /**
         * @var \ZendTwig\Loader\StackLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(StackLoader::class);

        $this->expectException(\Twig\Error\LoaderError::class);
        $this->expectExceptionMessageMatches('/There are no registered paths for namespace/');
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
        $this->assertEmpty($value);
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

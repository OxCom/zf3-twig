<?php

namespace ZendTwig\Test\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use ZendTwig\Loader\MapLoader;
use ZendTwig\Test\Bootstrap;

class MapLoaderTest extends TestCase
{
    public function testExists()
    {
        /**
         * @var \ZendTwig\Loader\MapLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(MapLoader::class);

        $this->assertTrue($loader->exists('layout'));
        $this->assertFalse($loader->exists('not-exists'));
    }

    /**
     * @dataProvider generatorAdd
     *
     * @param string $layout
     * @param string $path
     *
     * @throws \Twig_Error_Loader
     */
    public function testAdd($layout, $path)
    {
        /**
         * @var \ZendTwig\Loader\MapLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(MapLoader::class);

        $chain = $loader->add($layout, $path);
        $this->assertSame($loader, $chain);
        $this->assertTrue($loader->exists('layout'));
        $this->assertTrue($chain->exists('layout'));
    }

    /**
     * @return array
     */
    public function generatorAdd()
    {
        return [
            ['layout-1', 'path/to/layout-1.twig'],
            ['layout-2', 'path/to/layout-2.twig'],
            ['layout-3', 'path/to/layout-3.twig'],
        ];
    }

    /**
     * @expectedException \Twig_Error_Loader
     * @expectedExceptionMessage Name "layout" already exists in map
     */
    public function testAddEx()
    {
        /**
         * @var \ZendTwig\Loader\MapLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(MapLoader::class);

        $this->assertTrue($loader->exists('layout'));
        $loader->add('layout-4', 'path/to/layout-4.twig');

        $this->assertTrue($loader->exists('layout'));
        $loader->add('layout', 'path/to/layout.twig');
    }

    public function testGetSourceContext()
    {
        /**
         * @var \ZendTwig\Loader\MapLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(MapLoader::class);
        $layout = 'layout';

        $this->assertTrue($loader->exists($layout));
        $data = $loader->getSourceContext($layout);

        $this->assertInstanceOf(\Twig_Source::class, $data);
        $this->assertNotEmpty($data->getCode());
    }

    /**
     * @expectedException \Twig_Error_Loader
     * @expectedExceptionMessage Unable to find template "layout-not-exists" from template map
     */
    public function testGetSourceNotExistsMap()
    {
        /**
         * @var \ZendTwig\Loader\MapLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(MapLoader::class);
        $layout = 'layout-not-exists';

        $this->assertFalse($loader->exists($layout));
        $loader->getSourceContext($layout);
    }

    /**
     * @expectedException \Twig_Error_Loader
     * @expectedExceptionMessage Unable to open file "path/to/not/exists/file.twig" from template map
     */
    public function testGetSourceNotExistsFile()
    {
        /**
         * @var \ZendTwig\Loader\MapLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(MapLoader::class);
        $layout = 'layout-file-not-exists';
        $path   = 'path/to/not/exists/file.twig';

        $loader->add($layout, $path);
        $this->assertTrue($loader->exists($layout));
        $loader->getSourceContext($layout);
    }

    public function testIsFresh()
    {
        /**
         * @var \ZendTwig\Loader\MapLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(MapLoader::class);

        $this->assertFalse($loader->isFresh('layout', 0));
        $this->assertTrue($loader->isFresh('layout', PHP_INT_MAX));
    }

    /**
     * @throws \Twig_Error_Loader
     */
    public function testIsFreshNotExists()
    {
        /**
         * @var \ZendTwig\Loader\MapLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(MapLoader::class);

        $this->expectException('\Twig_Error_Loader');
        $this->expectExceptionMessageRegExp('/Unable to find template/');

        $loader->isFresh(rand(1, PHP_INT_MAX), 0);
    }

    /**
     * @throws \Twig_Error_Loader
     */
    public function testIsFreshNotFile()
    {
        /**
         * @var \ZendTwig\Loader\MapLoader $loader
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $loader = $sm->get(MapLoader::class);

        $name = 'template-' . rand(1, PHP_INT_MAX);
        $loader->add($name, 'path/to/layout-1.twig');

        $this->expectException('\Twig_Error_Loader');
        $this->expectExceptionMessageRegExp('/Unable to open file/');

        $loader->isFresh($name, 0);
    }
}

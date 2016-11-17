<?php

namespace ZendTwig\Test\Renderer;

use PHPUnit_Framework_TestCase as TestCase;
use Twig_Environment;
use ZendTwig\Test\Bootstrap;
use ZendTwig\Renderer\TwigRenderer;

class TwigRendererTest extends TestCase
{
    public function testGetEngine()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $engine = $render->getEngine();

        $this->assertSame($render, $engine);
    }

    public function testGetEnv()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);
        $env = $sm->get(Twig_Environment::class);

        $this->assertSame($env, $render->getEnvironment());
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     * @expectedExceptionMessageRegExp /Twig environment must be/
     */
    public function testGetEnvEx()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);
        $renderClone = clone $render;

        $renderClone->setEnvironment('qwe')
                    ->getEnvironment();
    }

    public function testGetLoader()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);
        $loader = $render->getLoader();

        $this->assertInstanceOf('\Twig_Loader_Chain', $loader);
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     * @expectedExceptionMessageRegExp /Twig loader must implement/
     */
    public function testGetLoaderEx()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);
        $renderClone = clone $render;

        $renderClone->setLoader('qwe')
                    ->getLoader();
    }

    public function testGetResolver()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm       = Bootstrap::getInstance()->getServiceManager();
        $render   = $sm->get(TwigRenderer::class);
        $resolver = $render->getResolver();

        $this->assertInstanceOf('\ZendTwig\Resolver\TwigResolver', $resolver);
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /must implement interface Zend\\View\\Resolver\\ResolverInterface/
     */
    public function testGetResolverEx()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);
        $renderClone = clone $render;

        $renderClone->setResolver('qwe');
    }

    public function testGetView()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);
        $view   = $render->getView();

        $this->assertInstanceOf('\Zend\View\View', $view);
    }

    public function testSetGetCanRenderTrees()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm           = Bootstrap::getInstance()->getServiceManager();
        $render       = $sm->get(TwigRenderer::class);
        $canRenderOld = $render->canRenderTrees();

        $render->setCanRenderTrees(!$canRenderOld);
        $this->assertNotEquals($canRenderOld, $render->canRenderTrees());

        $render->setCanRenderTrees($canRenderOld);
        $this->assertEquals($canRenderOld, $render->canRenderTrees());
    }

    /**
     * @expectedException \Zend\View\Exception\DomainException
     * @expectedExceptionMessageRegExp /but template is empty/
     */
    public function testRenderModelExNotTemplate()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $model = new \Zend\View\Model\ViewModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $render->render($model);
    }

    public function testRenderModelOptions()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm        = Bootstrap::getInstance()->getServiceManager();
        $render    = $sm->get(TwigRenderer::class);
        $view      = $sm->get('View');
        $viewClone = clone $view;

        $model = new \Zend\View\Model\ViewModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $model->setTemplate('View/testInjectResponse');
        $model->setOptions([
            'View' => $viewClone,
        ]);

        $expect     = "<span>value1</span><span>value2</span>\n";
        $result     = $render->render($model);
        $renderView = $render->getView();
        $this->assertSame($viewClone, $renderView);
        $render->setView($view);

        $this->assertEquals($expect, $result);
    }

    public function testRenderModelString()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm        = Bootstrap::getInstance()->getServiceManager();
        $render    = $sm->get(TwigRenderer::class);

        $expect     = "<span>value1</span><span>value2</span>\n";
        $result     = $render->render('View/testInjectResponse', [
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $this->assertEquals($expect, $result);
    }

    public function testRenderNull()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm        = Bootstrap::getInstance()->getServiceManager();
        $render    = $sm->get(TwigRenderer::class);

        $result     = $render->render('View/testRenderNull', [
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $this->assertEmpty($result);
    }

    public function testRenderChild()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $modelParent = new \Zend\View\Model\ViewModel([
            'key1' => 'value1',
        ]);
        $modelParent->setTemplate('View/testRenderChild');

        $modelChild1 = new \Zend\View\Model\ViewModel([
            'key1' => 'child1-1',
            'key2' => 'child1-2',
        ]);

        $modelChild2 = new \Zend\View\Model\ViewModel([
            'key1' => 'child2-1',
            'key2' => 'child2-2',
        ]);

        $modelChild3 = new \Zend\View\Model\ViewModel([
            'key1' => 'child3-1',
            'key2' => 'child3-2',
        ]);

        $modelChild1->setTemplate('View/testInjectResponse');
        $modelChild2->setTemplate('View/testInjectResponse');
        $modelChild3->setTemplate('View/testInjectResponse');

        $modelParent->addChild($modelChild1, 'injectChild');
        $modelParent->addChild($modelChild2);
        $modelParent->addChild($modelChild3, 'injectChild', true);

        $expect = "<span>value1</span><span><span>child1-1</span><span>child1-2</span>\n"
                    . "<span>child3-1</span><span>child3-2</span>\n"
                    . "</span><span>child2-1</span><span>child2-2</span>\n\n";
        $result = $render->render($modelParent);

        $this->assertEquals($expect, $result);
    }

    public function testRenderChildNoAppend()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $modelParent = new \Zend\View\Model\ViewModel([
            'key1' => 'value1',
        ]);
        $modelParent->setTemplate('View/testRenderChild');

        $modelChild1 = new \Zend\View\Model\ViewModel([
            'key1' => 'child1-1',
            'key2' => 'child1-2',
        ]);

        $modelChild2 = new \Zend\View\Model\ViewModel([
            'key1' => 'child2-1',
            'key2' => 'child2-2',
        ]);

        $modelChild3 = new \Zend\View\Model\ViewModel([
            'key1' => 'child3-1',
            'key2' => 'child3-2',
        ]);

        $modelChild1->setTemplate('View/testInjectResponse');
        $modelChild2->setTemplate('View/testInjectResponse');
        $modelChild3->setTemplate('View/testInjectResponse');

        $modelParent->addChild($modelChild1, 'injectChild');
        $modelParent->addChild($modelChild2);
        $modelParent->addChild($modelChild3, 'injectChild');

        $expect = "<span>value1</span><span><span>child3-1</span><span>child3-2</span>\n"
                  . "</span><span>child2-1</span><span>child2-2</span>\n\n";
        $result = $render->render($modelParent);

        $this->assertEquals($expect, $result);
    }

    public function testCallPlugin()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $result = $render->doctype();

        $this->assertInstanceOf(\Zend\View\Helper\Doctype::class, $result);
    }
}

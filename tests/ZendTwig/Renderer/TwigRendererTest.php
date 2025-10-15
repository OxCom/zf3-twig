<?php

namespace ZendTwig\Test\Renderer;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\View\Http\DefaultRenderingStrategy;
use Laminas\View\Model\ViewModel;
use ZendTwig\Test\Bootstrap;
use ZendTwig\Renderer\TwigRenderer;
use ZendTwig\View\TwigModel;

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
        $env = $sm->get(Environment::class);

        $this->assertSame($env, $render->getEnvironment());
    }

    public function testGetEnvEx()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);
        $renderClone = clone $render;

        $this->expectException(\Laminas\View\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Twig environment must be/');

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

        $this->assertInstanceOf('\Twig\Loader\ChainLoader', $loader);
    }

    public function testGetLoaderEx()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);
        $renderClone = clone $render;

        $this->expectException(\Laminas\View\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Twig loader must implement/');

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

    public function testGetView()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);
        $view   = $render->getView();

        $this->assertInstanceOf('\Laminas\View\View', $view);
    }

    public function testSetGetCanRenderTrees()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm           = Bootstrap::getInstance()->getServiceManager();
        $render       = $sm->get(TwigRenderer::class);

        $model = new ViewModel();
        $twigModel = new TwigModel();

        $this->assertFalse($render->canRenderTrees());
        $this->assertFalse($render->canRenderTrees('Element?'));
        $this->assertTrue($render->canRenderTrees($twigModel));
    }

    public function testRenderModelExNotTemplate()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $model = new ViewModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $this->expectException(\Laminas\View\Exception\DomainException::class);
        $this->expectExceptionMessageMatches('/but template is empty/');

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

        $model = new ViewModel([
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

    public function testRenderNotExistsEx()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm        = Bootstrap::getInstance()->getServiceManager();
        $render    = $sm->get(TwigRenderer::class);

        $this->expectException(\Laminas\View\Exception\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Unable to render template/');

        $render->render('View/testRenderNull', [
            'key1' => 'value1',
            'key2' => 'value2',
        ]);
    }

    public function testRenderChild()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $modelParent = new TwigModel([
            'key1' => 'value1',
        ]);
        $modelParent->setTemplate('View/testRenderChild');

        $modelChild1 = new TwigModel([
            'key1' => 'child1-1',
            'key2' => 'child1-2',
        ]);

        $modelChild2 = new TwigModel([
            'key1' => 'child2-1',
            'key2' => 'child2-2',
        ]);

        $modelChild3 = new TwigModel([
            'key1' => 'child3-1',
            'key2' => 'child3-2',
        ]);

        $modelChild1->setTemplate('View/testInjectResponse');
        $modelChild2->setTemplate('View/testInjectResponse');
        $modelChild3->setTemplate('View/testInjectResponse');

        $modelParent->addChild($modelChild1, 'injectChild');
        $modelParent->addChild($modelChild2);
        $modelParent->addChild($modelChild3, 'injectChild', true);

        // do not force standalone models
        $forceValue = $render->isForceStandalone();
        $render->setForceStandalone(false);

        $expect = "<span>value1</span><span><span>child1-1</span><span>child1-2</span>\n"
                    . "<span>child3-1</span><span>child3-2</span>\n"
                    . "</span><span>child2-1</span><span>child2-2</span>\n\n";
        $result = $render->render($modelParent);

        $render->setForceStandalone($forceValue);

        $this->assertEquals($expect, $result);
    }

    public function testRenderChildNoAppend()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $modelParent = new TwigModel([
            'key1' => 'value1',
        ]);
        $modelParent->setTemplate('View/testRenderChild');

        $modelChild1 = new TwigModel([
            'key1' => 'child1-1',
            'key2' => 'child1-2',
        ]);

        $modelChild2 = new TwigModel([
            'key1' => 'child2-1',
            'key2' => 'child2-2',
        ]);

        $modelChild3 = new TwigModel([
            'key1' => 'child3-1',
            'key2' => 'child3-2',
        ]);

        $modelChild1->setTemplate('View/testInjectResponse');
        $modelChild2->setTemplate('View/testInjectResponse');
        $modelChild3->setTemplate('View/testInjectResponse');

        $modelParent->addChild($modelChild1, 'injectChild');
        $modelParent->addChild($modelChild2);
        $modelParent->addChild($modelChild3, 'injectChild');

        // do not force standalone models
        $forceValue = $render->isForceStandalone();
        $render->setForceStandalone(false);

        $expect = "<span>value1</span><span><span>child3-1</span><span>child3-2</span>\n"
                  . "</span><span>child2-1</span><span>child2-2</span>\n\n";
        $result = $render->render($modelParent);

        $render->setForceStandalone($forceValue);

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

        $this->assertInstanceOf(\Laminas\View\Helper\Doctype::class, $result);
    }

    public function testForceStandaloneModel()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $modelParent = new TwigModel([
        ]);
        $modelParent->setTemplate('View/zend/layout');

        $modelChild1 = new TwigModel([
            'username' => 'Child007',
        ]);

        $modelChild1->setTemplate('View/zend/index');

        $modelParent->addChild($modelChild1);

        $expect = "<html><body><section>Hello, Child007!</section></body></html>\n";
        $result = $render->render($modelParent);

        $this->assertEquals($expect, $result);
    }

    public function testSimpleStandaloneModel()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $modelParent = new TwigModel([
        ]);
        $modelParent->setTemplate('View/zend/layout');

        $modelChild1 = new TwigModel([
            'username' => 'Child007',
        ]);

        $modelChild1->setTemplate('View/zend/index');
        $modelChild1->setTerminal(true);
        $modelParent->addChild($modelChild1);

        $forceStandalone = $render->isForceStandalone();
        $render->setForceStandalone(false);

        $expect = "<html><body><section>Hello, Child007!</section></body></html>\n";
        $result = $render->render($modelParent);

        $render->setForceStandalone($forceStandalone);

        $this->assertEquals($expect, $result);
    }

    public function testIssue2()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $modelParent = new TwigModel();
        $modelChild1 = new TwigModel();

        $modelParent->setTemplate('View/issue-2/layout');
        $modelChild1->setTemplate('View/issue-2/index');
        $modelChild1->setTerminal(true);
        $modelParent->addChild($modelChild1);

        $forceStandalone = $render->isForceStandalone();
        $render->setForceStandalone(false);

        $expect = "test header<section class=\"container\">test content</section>";
        $result = $render->render($modelParent);

        $render->setForceStandalone($forceStandalone);

        $this->assertEquals($expect, $result);
    }

    public function testIssue2Raw()
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $render
         */
        $sm     = Bootstrap::getInstance()->getServiceManager();
        $render = $sm->get(TwigRenderer::class);

        $modelParent = new TwigModel();
        $modelChild1 = new TwigModel();

        $modelParent->setTemplate('View/issue-2/layout-raw');
        $modelChild1->setTemplate('View/issue-2/index');
        $modelChild1->setTerminal(true);
        $modelParent->addChild($modelChild1);

        $forceStandalone = $render->isForceStandalone();
        $render->setForceStandalone(false);

        $expect = "test header<section class=\"container\">test content</section>";
        $result = $render->render($modelParent);

        $render->setForceStandalone($forceStandalone);

        $this->assertEquals($expect, $result);
    }
}

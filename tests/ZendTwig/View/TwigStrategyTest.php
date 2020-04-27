<?php

namespace ZendTwig\Test\View;

use PHPUnit\Framework\TestCase;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\ViewEvent;
use ZendTwig\Renderer\TwigRenderer;
use ZendTwig\Test\Bootstrap;
use ZendTwig\View\TwigModel;
use ZendTwig\View\TwigStrategy;

class TwigStrategyTest extends TestCase
{
    /**
     * Check that correct render was selected
     */
    public function testSelectRenderForce()
    {
        $model = $this->getMockBuilder('Laminas\View\Model\ModelInterface')->getMock();
        $model->method('getTemplate')
            ->will($this->returnValue('some-template-string'));

        /**
         * @var \Laminas\View\Model\ModelInterface $model
         */
        $event = new ViewEvent();
        $event->setModel($model);

        /**
         * @var \ZendTwig\View\TwigStrategy $strategy
         */
        $sm       = Bootstrap::getInstance()->getServiceManager();
        $strategy = $sm->get(TwigStrategy::class);
        $strategy->setForceRender(true);

        $renderA  = $sm->get(TwigRenderer::class);
        $renderB  = $strategy->selectRender($event);

        $this->assertSame($renderA, $renderB);
    }

    /**
     * Check that correct render was selected
     *
     * @var \Laminas\View\Model\ModelInterface $model
     *
     * @dataProvider generatorSelectRender
     */
    public function testSelectRenderNoRender($model, $expected)
    {
        $event = new ViewEvent();
        $event->setModel($model);

        /**
         * @var \ZendTwig\View\TwigStrategy $strategy
         */
        $sm       = Bootstrap::getInstance()->getServiceManager();
        $strategy = $sm->get(TwigStrategy::class);
        $strategy->setForceRender(false);
        $render  = $strategy->selectRender($event);

        if (!empty($expected)) {
            $expected = $sm->get($expected);
        }

        $this->assertSame($expected, $render);
    }

    /**
     * @return array
     */
    public function generatorSelectRender()
    {
        $viewModel = $this->getMockBuilder(ViewModel::class)->getMock();
        $viewModel->method('getTemplate')
            ->will($this->returnValue('some-template-string'));

        $jsonModel = $this->getMockBuilder(JsonModel::class)->getMock();
        $jsonModel->method('getTemplate')
            ->will($this->returnValue('some-template-string'));

        $twigModel = $this->getMockBuilder(TwigModel::class)->getMock();
        $twigModel->method('getTemplate')
            ->will($this->returnValue('some-template-string'));

        $viewModelTwig = new ViewModel();
        $viewModelTwig->setTemplate('layout');

        return [
            [$viewModel, null],
            [$jsonModel, null],
            [$twigModel, TwigRenderer::class],
            [$viewModelTwig, null],
        ];
    }

    /**
     * Check that response was injected
     *
     * @dataProvider generatorInjectResponse
     * @param $template
     * @param $expected
     */
    public function testInjectResponse($template, $expected)
    {
        $model    = new TwigModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $model->setTemplate($template);

        /**
         * @var \Laminas\View\View $view
         */
        $sm           = Bootstrap::getInstance()->getServiceManager();
        $strategyTwig = $sm->get(TwigStrategy::class);
        $view         = $sm->get('View');
        $request      = $sm->get('Request');
        $response     = $sm->get('Response');

        $e = $view->getEventManager();
        $strategyTwig->attach($e, 100);

        $view->setEventManager($e)
            ->setRequest($request)
            ->setResponse($response)
            ->render($model);

        $result = $view->getResponse()
            ->getContent();

        $this->assertEquals($expected, $result);
    }

    public function generatorInjectResponse()
    {
        return [
            ['View/testInjectResponse', "<span>value1</span><span>value2</span>\n"],
            ['View/testTreeRender', "<div>block</div>\n<div>content</div>\n"],
            ['layout', "9,800.33\n"],
        ];
    }

    /**
     * Check that response was injected but with wrong render
     */
    public function testInvalidInjectResponse()
    {
        $sm           = Bootstrap::getInstance()->getServiceManager();
        $phpRender    = $sm->get(PhpRenderer::class);
        $strategyTwig = new TwigStrategy($phpRender);
        $expected     = "<span>{{ key1 }}</span><span>{{ key2 }}</span>\n";
        $model        = new \Laminas\View\Model\ViewModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $model->setTemplate('View/testInjectResponse');

        $view         = $sm->get('View');
        $request      = $sm->get('Request');
        $response     = $sm->get('Response');

        $e = $view->getEventManager();
        $strategyTwig->attach($e, 100);
        $view->setEventManager($e)
            ->setRequest($request)
            ->setResponse($response)
            ->render($model);

        $result = $view->getResponse()
            ->getContent();

        $this->assertEquals($expected, $result);
    }
}

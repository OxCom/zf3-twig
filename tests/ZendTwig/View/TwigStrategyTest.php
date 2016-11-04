<?php

namespace ZendTwig\Test\View;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\View\ViewEvent;
use ZendTwig\Renderer\TwigRenderer;
use ZendTwig\Test\Bootstrap;
use ZendTwig\View\TwigStrategy;

class TwigStrategyTest extends TestCase
{
    /**
     * Check that correct render was selected
     */
    public function testSelectRenderer()
    {
        $model = $this->getMockBuilder('Zend\View\Model\ModelInterface')->getMock();
        $model->method('getTemplate')
            ->will($this->returnValue('some-template-string'));

        /**
         * @var \Zend\View\Model\ModelInterface $model
         */
        $event = new ViewEvent();
        $event->setModel($model);

        /**
         * @var \ZendTwig\View\TwigStrategy $strategy
         */
        $sm       = Bootstrap::getInstance()->getServiceManager();
        $strategy = $sm->get(TwigStrategy::class);
        $renderA  = $sm->get(TwigRenderer::class);
        $renderB  = $strategy->selectRender($event);

        $this->assertSame($renderA, $renderB);
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
        $model    = new \Zend\View\Model\ViewModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $model->setTemplate($template);

        /**
         * @var \Zend\View\View $view
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
        $expected     = "<span>value1</span><span>value2</span>\n";
        $model        = new \Zend\View\Model\ViewModel([
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

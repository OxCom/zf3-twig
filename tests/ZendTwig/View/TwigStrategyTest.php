<?php

namespace ZendTwig\Test\View;

use PHPUnit_Framework_TestCase as TestCase;
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
        $strategy = Bootstrap::getInstance()->getServiceManager()->get(TwigStrategy::class);
        $renderA  = Bootstrap::getInstance()->getServiceManager()->get(TwigRenderer::class);
        $renderB  = $strategy->selectRender($event);

        $this->assertSame($renderA, $renderB);
    }

    /**
     * Check that response was injected
     */
    public function testInjectResponse()
    {
        $template = '<span>{{ key1 }}</span><span>{{ key2 }}</span>';
        $expected   = '<span>value1</span><span>value2</span>';

        $model = new \Zend\View\Model\ViewModel([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $model->setTemplate('template-name');
        $event = new ViewEvent();
        $event->setModel($model);

        /**
         * @var \ZendTwig\View\TwigStrategy $strategy
         */
        $strategy = Bootstrap::getInstance()->getServiceManager()->get(TwigStrategy::class);
        $render   = $strategy->selectRender($event);

        $loaderMock = $this->getMockBuilder('\ZendTwig\Loader\MapLoader')->getMock();
        $loaderMock->method('exists')
            ->will($this->returnValue(true));

        $loaderMock->method('getSource')
            ->will($this->returnValue($template));

        /**
         * @var \Twig_Loader_Chain $loader
         */
        $loader = $render->getLoader();
        $loader->addLoader($loaderMock);

        $render->setLoader($loader);
        $result = $render->render($model);

        $this->assertEquals($expected, $result);
    }
}

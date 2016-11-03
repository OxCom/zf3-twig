<?php
namespace ZendTwig\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\View\Strategy\PhpRendererStrategy;
use ZendTwig\View\TwigStrategy;

class TwigStrategyFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TwigStrategy
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var \ZendTwig\Renderer\TwigRenderer $renderer
         * @var \Zend\View\View $view
         */
        $renderer = $container->get('ZendTwig\Renderer\TwigRenderer');
        $strategy = new TwigStrategy($renderer);

        return $strategy;
    }
}

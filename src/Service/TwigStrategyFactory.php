<?php
namespace ZendTwig\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use ZendTwig\Renderer\TwigRenderer;
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
        $renderer = $container->get(TwigRenderer::class);
        $strategy = new TwigStrategy($renderer);

        return $strategy;
    }
}

<?php
namespace ZendTwig\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use ZendTwig\Module;
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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : TwigStrategy
    {
        $config      = $container->get('Configuration');
        $name        = Module::MODULE_NAME;
        $options     = $envOptions = empty($config[$name]) ? [] : $config[$name];

        /**
         * @var \ZendTwig\Renderer\TwigRenderer $renderer
         * @var \Zend\View\View $view
         */
        $renderer = $container->get(\ZendTwig\Renderer\TwigRenderer::class);
        $strategy = new TwigStrategy($renderer);

        $forceStrategy = !empty($options['force_twig_strategy']);
        $strategy->setForceRender($forceStrategy);

        return $strategy;
    }
}

<?php
namespace ZendTwig\Service;

use Twig_Environment;
use ZendTwig\Renderer\TwigRenderer;
use ZendTwig\Resolver\TwigResolver;
use ZendTwig\View\HelperPluginManager as TwigHelperPluginManager;

use Interop\Container\ContainerInterface;
use Zend\View\View;
use Zend\ServiceManager\Factory\FactoryInterface;

class TwigRendererFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TwigRenderer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var Twig_Environment $env
         */
        $env      = $container->get(Twig_Environment::class);
        $renderer = new TwigRenderer(
            $container->get(View::class),
            $env,
            $container->get(TwigResolver::class)
        );

        $renderer->setTwigHelpers($container->get(TwigHelperPluginManager::class));
        $renderer->setZendHelpers($container->get('ViewHelperManager'));

        return $renderer;
    }
}

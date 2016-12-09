<?php
namespace ZendTwig\Service;

use Twig_Environment;
use ZendTwig\Module;
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
        $config      = $container->get('Configuration');
        $name        = Module::MODULE_NAME;
        $options     = empty($config[$name]) ? [] : $config[$name];

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

        if (!empty($options['force_standalone'])) {
            $renderer = $renderer->setForceStandalone(true);
        }

        return $renderer;
    }
}

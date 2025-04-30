<?php
namespace ZendTwig\Service;

use Twig\Environment;
use ZendTwig\Module;
use ZendTwig\Renderer\TwigRenderer;
use ZendTwig\Resolver\TwigResolver;
use ZendTwig\View\HelperPluginManager as TwigHelperPluginManager;

use Interop\Container\ContainerInterface;
use Laminas\View\View;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TwigRendererFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TwigRenderer
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : TwigRenderer
    {
        $config      = $container->get('Configuration');
        $name        = Module::MODULE_NAME;
        $options     = empty($config[$name]) ? [] : $config[$name];

        /**
         * @var Environment $env
         */
        $env      = $container->get(Environment::class);
        $view     = $container->get(View::class);
        $resolver = $container->get(TwigResolver::class);
        $renderer = new TwigRenderer($view, $env, $resolver);

        $renderer->setTwigHelpers($container->get(TwigHelperPluginManager::class));
        $renderer->setZendHelpers($container->get('ViewHelperManager'));

        if (!empty($options['force_standalone'])) {
            $renderer = $renderer->setForceStandalone(true);
        }

        return $renderer;
    }
}

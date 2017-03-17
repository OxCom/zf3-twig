<?php
namespace ZendTwig;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\View\Exception\InvalidArgumentException;
use ZendTwig\Renderer\TwigRenderer;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    const MODULE_NAME = 'zend_twig';

    /**
     * Listen to the bootstrap event
     *
     * @param \Zend\Mvc\MvcEvent|EventInterface $e
     *
     * @return array|void
     */
    public function onBootstrap(EventInterface $e)
    {
        $app       = $e->getApplication();
        $container = $app->getServiceManager();

        /**
         * @var \Twig_Environment $env
         */
        $config      = $container->get('Configuration');
        $env         = $container->get(\Twig_Environment::class);
        $name        = static::MODULE_NAME;
        $options     = $envOptions = empty($config[$name]) ? [] : $config[$name];
        $extensions  = empty($options['extensions']) ? [] : $options['extensions'];
        $renderer    = $container->get(TwigRenderer::class);

        // Setup extensions
        foreach ($extensions as $extension) {
            // Allows modules to override/remove extensions.
            if (empty($extension)) {
                continue;
            } elseif (is_string($extension)) {
                if ($container->has($extension)) {
                    $extension = $container->get($extension);
                } else {
                    $extension = new $extension($container, $renderer);
                }
            } elseif (!is_object($extension)) {
                throw new InvalidArgumentException('Extensions should be a string or object.');
            }

            if (!$env->hasExtension(get_class($extension))) {
                $env->addExtension($extension);
            }
        }

        return;
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}

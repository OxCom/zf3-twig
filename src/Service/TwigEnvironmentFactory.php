<?php
namespace ZendTwig\Service;

use Twig_Environment;
use ZendTwig\Module;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Exception\InvalidArgumentException;
use ZendTwig\View\FallbackFunction;

class TwigEnvironmentFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Twig_Environment
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config      = $container->get('Configuration');
        $name        = Module::MODULE_NAME;
        $options     = $envOptions = empty($config[$name]) ? [] : $config[$name];
        $envOptions  = empty($options['environment']) ? [] : $options['environment'];
        $loader      = $container->get('Twig_Loader_Chain');
        $env         = new Twig_Environment($loader, $envOptions);
        $extensions  = empty($options['extensions']) ? [] : $options['extensions'];
        $zendHelpers = empty($options['helpers']['invoke_zend']) ? false : (bool)$options['helpers']['invoke_zend'];

        if ($zendHelpers) {
            $helperPluginManager = $container->get('ViewHelperManager');
            $env->registerUndefinedFunctionCallback(
                function ($name) use ($helperPluginManager) {
                    if ($helperPluginManager->has($name)) {
                        return new FallbackFunction($name);
                    }

                    $name = strtolower('zendviewhelper' . $name);
                    if ($helperPluginManager->has($name)) {
                        return new FallbackFunction($name);
                    }

                    return false;
                }
            );
        }

        // Setup extensions
        foreach ($extensions as $extension) {
            // Allows modules to override/remove extensions.
            if (empty($extension)) {
                continue;
            } elseif (is_string($extension)) {
                if ($container->has($extension)) {
                    $extension = $container->get($extension);
                } else {
                    $extension = new $extension();
                }
            } elseif (!is_object($extension)) {
                throw new InvalidArgumentException('Extensions should be a string or object.');
            }

            $env->addExtension($extension);
        }

        return $env;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Twig_Environment
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Twig_Environment::class);
    }
}
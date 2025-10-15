<?php
namespace ZendTwig\Service;

use Twig\Environment;
use ZendTwig\Module;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ZendTwig\View\FallbackFunction;
use ZendTwig\View\HelperPluginManager;

class TwigEnvironmentFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Environment
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : Environment
    {
        $config      = $container->get('Configuration');
        $name        = Module::MODULE_NAME;
        $options     = $envOptions = empty($config[$name]) ? [] : $config[$name];
        $envOptions  = empty($options['environment']) ? [] : $options['environment'];
        $loader      = $container->get(\Twig\Loader\ChainLoader::class);
        $env         = new Environment($loader, $envOptions);
        $zendHelpers = !isset($options['invoke_zend_helpers']) ? false : (bool)$options['invoke_zend_helpers'];

        if ($zendHelpers) {
            $twigHelperPluginManager = $container->get(HelperPluginManager::class);
            $zendHelperPluginManager = $container->get('ViewHelperManager');
            $env->registerUndefinedFunctionCallback(
                function ($name) use ($twigHelperPluginManager, $zendHelperPluginManager) {
                    if ($twigHelperPluginManager->has($name) || $zendHelperPluginManager->has($name)) {
                        return FallbackFunction::build($name);
                    }

                    $name = strtolower('zendviewhelper' . $name);
                    if ($zendHelperPluginManager->has($name)) {
                        return FallbackFunction::build($name);
                    }

                    return false;
                }
            );
        }

        return $env;
    }
}

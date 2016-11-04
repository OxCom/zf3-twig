<?php
namespace ZendTwig\Service;

use Twig_Environment;
use ZendTwig\Module;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
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
        $zendHelpers = !isset($options['invoke_zend_helpers']) ? false : (bool)$options['invoke_zend_helpers'];

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

        return $env;
    }
}

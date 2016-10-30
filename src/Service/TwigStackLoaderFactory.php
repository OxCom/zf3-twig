<?php

namespace ZendTwig\Service;

use ZendTwig\Loader\StackLoader;
use ZendTwig\Module;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class TwigStackLoaderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return \Zend\View\Resolver\TemplatePathStack
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config  = $container->get('Configuration');
        $name    = Module::MODULE_NAME;
        $options = $envOptions = empty($config[$name]) ? [] : $config[$name];
        $suffix  = empty($options['suffix']) ? TwigLoaderFactory::DEFAULT_SUFFIX : $options['suffix'];

        /** @var \Zend\View\Resolver\TemplatePathStack $zfStack */
        $zfStack = $container->get('ViewTemplatePathStack');

        $loader = new StackLoader($zfStack->getPaths()->toArray());
        $loader->setSuffix($suffix);

        return $loader;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StackLoader
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, StackLoader::class);
    }
}
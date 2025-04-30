<?php

namespace ZendTwig\Service;

use ZendTwig\Loader\StackLoader;
use ZendTwig\Module;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TwigStackLoaderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return \ZendTwig\Loader\StackLoader
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : StackLoader
    {
        $config  = $container->get('Configuration');
        $name    = Module::MODULE_NAME;
        $options = $envOptions = empty($config[$name]) ? [] : $config[$name];
        $suffix  = empty($options['suffix']) ? TwigLoaderFactory::DEFAULT_SUFFIX : $options['suffix'];

        /** @var \Laminas\View\Resolver\TemplatePathStack $zfStack */
        $zfStack = $container->get('ViewTemplatePathStack');

        $loader = new StackLoader($zfStack->getPaths()->toArray());
        $loader->setSuffix($suffix);

        return $loader;
    }
}

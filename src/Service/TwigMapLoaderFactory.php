<?php

namespace ZendTwig\Service;

use ZendTwig\Module;
use ZendTwig\Loader\MapLoader;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TwigMapLoaderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return MapLoader
     * @throws \Twig_Error_Loader
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config  = $container->get('Configuration');
        $name    = Module::MODULE_NAME;
        $options = $envOptions = empty($config[$name]) ? [] : $config[$name];
        $suffix  = empty($options['suffix']) ? TwigLoaderFactory::DEFAULT_SUFFIX : $options['suffix'];

        /** @var \Zend\View\Resolver\TemplateMapResolver $zfMap */
        $zfMap = $container->get('ViewTemplateMapResolver');

        $loader = new MapLoader();
        foreach ($zfMap as $name => $path) {
            if ($suffix == pathinfo($path, PATHINFO_EXTENSION)) {
                $loader->add($name, $path);
            }
        }

        return $loader;
    }
}

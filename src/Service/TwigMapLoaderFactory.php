<?php

namespace ZendTwig\Service;

use ZendTwig\Module;
use ZendTwig\Loader\MapLoader;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TwigMapLoaderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return MapLoader
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : MapLoader
    {
        $config  = $container->get('Configuration');
        $name    = Module::MODULE_NAME;
        $options = $envOptions = empty($config[$name]) ? [] : $config[$name];
        $suffix  = empty($options['suffix']) ? TwigLoaderFactory::DEFAULT_SUFFIX : $options['suffix'];

        /** @var \Laminas\View\Resolver\TemplateMapResolver $zfMap */
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

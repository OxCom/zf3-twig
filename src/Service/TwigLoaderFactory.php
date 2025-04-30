<?php
namespace ZendTwig\Service;

use Twig\Loader\ChainLoader;
use ZendTwig\Module;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Exception\InvalidArgumentException;

class TwigLoaderFactory implements FactoryInterface
{
    const DEFAULT_SUFFIX = 'twig';

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return ChainLoader
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : ChainLoader
    {
        $config  = $container->get('Configuration');
        $name    = Module::MODULE_NAME;
        $options = $envOptions = empty($config[$name]) ? [] : $config[$name];
        $list    = empty($options['loader_chain']) ? [] : $options['loader_chain'];
        $chain   = new ChainLoader();

        foreach ($list as $loader) {
            if (!is_string($loader) || !$container->has($loader)) {
                throw new InvalidArgumentException('Loaders should be a service manager alias.');
            }

            $chain->addLoader($container->get($loader));
        }

        return $chain;
    }
}

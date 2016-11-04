<?php
namespace ZendTwig\Service;

use Twig_Environment;
use Twig_Loader_Chain;
use ZendTwig\Module;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Exception\InvalidArgumentException;

class TwigLoaderFactory implements FactoryInterface
{
    const DEFAULT_SUFFIX = 'twig';

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Twig_Loader_Chain
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config  = $container->get('Configuration');
        $name    = Module::MODULE_NAME;
        $options = $envOptions = empty($config[$name]) ? [] : $config[$name];
        $list    = empty($options['loader_chain']) ? [] : $options['loader_chain'];
        $chain   = new Twig_Loader_Chain();

        foreach ($list as $loader) {
            if (!is_string($loader) || !$container->has($loader)) {
                throw new InvalidArgumentException('Loaders should be a service manager alias.');
            }

            $chain->addLoader($container->get($loader));
        }

        return $chain;
    }
}

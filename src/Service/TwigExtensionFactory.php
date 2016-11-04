<?php

namespace ZendTwig\Service;

use ZendTwig\Renderer\TwigRenderer;
use ZendTwig\Extension\Extension;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TwigExtensionFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Extension
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName($container, $container->get(TwigRenderer::class));
    }
}

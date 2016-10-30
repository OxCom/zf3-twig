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
        return new Extension($container->get(TwigRenderer::class));
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Extension
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Extension::class);
    }


}
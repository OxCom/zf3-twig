<?php

return [
    'view_manager'    => [
        'strategies' => [
            'ZendTwig\View\TwigStrategy',
        ],
    ],
    'service_manager' => [
        'factories' => [
            \ZendTwig\View\TwigStrategy::class        => \ZendTwig\Service\TwigStrategyFactory::class,
            \ZendTwig\View\HelperPluginManager::class => \ZendTwig\Service\TwigHelperPluginManagerFactory::class,

            \ZendTwig\Renderer\TwigRenderer::class    => \ZendTwig\Service\TwigRendererFactory::class,
            \ZendTwig\Resolver\TwigResolver::class    => \ZendTwig\Service\TwigResolverFactory::class,

            \Twig_Environment::class                  => \ZendTwig\Service\TwigEnvironmentFactory::class,
            \Twig_Loader_Chain::class                 => \ZendTwig\Service\TwigLoaderFactory::class,

            \ZendTwig\Loader\MapLoader::class         => \ZendTwig\Service\TwigMapLoaderFactory::class,
            \ZendTwig\Loader\StackLoader::class       => \ZendTwig\Service\TwigStackLoaderFactory::class,
        ],
    ],
    'zend_twig'       => [
        'environment'  => [
        ],
        'loader_chain' => [
            \ZendTwig\Loader\MapLoader::class,
            \ZendTwig\Loader\StackLoader::class
        ],
        'extensions'   => [

        ],
        'helpers'      => [
            'invoke_zend' => true,
            'configs'     => [
                \Zend\Navigation\View\HelperConfig::class,
            ]
        ],
    ],
];

<?php

return [
    'view_manager' => [
        'strategies' => [
            ZendTwig\View\TwigStrategy::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            \ZendTwig\View\TwigStrategy::class => \ZendTwig\Service\TwigStrategyFactory::class,
            \ZendTwig\View\HelperPluginManager::class => \ZendTwig\Service\TwigHelperPluginManagerFactory::class,
            \ZendTwig\Renderer\TwigRenderer::class => \ZendTwig\Service\TwigRendererFactory::class,
            \ZendTwig\Resolver\TwigResolver::class => \ZendTwig\Service\TwigResolverFactory::class,
            \Twig_Environment::class => \ZendTwig\Service\TwigEnvironmentFactory::class,
            \Twig_Loader_Chain::class => \ZendTwig\Service\TwigLoaderFactory::class,
            \ZendTwig\Loader\MapLoader::class => \ZendTwig\Service\TwigMapLoaderFactory::class,
            \ZendTwig\Loader\StackLoader::class => \ZendTwig\Service\TwigStackLoaderFactory::class,
        ],
    ],
    'zend_twig' => [
        'suffix' => \ZendTwig\Service\TwigLoaderFactory::DEFAULT_SUFFIX,
        /**
         * In a ZF3 by default we have this structure:
         *  - ViewModel with template from 'layout/layout'
         *  - ViewModel as child with action template 'application/index/index'
         * In that case we should always force standalone state of child models
         */
        'force_standalone' => true,
        /**
         * Force Your application to use TwigRender for ViewModel.
         * If false, then TwigStrategy will be applied only for TwigModel
         *
         * @note: In release v.1.5 this parameter will be set to false
         */
        'force_twig_strategy' => true,
        /**
         * Developer can disable Zend View Helpers like docType, translate and e.t.c.
         */
        'invoke_zend_helpers' => true,
        /**
         * Twig Environment settings
         */
        'environment' => [
        ],
        /**
         * Default loaders for views
         */
        'loader_chain' => [
            \ZendTwig\Loader\MapLoader::class,
            \ZendTwig\Loader\StackLoader::class,
        ],
        /**
         * List of Twig Extensions
         */
        'extensions' => [
            \ZendTwig\Extension\Extension::class,
        ],
        'helpers' => [
            'configs' => [
                /**
                 * Here can be declared configuration classes for service manager:
                 *  \Zend\Form\View\HelperConfig::class,
                 *  \Zend\I18n\View\HelperConfig::class,
                 *  \Zend\Navigation\View\HelperConfig::class,
                 */
            ],
        ],
    ],
];

<?php

use ZendTwig\Service\TwigLoaderFactory;

return [
    'view_manager'    => [
        'template_path_stack'     => [
            'ZendTwig' => __DIR__ . '/../../view/ZendTwig',
        ],
        'default_template_suffix' => TwigLoaderFactory::DEFAULT_SUFFIX,
    ],
    'service_manager' => [
        'factories' => [
            \Twig_Extension_Debug::class => \ZendTwig\Service\TwigExtensionFactory::class,
        ],
    ],
    'zend_twig'       => [
        'environment' => [
            'debug' => true,
        ],
        'extensions'  => [
            \Twig_Extension_Debug::class,
        ],
    ],
];

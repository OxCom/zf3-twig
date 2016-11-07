<?php

return [
    'view_manager' => [
        'template_path_stack'     => [
            'ZendTwig' => __DIR__ . '/../../view/ZendTwig',
        ],
        'default_template_suffix' => \ZendTwig\Service\TwigLoaderFactory::DEFAULT_SUFFIX,
    ],
    'service_manager' => [
        'factories' => [
            \ZendTwig\Test\Fixture\Extension\InstanceOfExtension::class => \ZendTwig\Service\TwigExtensionFactory::class,
        ],
    ],
    'zend_twig'       => [
        'extensions' => [
            \ZendTwig\Test\Fixture\Extension\InstanceOfExtension::class,
        ],
    ],
];

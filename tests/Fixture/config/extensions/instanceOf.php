<?php

use ZendTwig\Service\TwigLoaderFactory;
use ZendTwig\Test\Fixture\Extension\InstanceOfExtension;

return [
    'view_manager'    => [
        'template_path_stack'     => [
            'ZendTwig' => __DIR__ . '/../../view/ZendTwig',
        ],
        'default_template_suffix' => TwigLoaderFactory::DEFAULT_SUFFIX,
    ],
    'service_manager' => [
        'factories' => [
            InstanceOfExtension::class => \ZendTwig\Service\TwigExtensionFactory::class,
        ],
    ],
    'zend_twig'       => [
        'extensions' => [
            InstanceOfExtension::class,
        ],
    ],
];

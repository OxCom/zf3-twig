<?php

return [
    'service_manager' => [
        'factories' => [
            \ZendTwig\Test\Fixture\DummyClass::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack'     => [
            'ZendTwig' => __DIR__ . '/../../view/ZendTwig',
        ],
        'default_template_suffix' => \ZendTwig\Service\TwigLoaderFactory::DEFAULT_SUFFIX,
    ],
    'zend_twig'       => [
        'helpers' => [
            'configs' => [
                \Zend\Navigation\View\HelperConfig::class,
                \ZendTwig\Test\Fixture\DummyClass::class,
            ],
        ],
    ],
];

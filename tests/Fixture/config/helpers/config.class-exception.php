<?php

use Laminas\Navigation\View\HelperConfig;
use Laminas\ServiceManager\Factory\InvokableFactory;
use ZendTwig\Service\TwigLoaderFactory;
use ZendTwig\Test\Fixture\DummyClass;
use ZendTwig\Test\Fixture\DummyClassInvokable;

return [
    'service_manager' => [
        'factories' => [
            DummyClassInvokable::class => InvokableFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_path_stack'     => [
            'ZendTwig' => __DIR__ . '/../../view/ZendTwig',
        ],
        'default_template_suffix' => TwigLoaderFactory::DEFAULT_SUFFIX,
    ],
    'zend_twig'       => [
        'helpers' => [
            'configs' => [
                HelperConfig::class,
                DummyClassInvokable::class,
                [
                    'aliases'    => [],
                    'delegators' => [],
                    'factories'  => [],
                ],
                // will \ZendTwig\Service\TwigHelperPluginManagerFactory
                DummyClass::class,
            ],
        ],
    ],
];

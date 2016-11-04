<?php

return [
    'service_manager' => [
        'factories' => [
            \ZendTwig\Test\Fixture\Extension\DummyExtension::class => \ZendTwig\Service\TwigExtensionFactory::class,
        ],
    ],
    'zend_twig'       => [
        'extensions' => [
            \ZendTwig\Test\Fixture\Extension\DummyExtension::class,
        ],
    ],
];

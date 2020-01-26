<?php

use Twig\Extension\DebugExtension;
use ZendTwig\Service\TwigLoaderFactory;

return [
    'view_manager' => [
        'template_path_stack'     => [
            'ZendTwig' => __DIR__ . '/../../view/ZendTwig',
        ],
        'default_template_suffix' => TwigLoaderFactory::DEFAULT_SUFFIX,
    ],
    'zend_twig'    => [
        'environment' => [
            'debug' => true,
        ],
        'extensions'  => [
            DebugExtension::class,
        ],
    ],
];

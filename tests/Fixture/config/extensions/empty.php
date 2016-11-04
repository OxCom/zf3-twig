<?php

return [
    'view_manager' => [
        'template_path_stack'     => [
            'ZendTwig' => __DIR__ . '/../../view/ZendTwig',
        ],
        'default_template_suffix' => \ZendTwig\Service\TwigLoaderFactory::DEFAULT_SUFFIX,
    ],
    'zend_twig'       => [
        'extensions'   => [
            null,
        ],
    ],
];

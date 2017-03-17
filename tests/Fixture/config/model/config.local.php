<?php

return [
    'zend_twig' => [
        'force_twig_strategy' => false,
    ],
    'view_manager' => [
        'doctype'                 => \Zend\View\Helper\Doctype::HTML5,
        'template_map' => array (
            'layout' => __DIR__ . '/../../view/Map/layout.twig',
        ),
        'template_path_stack'     => [
            'ZendTwig' => __DIR__ . '/../../view/ZendTwig',
        ],
    ],
];

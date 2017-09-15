<?php

use ZendTwig\Test\Fixture\View\Helper\Menu;
use ZendTwig\Test\Fixture\View\Helper\MenuInvoke;

return [
    'service_manager' => [
        'factories' => [
            \ZendTwig\Test\Fixture\DummyClassInvokable::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_path_stack'     => [
            'ZendTwig' => __DIR__ . '/../../view/ZendTwig',
        ],
        'default_template_suffix' => \ZendTwig\Service\TwigLoaderFactory::DEFAULT_SUFFIX,
    ],
    'zend_twig'       => [
        'helpers' => [
            'configs' => [],
        ],
    ],
    'view_helpers'    => [
        'factories' => [
            Menu::class       => \ZendTwig\Test\Fixture\View\Factory\MenuFactory::class,
            MenuInvoke::class => \ZendTwig\Test\Fixture\View\Factory\MenuFactory::class,
        ],
        'aliases'   => [
            'mainMenu'       => Menu::class,
            'mainMenuInvoke' => MenuInvoke::class,
        ],
    ],
];

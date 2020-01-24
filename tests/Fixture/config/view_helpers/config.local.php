<?php

use Laminas\ServiceManager\Factory\InvokableFactory;
use ZendTwig\Service\TwigLoaderFactory;
use ZendTwig\Test\Fixture\DummyClassInvokable;
use ZendTwig\Test\Fixture\View\Factory\MenuFactory;
use ZendTwig\Test\Fixture\View\Helper\Menu;
use ZendTwig\Test\Fixture\View\Helper\MenuInvoke;

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
            'configs' => [],
        ],
    ],
    'view_helpers'    => [
        'factories' => [
            Menu::class       => MenuFactory::class,
            MenuInvoke::class => MenuFactory::class,
        ],
        'aliases'   => [
            'mainMenu'       => Menu::class,
            'mainMenuInvoke' => MenuInvoke::class,
        ],
    ],
];

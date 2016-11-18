# ZendTwig module for Zend Framework 3
[![codecov.io](https://codecov.io/github/OxCom/zf3-twig/coverage.svg?branch=master)](https://codecov.io/github/OxCom/zf3-twig?branch=master)
[![Build Status](https://travis-ci.org/OxCom/zf3-twig.svg?branch=master)](https://travis-ci.org/OxCom/zf3-twig)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

ZendTwig is a module that integrates the [Twig](https://github.com/twigphp/Twig) template engine with [Zend Framework 3](https://github.com/zendframework/zendframework).

## Thanks
Thanks for [ZF-Common](https://github.com/ZF-Commons) for good ideas.

## Install

1. Add ZendTwig lib with composer: ``` composer require oxcom/zend-twig ``` 
2. Add ZendTwig to Your ``` config/application.config.php ``` file as module:
```php
    // Retrieve list of modules used in this application.
    'modules'                 => [
        'Zend\Router',
        'Zend\Validator',
        'Zend\I18n',
        'Zend\Mvc\I18n',
        'Application',
        // ...
        'ZendTwig',
    ],
```

## Setting up

[Here](https://github.com/OxCom/zf3-twig/tree/master/docs) You can find some examples, configurations and e.t.c. that, I hope, will help You do build Your application.
Short list of available chapters:

    1. ZendTwig module 
    2. Custom Twig Extensions

## Bugs and Issues

Please, if You found a bug or something, that is not working properly, contact me and tell what's wrong. It's nice to have an example how to reproduce a bug, or any idea how to fix it in Your request. I'll take care about it ASAP.

## TODO
    1. More docs
    3. More examples


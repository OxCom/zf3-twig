# ZendTwig module

The module configuration should be done in scope of ``` zend_twig ``` key. Default configuration is:
```php
    'zend_twig'       => [
        'environment'         => [
        ],
        'loader_chain'        => [
            \ZendTwig\Loader\MapLoader::class,
            \ZendTwig\Loader\StackLoader::class,
        ],
        'extensions'          => [
            \ZendTwig\Extension\Extension::class,
        ],
        'invoke_zend_helpers' => true,
        'helpers'             => [
            'configs' => [
                \Zend\Navigation\View\HelperConfig::class,
            ],
        ],
    ],
```

## Key: environment
There should be done configuration of Twig environment. List of available options can be found [here](http://twig.sensiolabs.org/doc/api.html#environment-options).


## Key: loader_chain
In current module You can set Your own template loaders. 
This loaders will be added to the [Twig_Loader_Chain](http://twig.sensiolabs.org/doc/api.html#twig-loader-chain). So, developer will be able to add custom loaders.
All loaders should be available from service manager.

## Key: extensions
Developer can inject custom extensions into Twig. For new extension developer should extends his extension from ``` \ZendTwig\Extension\AbstractExtension ```
and should be available from service manager by ``` \ZendTwig\Service\TwigExtensionFactory::class ```. Example is here: [Here](https://github.com/OxCom/zf3-twig/tree/master/docs/Extensions.md)

Please, read more info about [Twig Extensions](http://twig.sensiolabs.org/doc/advanced.html#creating-an-extension).

## Key: invoke_zend_helpers
Developer can disable Zend View Helpers like docType, translate and e.t.c. Value: ``` true ``` or ``` false ```. Default: ``` true ```
This is done with ``` \ZendTwig\View\FallbackFunction ```

## Key: helpers
Configuration for Zend View Helpers

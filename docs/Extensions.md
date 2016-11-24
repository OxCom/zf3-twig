# Extensions
Define Your new extensions for twig this ``` service_manager ``` configuration:
```php
    'service_manager' => [
        'factories' => [
            \ZendTwig\Test\Fixture\Extension\InstanceOfExtension::class => \ZendTwig\Service\TwigExtensionFactory::class,
        ],
    ],
    'zend_twig'       => [
        'extensions' => [
            \ZendTwig\Test\Fixture\Extension\InstanceOfExtension::class,
        ],
    ],
```

Extension class example:

```php
    class NewExtension extends \ZendTwig\Extension\AbstractExtension
    {
        /**
         * Common code for Twig Extensions
         */
    }
```

After that Your extension will be loaded as Twig extensions.

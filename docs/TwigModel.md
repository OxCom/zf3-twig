# TwigModel
Zend Framework has interesting approach how to get correct renderer. 
By default flow developer has something like this in controllers
```php
public function indexAction()
{
    // \Zend\View\Model\ViewModel
    return new ViewModel([
        'foo' => 'bar',
    ]);
}
```
In this case ZF will render template with PhpRenderer.


When developer would like to send JSON response, then we can change to
```php
public function indexAction()
{
    // \Zend\View\Model\JsonModel
    return new JsonModel([
        'foo' => 'bar',
    ]);
}
```

Now, with this extension developer can use ```TwigModel``` to render Twig templates with TwigRenderer.
```php
public function indexAction()
{
    // \ZendTwig\View\TwigModel
    return new TwigModel([
        'foo' => 'bar',
    ]);
}
``` 

*NOTE*: To have described behaviour, please, set ```'force_twig_strategy'``` to ```false```
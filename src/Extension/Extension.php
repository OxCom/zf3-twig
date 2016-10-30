<?php

namespace ZendTwig\Extension;

use Twig_Extension;
use ZendTwig\Module;
use ZendTwig\Renderer\TwigRenderer;

class Extension extends Twig_Extension
{
    /**
     * @var TwigRenderer
     */
    protected $renderer;

    /**
     * @param \ZendTwig\Renderer\TwigRenderer $renderer
     */
    public function __construct(TwigRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return \Zend\View\HelperPluginManager
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return Module::MODULE_NAME;
    }
}
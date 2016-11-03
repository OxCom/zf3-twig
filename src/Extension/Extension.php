<?php

namespace ZendTwig\Extension;

use Twig_Extension;
use ZendTwig\Module;
use ZendTwig\Renderer\TwigRenderer;

class Extension extends Twig_Extension
{
    /**
     * @var \ZendTwig\Renderer\TwigRenderer
     */
    protected $renderer;

    /**
     * @param \ZendTwig\Renderer\TwigRenderer $renderer
     */
    public function __construct(TwigRenderer $renderer = null)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return \ZendTwig\Renderer\TwigRenderer
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

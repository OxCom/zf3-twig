<?php

namespace ZendTwig\Extension;

use Twig_Extension;
use Interop\Container\ContainerInterface;
use ZendTwig\Module;
use ZendTwig\Renderer\TwigRenderer;

class Extension extends Twig_Extension
{
    /**
     * @var \ZendTwig\Renderer\TwigRenderer
     */
    protected $renderer;

    /**
     * @var \Interop\Container\ContainerInterface
     */
    protected $serviceManager;

    /**
     * @param \Interop\Container\ContainerInterface $serviceManager
     * @param \ZendTwig\Renderer\TwigRenderer       $renderer
     */
    public function __construct(ContainerInterface $serviceManager, TwigRenderer $renderer = null)
    {
        $this->serviceManager = $serviceManager;
        $this->renderer       = $renderer;
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

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}

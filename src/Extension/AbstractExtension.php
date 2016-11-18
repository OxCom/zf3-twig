<?php

namespace ZendTwig\Extension;

use Twig_Extension;
use Interop\Container\ContainerInterface;
use ZendTwig\Renderer\TwigRenderer;

abstract class AbstractExtension extends Twig_Extension
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
    abstract public function getRenderer();

    /**
     * @return \Interop\Container\ContainerInterface
     */
    abstract public function getServiceManager();
}

<?php

namespace ZendTwig\Extension;

use Interop\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use ZendTwig\Renderer\TwigRenderer;

abstract class AbstractExtension extends AbstractExtension
{
    /**
     * @var TwigRenderer
     */
    protected $renderer;

    /**
     * @var ContainerInterface
     */
    protected $serviceManager;

    /**
     * @param ContainerInterface $serviceManager
     * @param TwigRenderer       $renderer
     */
    public function __construct(ContainerInterface $serviceManager, ?TwigRenderer $renderer = null)
    {
        $this->serviceManager = $serviceManager;
        $this->renderer       = $renderer;
    }

    /**
     * @return TwigRenderer
     */
    abstract public function getRenderer();

    /**
     * @return ContainerInterface
     */
    abstract public function getServiceManager();
}

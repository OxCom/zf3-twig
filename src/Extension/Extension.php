<?php

namespace ZendTwig\Extension;

use Interop\Container\ContainerInterface;
use ZendTwig\Renderer\TwigRenderer;

class Extension extends AbstractExtension
{
    /**
     * @return TwigRenderer
     */
    public function getRenderer() : TwigRenderer
    {
        return $this->renderer;
    }

    /**
     * @return ContainerInterface
     */
    public function getServiceManager() : ContainerInterface
    {
        return $this->serviceManager;
    }
}

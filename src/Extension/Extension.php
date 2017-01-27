<?php

namespace ZendTwig\Extension;

use Interop\Container\ContainerInterface;
use ZendTwig\Renderer\TwigRenderer;

class Extension extends AbstractExtension
{
    /**
     * @return \ZendTwig\Renderer\TwigRenderer
     */
    public function getRenderer() : TwigRenderer
    {
        return $this->renderer;
    }

    /**
     * @return \Interop\Container\ContainerInterface
     */
    public function getServiceManager() : ContainerInterface
    {
        return $this->serviceManager;
    }
}

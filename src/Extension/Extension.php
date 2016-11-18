<?php

namespace ZendTwig\Extension;

class Extension extends AbstractExtension
{
    /**
     * @return \ZendTwig\Renderer\TwigRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return \Interop\Container\ContainerInterface
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}

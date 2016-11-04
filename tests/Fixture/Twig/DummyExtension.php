<?php

namespace ZendTwig\Test\Fixture\Extension;

use ZendTwig\Extension\Extension;

class DummyExtension extends Extension
{
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
        return parent::getName() . '::dummy';
    }
}

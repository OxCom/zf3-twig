<?php
namespace ZendTwig\Resolver;

use Twig_Environment;

use Zend\View\Resolver\ResolverInterface;
use Zend\View\Renderer\RendererInterface as Renderer;

class TwigResolver implements ResolverInterface
{
    /**
     * @var Twig_Environment
     */
    protected $environment;

    /**
     * Constructor.
     *
     * @param Twig_Environment $environment
     */
    public function __construct(Twig_Environment $environment = null)
    {
        $this->environment = $environment;
    }

    /**
     * Resolve a template/pattern name to a resource the renderer can consume
     *
     * @param  string        $name
     * @param  null|Renderer $renderer
     *
     * @return string
     */
    public function resolve($name, Renderer $renderer = null)
    {
        return $this->environment
                    ->resolveTemplate($name);
    }
}

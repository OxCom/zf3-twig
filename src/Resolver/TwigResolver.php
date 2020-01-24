<?php
namespace ZendTwig\Resolver;

use Twig\Environment;

use Twig\Template;
use Twig\TemplateWrapper;
use Laminas\View\Resolver\ResolverInterface;
use Laminas\View\Renderer\RendererInterface as Renderer;

class TwigResolver implements ResolverInterface
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * Constructor.
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment = null)
    {
        $this->environment = $environment;
    }

    /**
     * Resolve a template/pattern name to a resource the renderer can consume
     *
     * @param  string        $name
     * @param  null|Renderer $renderer
     *
     * @return TemplateWrapper|Template
     */
    public function resolve($name, Renderer $renderer = null)
    {
        return $this->environment->resolveTemplate($name);
    }
}
